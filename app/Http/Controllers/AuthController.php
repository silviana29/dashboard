<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $organization = $request->route('organization');
        if (is_string($organization)) {
            $organization = Organization::where('slug', strtolower($organization))
                ->where('is_active', true)
                ->firstOrFail();
        }

        return view('auth.login', compact('organization'));
    }

    public function showInstall()
    {
        return view('auth.install');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'identity' => ['required', 'string', 'max:320', 'regex:/^[^@\s]+@[a-z0-9_-]+(\.mm)?$/i'],
            'password' => 'required',
            'remember' => 'nullable|boolean',
        ], [
            'identity.regex' => 'Format ID masuk harus seperti nama@perusahaan atau nama@perusahaan.mm.',
        ]);

        $remember = $request->boolean('remember');
        $identity = trim($validated['identity']);
        $hasMm = str_ends_with(strtolower($identity), '.mm');

        if ($hasMm) {
            [$username, $organizationSlug] = explode('@', substr($identity, 0, -3), 2);
        } else {
            [$username, $organizationSlug] = explode('@', $identity, 2);
        }
        $username = strtolower(trim($username));

        $organization = Organization::where('slug', strtolower($organizationSlug))
            ->where('is_active', true)
            ->first();

        if (!$organization) {
            return back()->withInput($request->only('identity'))->withErrors([
                'identity' => 'ID masuk atau password tidak sesuai.',
            ]);
        }

        if (!$organization->plan_details->has_custom_suffix && !$hasMm) {
            return back()->withInput($request->only('identity'))->withErrors([
                'identity' => "Format ID masuk untuk organisasi Anda harus menggunakan .mm (misal: {$username}@{$organization->slug}.mm).",
            ]);
        }

        $credentials = [
            'organization_id' => $organization->id,
            'username' => $username,
            'password' => $validated['password'],
        ];

        $candidate = User::where('organization_id', $organization->id)->where('username', $username)->first();
        if ($candidate && Hash::check($validated['password'], $candidate->password) && $candidate->approval_status !== 'approved') {
            $message = $candidate->approval_status === 'pending'
                ? 'Akun Anda masih menunggu persetujuan admin perusahaan.'
                : 'Pendaftaran akun Anda belum disetujui. Hubungi admin perusahaan.';
            return back()->withInput($request->only('identity'))->withErrors(['identity' => $message]);
        }

        if ($candidate && Hash::check($validated['password'], $candidate->password) && $candidate->is_billing_suspended) {
            return back()->withInput($request->only('identity'))->withErrors([
                'identity' => 'Akun anggota sedang dinonaktifkan karena masa aktif paket workspace telah berakhir. Hubungi admin workspace.',
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            LogHelper::log($request, 'Login', 'User berhasil login');


            $role = Auth::user()->role;
            return match ($role) {
                'superadmin' => redirect('/superadmin/dashboard'),
                'admin' => redirect('/admin/dashboard'),
                'pegawai' => redirect('/pegawai/rekap-absensi'),
                'siswa' => redirect('/siswa/absen'),
                'guru' => redirect('/guru/rekap-absensi'),
                default => redirect('/'),
            };
        }

        return back()->withInput($request->only('identity'))->withErrors([
            'identity' => 'ID masuk atau password tidak sesuai.',
        ]);
    }


    public function logout(Request $request)
    {
        LogHelper::log($request, 'Logout', 'User logout dari sistem');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
