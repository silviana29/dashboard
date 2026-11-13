<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(fn () => app(\App\Services\SubscriptionLifecycleService::class)->process())
            ->dailyAt('08:00')
            ->name('subscription-lifecycle')
            ->withoutOverlapping();

        $schedule->call(function () {
            $organizations = \App\Models\Organization::all();
            foreach ($organizations as $org) {
                $retentionMonths = $org->plan_details->retention_months;
                if ($retentionMonths !== null) {
                    $cutoffDate = now()->subMonths($retentionMonths)->toDateString();
                    \App\Models\Absensi::whereHas('siswa.user', function ($query) use ($org) {
                        $query->where('organization_id', $org->id);
                    })->where('tanggal', '<', $cutoffDate)->delete();
                }
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
