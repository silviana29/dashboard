<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Plan;
use App\Models\PlanOffer;
use App\Models\SupportMessage;
use App\Models\SupportThread;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupportController extends Controller
{
    public function adminIndex(Request $request)
    {
        $organization = $request->user()->organization;
        $threads = SupportThread::where('organization_id', $organization->id)
            ->with('messages')->latest('last_message_at')->latest()->get();
        $activeThread = $request->integer('thread')
            ? SupportThread::where('organization_id', $organization->id)->with(['messages.user', 'planOffers.plan'])->findOrFail($request->integer('thread'))
            : $threads->first()?->load(['messages.user', 'planOffers.plan']);

        if ($activeThread) {
            $activeThread->messages()->where('sender_role', 'support')->whereNull('read_at')->update(['read_at' => now()]);
        }

        return view('admin.support', [
            'organization' => $organization,
            'threads' => $threads,
            'activeThread' => $activeThread,
            'payments' => $organization->payments()->latest('paid_at')->limit(10)->get(),
        ]);
    }

    public function storeThread(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required','string','max:150'],
            'category' => ['required', Rule::in(['general','technical','billing','consultation'])],
            'priority' => ['required', Rule::in(['normal','high','urgent'])],
            'message' => ['required','string','max:5000'],
        ]);
        $thread = SupportThread::create([
            'organization_id' => $request->user()->organization_id,
            'created_by' => $request->user()->id,
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'last_message_at' => now(),
        ]);
        $thread->messages()->create(['user_id'=>$request->user()->id,'sender_role'=>'admin','body'=>$validated['message']]);
        LogHelper::log($request, 'Percakapan CS dibuat', "Tiket #{$thread->id}: {$thread->subject}");
        return redirect()->route('admin.support', ['thread'=>$thread->id])->with('success', 'Pesan berhasil dikirim ke CS.');
    }

    public function adminReply(Request $request, SupportThread $thread)
    {
        abort_unless($thread->organization_id === $request->user()->organization_id, 403);
        $validated = $request->validate(['message'=>['required','string','max:5000']]);
        $thread->messages()->create(['user_id'=>$request->user()->id,'sender_role'=>'admin','body'=>$validated['message']]);
        $thread->update(['status'=>'open','last_message_at'=>now()]);
        LogHelper::log($request, 'Balasan CS dikirim', "Balasan admin pada tiket #{$thread->id}.");
        return back()->with('success', 'Balasan terkirim.');
    }

    public function superShow(SupportThread $thread)
    {
        $thread->load(['organization','messages.user', 'planOffers.plan', 'planOffers.offeredBy']);
        $thread->messages()->where('sender_role', 'admin')->whereNull('read_at')->update(['read_at'=>now()]);
        $privatePlans = Plan::where('is_private', true)->orderBy('price')->get();
        return view('superadmin.support-thread', compact('thread', 'privatePlans'));
    }

    public function superReply(Request $request, SupportThread $thread)
    {
        $validated = $request->validate(['message'=>['required','string','max:5000']]);
        $thread->messages()->create(['user_id'=>$request->user()->id,'sender_role'=>'support','body'=>$validated['message']]);
        $thread->update(['status'=>'waiting_admin','last_message_at'=>now()]);
        LogHelper::record($request->user(), $thread->organization_id, 'Balasan CS diterima', "CS membalas tiket #{$thread->id}.", $request);
        return back()->with('success', 'Balasan CS terkirim.');
    }

    public function updateStatus(Request $request, SupportThread $thread)
    {
        $validated = $request->validate(['status'=>['required',Rule::in(['open','waiting_admin','resolved','closed'])]]);
        $thread->update($validated);
        LogHelper::record($request->user(), $thread->organization_id, 'Status percakapan diubah', "Tiket #{$thread->id} diubah menjadi {$validated['status']}.", $request);
        return back()->with('success', 'Status percakapan diperbarui.');
    }

    public function storePlanOffer(Request $request, SupportThread $thread)
    {
        $validated = $request->validate([
            'plan_id' => ['required', Rule::exists('plans', 'id')->where(fn ($query) => $query->where('is_private', true)->whereNull('deleted_at'))],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);
        $plan = Plan::where('is_private', true)->findOrFail($validated['plan_id']);

        PlanOffer::where('organization_id', $thread->organization_id)
            ->where('plan_id', $plan->id)->where('status', 'offered')
            ->update(['status' => 'revoked']);

        $offer = PlanOffer::create([
            'organization_id' => $thread->organization_id,
            'plan_id' => $plan->id,
            'support_thread_id' => $thread->id,
            'offered_by' => $request->user()->id,
            'status' => 'offered',
            'offered_at' => now(),
            'expires_at' => $validated['expires_at'] ?? null,
        ]);
        LogHelper::record($request->user(), $thread->organization_id, 'Paket khusus diberikan', "Paket khusus {$plan->name} diberikan melalui tiket #{$thread->id}.", $request);

        return back()->with('success', "Paket khusus {$offer->plan->name} sekarang tersedia di halaman tagihan perusahaan.");
    }

    public function adminBilling(Request $request)
    {
        $organization = $request->user()->organization;
        $payments = $organization->payments()->with('plan')->latest('paid_at')->latest('id')->paginate(10);
        $planOptions = Plan::query()
            ->where(function ($query) use ($organization) {
                $query->where('is_private', false)
                    ->orWhereHas('offers', function ($offers) use ($organization) {
                        $offers->where('organization_id', $organization->id)
                            ->where('status', 'offered')
                            ->where(fn ($expiry) => $expiry->whereNull('expires_at')->orWhere('expires_at', '>', now()));
                    });
            })->orderBy('price')->get();
        $paymentConfigured = app(\App\Services\DuitkuService::class)->isConfigured();
        return view('admin.billing', compact('organization', 'payments', 'planOptions', 'paymentConfigured'));
    }

    public function adminUpgradePlan(Request $request)
    {
        $organization = $request->user()->organization;
        $validated = $request->validate([
            'plan_id' => ['required', Rule::exists('plans', 'id')->whereNull('deleted_at')],
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        if ($plan->is_private && ! PlanOffer::where('organization_id', $organization->id)->where('plan_id', $plan->id)->where('status', 'offered')->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))->exists()) {
            abort(403, 'Paket khusus ini belum diberikan kepada workspace Anda.');
        }

        // Create a support thread automatically
        $thread = SupportThread::create([
            'organization_id' => $organization->id,
            'created_by' => $request->user()->id,
            'subject' => 'Permintaan Upgrade Paket ke ' . $plan->name,
            'category' => 'billing',
            'priority' => 'high',
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        $thread->messages()->create([
            'user_id' => $request->user()->id,
            'sender_role' => 'admin',
            'body' => "Halo CS MWork,\n\nKami mengajukan permintaan upgrade paket langganan untuk organisasi kami:\n- Nama Organisasi: {$organization->name}\n- Paket yang diajukan: {$plan->name} (Rp " . number_format($plan->price, 0, ',', '.') . ")\n- Batas Pengguna Baru: {$plan->max_users} pengguna\n\nMohon petunjuk pembayaran dan pemrosesan selanjutnya. Terima kasih!"
        ]);
        LogHelper::log($request, 'Upgrade paket diajukan', "Mengajukan paket {$plan->name} melalui tiket #{$thread->id}.");

        return redirect()->route('admin.support', ['thread' => $thread->id])->with('success', 'Permintaan upgrade paket diajukan. Silakan konfirmasi pembayaran Anda di bawah ini.');
    }
}
