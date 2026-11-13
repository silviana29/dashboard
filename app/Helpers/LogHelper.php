<?php

namespace App\Helpers;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Throwable;
use App\Models\User;

class LogHelper
{
    public static function log(Request $request, $aksi, $keterangan = null)
    {
        self::record(Auth::user(), Auth::user()?->organization_id, $aksi, $keterangan, $request);
    }

    public static function record(?User $actor, ?int $organizationId, string $aksi, ?string $keterangan = null, ?Request $request = null): void
    {
        try {
            LogActivity::create([
                'organization_id' => $organizationId,
                'user' => $actor?->username ?? 'system',
                'role'       => $actor?->role ?? 'system',
                'aksi'       => $aksi,
                'keterangan' => $keterangan,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
            ]);
        } catch (Throwable $exception) {
            // Audit tidak boleh membuat login, absensi, atau halaman utama gagal total.
            report($exception);
        }
    }
}
