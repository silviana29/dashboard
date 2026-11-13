<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\PlanOffer;
use App\Services\DuitkuService;
use App\Services\SubscriptionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class DuitkuPaymentController extends Controller
{
    public function checkout(Request $request, DuitkuService $gateway)
    {
        if (! $gateway->isConfigured()) {
            return back()->withInput()->with('error', 'Sistem pembayaran online belum dikonfigurasi.');
        }

        $payment = $this->createPendingPayment($request, $this->validatedCheckout($request));
        LogHelper::log($request, 'Pembayaran paket dimulai', "Invoice {$payment->invoice_number} untuk paket {$payment->plan?->name} dibuat.");
        return $this->openPaymentPage($payment, $gateway);
    }

    public function proposal(Request $request)
    {
        $payment = $this->createPendingPayment($request, $this->validatedCheckout($request));
        LogHelper::log($request, 'Invoice penawaran dibuat', "Invoice {$payment->invoice_number} untuk paket {$payment->plan?->name} diunduh.");
        return $this->downloadInvoice($request, $payment);
    }

    public function pay(Request $request, Payment $payment, DuitkuService $gateway)
    {
        $this->authorizePayment($request, $payment);
        abort_if($payment->status === 'paid', 422, 'Invoice ini sudah dibayar.');

        if (! $gateway->isConfigured()) {
            return back()->with('error', 'Sistem pembayaran online belum dikonfigurasi.');
        }

        LogHelper::log($request, 'Pembayaran invoice dilanjutkan', "Membuka pembayaran invoice {$payment->invoice_number}.");
        return $this->openPaymentPage($payment, $gateway);
    }

    public function invoice(Request $request, Payment $payment)
    {
        $this->authorizePayment($request, $payment);
        if (! $payment->invoice_number) {
            $payment->update([
                'invoice_number' => sprintf('INV-%s-%04d-%06d', ($payment->created_at ?? now())->format('Ym'), $payment->organization_id, $payment->id),
                'invoice_issued_at' => $payment->created_at ?? now(),
            ]);
        }
        LogHelper::log($request, 'Invoice diunduh', "Mengunduh invoice {$payment->invoice_number}.");
        return $this->downloadInvoice($request, $payment);
    }

    public function callback(Request $request, DuitkuService $gateway, SubscriptionService $subscriptions)
    {
        if (! $gateway->isConfigured()) {
            return response('Payment gateway is not configured', 503);
        }

        $merchantCode = (string) $request->input('merchantCode');
        $amount = (string) $request->input('amount');
        $merchantOrderId = (string) $request->input('merchantOrderId');
        $signature = (string) $request->input('signature');

        if (blank($merchantCode) || blank($amount) || blank($merchantOrderId) || blank($signature)) {
            return response('Invalid callback data', 400);
        }

        if (! hash_equals($gateway->merchantCode(), $merchantCode)
            || ! hash_equals($gateway->callbackSignature($amount, $merchantOrderId), $signature)) {
            return response('Invalid signature', 403);
        }

        $payment = Payment::where('merchant_order_id', $merchantOrderId)->first();
        if (! $payment || (int) round((float) $payment->amount) !== (int) round((float) $amount)) {
            return response('Payment not found or amount mismatch', 404);
        }

        DB::transaction(function () use ($payment, $request, $subscriptions) {
            $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            if ($lockedPayment->status === 'paid') {
                return;
            }

            $callbackData = $request->only([
                'merchantCode', 'amount', 'merchantOrderId', 'reference', 'resultCode',
                'paymentCode', 'publisherOrderId', 'settlementDate',
            ]);

            if ((string) $request->input('resultCode') === '00') {
                $subscriptions->activate($lockedPayment, $callbackData);
            } else {
                $lockedPayment->update(['status' => 'failed', 'gateway_payload' => $callbackData]);
                LogHelper::record(null, $lockedPayment->organization_id, 'Pembayaran gagal', "Transaksi {$lockedPayment->merchant_order_id} ditolak oleh sistem pembayaran.", $request);
            }
        });

        return response('SUCCESS', 200);
    }

    public function returnFromGateway(Request $request)
    {
        $payment = Payment::where('merchant_order_id', (string) $request->query('merchantOrderId'))->first();

        if (Auth::check() && Auth::user()->role === 'admin' && $payment?->organization_id === Auth::user()->organization_id) {
            $message = $payment->status === 'paid'
                ? 'Pembayaran berhasil dan paket sudah aktif.'
                : 'Pembayaran sedang diverifikasi. Gunakan tombol Cek status bila status belum berubah.';

            return redirect()->route('admin.billing')->with($payment->status === 'paid' ? 'success' : 'info', $message);
        }

        return view('payments.duitku-return', compact('payment'));
    }

    public function sync(Request $request, Payment $payment, DuitkuService $gateway, SubscriptionService $subscriptions)
    {
        $this->authorizePayment($request, $payment);
        abort_unless($payment->gateway === 'duitku', 422, 'Invoice belum memiliki transaksi pembayaran online.');

        if ($payment->status === 'paid') {
            return back()->with('success', 'Pembayaran sudah berhasil dan paket telah aktif.');
        }

        try {
            $status = $gateway->transactionStatus($payment);
            if (isset($status['amount']) && (int) round((float) $status['amount']) !== (int) round((float) $payment->amount)) {
                throw new RuntimeException('Nominal transaksi tidak sesuai dengan invoice.');
            }

            DB::transaction(function () use ($payment, $status, $subscriptions) {
                $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
                if ($lockedPayment->status === 'paid') {
                    return;
                }
                if ((string) $status['statusCode'] === '00') {
                    $subscriptions->activate($lockedPayment, $status);
                } elseif ((string) $status['statusCode'] === '02') {
                    $lockedPayment->update(['status' => 'cancelled', 'gateway_payload' => $status]);
                } else {
                    $lockedPayment->update(['status' => 'pending', 'gateway_payload' => $status]);
                }
            });

            $payment->refresh();
            LogHelper::log($request, 'Status pembayaran diperiksa', "Status invoice {$payment->invoice_number}: {$payment->status}.");
            return back()->with($payment->status === 'paid' ? 'success' : 'info', match ($payment->status) {
                'paid' => 'Pembayaran berhasil diverifikasi. Paket sudah aktif.',
                'cancelled' => 'Transaksi telah dibatalkan atau kedaluwarsa.',
                default => 'Pembayaran masih menunggu penyelesaian.',
            });
        } catch (Throwable $exception) {
            report($exception);
            return back()->with('error', 'Status pembayaran belum dapat diperiksa. Silakan coba kembali beberapa saat lagi.');
        }
    }

    protected function validatedCheckout(Request $request): array
    {
        return $request->validate([
            'plan_id' => ['required', Rule::exists('plans', 'id')->whereNull('deleted_at')],
            'email' => ['required', 'string', 'max:255', 'regex:/\A[^\r\n]+\z/', 'email:rfc'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
    }

    protected function createPendingPayment(Request $request, array $validated): Payment
    {
        $organization = $request->user()->organization;
        $plan = Plan::findOrFail($validated['plan_id']);
        $offer = null;
        if ($plan->is_private) {
            $offer = PlanOffer::where('organization_id', $organization->id)
                ->where('plan_id', $plan->id)
                ->where('status', 'offered')
                ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->latest('offered_at')->first();
            abort_unless($offer, 403, 'Paket khusus ini tidak tersedia untuk workspace Anda.');
        }
        $price = (int) round((float) $plan->price);

        if ($price < 1) {
            abort(422, 'Paket gratis tidak memerlukan invoice pembayaran.');
        }

        $extendsCurrentPlan = $organization->plan_id === $plan->id
            && $organization->subscription_status === 'active'
            && $organization->subscription_ends_at?->greaterThanOrEqualTo(today());
        $periodStart = $extendsCurrentPlan
            ? $organization->subscription_ends_at->copy()->addDay()->startOfDay()
            : today();

        $payment = Payment::create([
            'organization_id' => $organization->id,
            'plan_id' => $plan->id,
            'plan_offer_id' => $offer?->id,
            'recorded_by' => $request->user()->id,
            'amount' => $price,
            'status' => 'pending',
            'gateway' => 'invoice',
            'merchant_order_id' => $this->newOrderId($organization->id),
            'invoice_issued_at' => now(),
            'duration_months' => $plan->duration_unit === 'months' ? $plan->duration_value : 1,
            'duration_value' => $plan->duration_value,
            'duration_unit' => $plan->duration_unit,
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'] ?? null,
            'period_start' => $periodStart,
            'period_end' => $plan->periodEnd($periodStart),
            'notes' => "Invoice paket {$plan->name} untuk masa aktif {$plan->duration_label}.",
        ]);

        $payment->update(['invoice_number' => sprintf('INV-%s-%04d-%06d', now()->format('Ym'), $organization->id, $payment->id)]);
        return $payment->fresh(['organization', 'plan']);
    }

    protected function openPaymentPage(Payment $payment, DuitkuService $gateway)
    {
        if ($payment->payment_url && $payment->expires_at?->isFuture()) {
            if ($payment->status !== 'pending') {
                $payment->update(['status' => 'pending']);
            }
            return redirect()->away($payment->payment_url);
        }

        if ($payment->expires_at?->isPast()) {
            $payment->update([
                'merchant_order_id' => $this->newOrderId($payment->organization_id),
                'payment_url' => null,
                'gateway_reference' => null,
                'reference' => null,
                'gateway_payload' => null,
            ]);
        }

        $payment->update([
            'status' => 'pending',
            'gateway' => 'duitku',
            'method' => null,
            'expires_at' => now()->addMinutes((int) config('services.duitku.expiry_period', 60)),
        ]);

        try {
            $invoice = $gateway->createInvoice($payment->fresh());
            $payment->update([
                'gateway_reference' => $invoice['reference'] ?? null,
                'reference' => $invoice['reference'] ?? null,
                'payment_url' => $invoice['paymentUrl'],
                'gateway_payload' => array_filter([
                    'merchantCode' => $invoice['merchantCode'] ?? null,
                    'reference' => $invoice['reference'] ?? null,
                    'statusCode' => $invoice['statusCode'] ?? null,
                    'statusMessage' => $invoice['statusMessage'] ?? null,
                ], fn ($value) => $value !== null && $value !== ''),
            ]);

            return redirect()->away($invoice['paymentUrl']);
        } catch (Throwable $exception) {
            report($exception);
            return redirect()->route('admin.billing')->with('error', 'Halaman pembayaran belum dapat dibuat. Invoice tetap tersimpan dan dapat dicoba kembali.');
        }
    }

    protected function downloadInvoice(Request $request, Payment $payment)
    {
        $payment->loadMissing(['organization', 'plan']);
        $pdf = Pdf::loadView('payments.invoice-pdf', [
            'payment' => $payment,
            'organization' => $payment->organization,
            'plan' => $payment->plan,
        ])->setPaper('a4', 'landscape');

        return $pdf->download(strtolower($payment->invoice_number ?: 'invoice-mwork').'.pdf');
    }

    protected function authorizePayment(Request $request, Payment $payment): void
    {
        abort_unless($payment->organization_id === $request->user()->organization_id, 403);
    }

    protected function newOrderId(int $organizationId): string
    {
        return sprintf('MW-%d-%s-%s', $organizationId, now()->format('ymdHis'), Str::upper(Str::random(6)));
    }
}
