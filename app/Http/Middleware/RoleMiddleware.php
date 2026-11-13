<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
public function handle($request, Closure $next, ...$roles)
{
    if (!auth()->check()) {
        return redirect('/login');
    }

    if (auth()->user()->role !== 'superadmin' && !auth()->user()->organization?->is_active) {
        $reason = auth()->user()->organization?->suspended_reason;
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->withErrors([
            'identity' => 'Workspace sedang ditangguhkan oleh MWork.'.($reason ? ' Alasan: '.$reason : ''),
        ]);
    }

    if (!in_array(auth()->user()->role, ['admin', 'superadmin'], true) && auth()->user()->is_billing_suspended) {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->withErrors([
            'identity' => 'Akun anggota sedang dinonaktifkan karena masa aktif paket workspace telah berakhir.',
        ]);
    }

    if (in_array(auth()->user()->role, $roles, true)) {
        return $next($request);
    }

    abort(403, 'Akun Anda tidak memiliki izin untuk membuka halaman ini.');
}

}
