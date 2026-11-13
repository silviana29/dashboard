<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberRegistrationController extends Controller
{
    public function choice()
    {
        return view('auth.register-choice');
    }

    public function create(Request $request)
    {
        $organization = $request->route('organization');
        if (is_string($organization)) {
            $organization = Organization::where('company_code', strtoupper($organization))
                ->where('is_active', true)
                ->firstOrFail();
        }
        $organization?->load('teams');

        return view('auth.register-member', compact('organization'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'organization_code' => strtoupper(trim((string) $request->organization_code)),
            'username' => strtolower(trim((string) $request->username)),
        ]);

        $request->validate([
            'organization_code' => ['required', 'string'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda minus.',
        ]);

        $organization = Organization::where('company_code', $request->organization_code)
            ->where('is_active', true)
            ->first();

        if (!$organization) {
            return back()->withInput($request->only('organization_code', 'username'))->withErrors([
                'organization_code' => 'Kode perusahaan tidak ditemukan atau sedang nonaktif.',
            ]);
        }

        $maxUsers = $organization->plan_details->max_users ?? 10;
        $currentUsers = User::where('organization_id', $organization->id)->where('approval_status', 'approved')->count();
        if ($currentUsers >= $maxUsers) {
            return back()->withInput($request->only('organization_code', 'username'))->withErrors([
                'username' => "Batas pendaftaran pengguna untuk organisasi ini ({$maxUsers} pengguna) telah terpenuhi. Silakan hubungi administrator organisasi Anda.",
            ]);
        }

        $teamId = $request->input('team_id');
        if ($teamId && !$organization->teams()->whereKey($teamId)->exists()) {
            return back()->withInput($request->only('organization_code', 'username'))->withErrors([
                'team_id' => 'Tim tidak tersedia di perusahaan yang dipilih.',
            ]);
        }

        $existingUser = User::where('organization_id', $organization->id)
            ->whereRaw('LOWER(username) = ?', [$request->username])
            ->first();

        if ($existingUser && $existingUser->approval_status !== 'rejected') {
            return back()->withInput($request->only('organization_code', 'username'))->withErrors([
                'username' => $existingUser->approval_status === 'pending'
                    ? 'Username ini sudah terdaftar dan masih menunggu persetujuan admin.'
                    : 'Username sudah digunakan di perusahaan ini.',
            ]);
        }

        if ($existingUser) {
            $existingUser->update([
                'password' => Hash::make($request->password),
                'requested_team_id' => $teamId ?: null,
                'approval_status' => 'pending',
                'requested_at' => now(),
                'approved_at' => null,
            ]);
        } else {
            $existingUser = User::create([
                'organization_id' => $organization->id,
                'requested_team_id' => $teamId ?: null,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'siswa',
                'approval_status' => 'pending',
                'requested_at' => now(),
            ]);
        }

        LogHelper::record($existingUser, $organization->id, 'Pendaftaran anggota diajukan', "{$existingUser->username} mendaftar dan menunggu persetujuan admin.", $request);

        return redirect()->route('login.organization', $organization)->with(
            'success',
            'Pendaftaran berhasil dikirim. Akun Anda dapat digunakan setelah disetujui admin perusahaan.'
        );
    }

    public function usernameAvailability(Request $request)
    {
        $organizationCode = strtoupper(trim((string) $request->query('organization_code')));
        $username = strtolower(trim((string) $request->query('username')));
        $organization = Organization::where('company_code', $organizationCode)
            ->where('is_active', true)
            ->first();

        if (!$organization) {
            return response()->json(['available' => null, 'teams' => []]);
        }

        $teams = $organization->teams()->orderBy('name')->get(['id', 'name']);
        if (strlen($username) < 3) {
            return response()->json(['available' => null, 'teams' => $teams]);
        }

        $existingUser = User::where('organization_id', $organization->id)
            ->whereRaw('LOWER(username) = ?', [$username])
            ->first();
        $available = !$existingUser || $existingUser->approval_status === 'rejected';

        return response()->json([
            'available' => $available,
            'message' => $available
                ? "Username tersedia di {$organization->name}."
                : "Username sudah digunakan di {$organization->name}. Silakan pilih username lain.",
            'teams' => $teams,
        ]);
    }
}
