<?php

namespace App\Services;

use App\Helpers\LogHelper;
use App\Models\Payment;
use Illuminate\Support\Carbon;

class SubscriptionService
{
    public function activate(Payment $payment, array $gatewayData = []): void
    {
        $payment->loadMissing(['organization', 'plan']);

        if ($payment->status === 'paid') {
            return;
        }

        $method = $gatewayData['paymentCode'] ?? $payment->method ?? 'Pembayaran online';
        $reference = $gatewayData['reference'] ?? $payment->gateway_reference ?? $payment->reference;
        $periodStart = $payment->period_start?->copy() ?? today();

        if ($payment->organization->plan_id === $payment->plan_id
            && $payment->organization->subscription_status === 'active'
            && $payment->organization->subscription_ends_at?->greaterThanOrEqualTo(today())
            && $payment->organization->subscription_ends_at->greaterThanOrEqualTo($periodStart)) {
            $periodStart = $payment->organization->subscription_ends_at->copy()->addDay()->startOfDay();
        } elseif ($periodStart->isBefore(today())) {
            $periodStart = Carbon::today();
        }
        $duration = max(1, (int) $payment->duration_value);
        $periodEnd = $payment->duration_unit === 'days'
            ? $periodStart->copy()->addDays($duration)->subDay()
            : $periodStart->copy()->addMonthsNoOverflow($duration)->subDay();

        $payment->update([
            'status' => 'paid',
            'method' => $method,
            'reference' => $reference,
            'gateway_reference' => $reference,
            'paid_at' => now(),
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'gateway_payload' => array_filter([
                'merchantCode' => $gatewayData['merchantCode'] ?? null,
                'merchantOrderId' => $gatewayData['merchantOrderId'] ?? $payment->merchant_order_id,
                'reference' => $reference,
                'resultCode' => $gatewayData['resultCode'] ?? null,
                'statusCode' => $gatewayData['statusCode'] ?? null,
                'paymentCode' => $gatewayData['paymentCode'] ?? null,
                'publisherOrderId' => $gatewayData['publisherOrderId'] ?? null,
                'settlementDate' => $gatewayData['settlementDate'] ?? null,
            ], fn ($value) => $value !== null && $value !== ''),
        ]);

        $payment->organization->update([
            'plan_id' => $payment->plan_id,
            'plan' => $payment->plan->name,
            'subscription_status' => 'active',
            'subscription_ends_at' => $periodEnd->copy()->endOfDay(),
            'subscription_reminder_sent_at' => null,
        ]);

        $payment->organization->users()->whereNotIn('role', ['admin', 'superadmin'])->update(['is_billing_suspended' => false]);

        if ($payment->plan_offer_id) {
            $payment->planOffer()->where('status', 'offered')->update(['status' => 'paid', 'paid_at' => now()]);
        }

        LogHelper::record(null, $payment->organization_id, 'Paket langganan aktif', "Pembayaran {$payment->invoice_number} berhasil. Paket {$payment->plan->name} aktif sampai {$periodEnd->format('d-m-Y')}.");
    }
}
