<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Plan;
use App\Models\PlanOffer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SubscriptionLifecycleService
{
    public function process(): void
    {
        PlanOffer::where('status', 'offered')->whereNotNull('expires_at')->where('expires_at', '<=', now())->update(['status' => 'expired']);

        $freePlan = Plan::where('price', 0)->orderBy('id')->first();
        if (! $freePlan) {
            report(new \RuntimeException('Paket Free tidak ditemukan; langganan kedaluwarsa tidak dapat diproses.'));
            return;
        }

        Organization::query()
            ->where('subscription_status', 'active')
            ->whereNotNull('subscription_ends_at')
            ->with('subscriptionPlan')
            ->orderBy('id')
            ->chunkById(100, function ($organizations) use ($freePlan) {
                foreach ($organizations as $organization) {
                    if ((float) ($organization->subscriptionPlan?->price ?? 0) <= 0) {
                        continue;
                    }

                    if ($organization->subscription_ends_at->isPast()) {
                        $this->expire($organization, $freePlan);
                        continue;
                    }

                    $reminderDays = max(1, (int) config('subscriptions.reminder_days', 7));
                    if (! $organization->subscription_reminder_sent_at
                        && $organization->subscription_ends_at->lessThanOrEqualTo(now()->addDays($reminderDays))) {
                        $this->sendReminder($organization, $reminderDays);
                    }
                }
            });
    }

    protected function expire(Organization $organization, Plan $freePlan): void
    {
        DB::transaction(function () use ($organization, $freePlan) {
            $locked = Organization::whereKey($organization->id)->lockForUpdate()->firstOrFail();
            if ($locked->subscription_status !== 'active' || ! $locked->subscription_ends_at?->isPast()) {
                return;
            }

            $locked->update([
                'plan_id' => $freePlan->id,
                'plan' => $freePlan->name,
                'subscription_status' => 'expired',
                'subscription_reminder_sent_at' => null,
            ]);

            $locked->users()->whereNotIn('role', ['admin', 'superadmin'])->update(['is_billing_suspended' => true]);
        });
    }

    protected function sendReminder(Organization $organization, int $reminderDays): void
    {
        $payment = $organization->payments()
            ->where('status', 'paid')
            ->whereNotNull('customer_email')
            ->latest('paid_at')
            ->latest('id')
            ->first();

        if (! $payment?->customer_email) {
            return;
        }

        try {
            Mail::send('emails.subscription-expiring', [
                'organization' => $organization,
                'plan' => $organization->subscriptionPlan,
                'reminderDays' => $reminderDays,
            ], function ($message) use ($payment, $organization) {
                $message->to($payment->customer_email)
                    ->subject('Pengingat masa aktif MWork - '.$organization->name);
            });
            $organization->update(['subscription_reminder_sent_at' => now()]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
