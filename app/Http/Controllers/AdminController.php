<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Absensi;
use App\Models\AttendanceType;
use App\Models\Gedung;
use App\Models\JenisIzin;
use App\Models\PegawaiProfile;
use App\Models\Setting;
use App\Models\SiswaProfile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organization;
        $orgId = $organization->id;

        $siswaUsers = User::with(['siswaProfile.gedung', 'siswaProfile.attendanceType', 'siswaProfile.team'])->where('organization_id', $orgId)->where('role', 'siswa')->where('approval_status', 'approved')->latest()->get();
        $pendingUsers = User::with('requestedTeam')->where('organization_id', $orgId)->where('role', 'siswa')->where('approval_status', 'pending')->oldest('requested_at')->get();
        $pegawaiUsers = User::with('pegawaiProfile.gedung')->where('organization_id', $orgId)->where('role', 'pegawai')->latest()->get();
        $adminUsers = User::where('organization_id', $orgId)->where('role', 'admin')->latest()->get();
        $guruUsers = User::with(['siswaYangDiawasi', 'supervisedTeams'])->where('organization_id', $orgId)->where('role', 'guru')->latest()->get();
        $teams = Team::with(['members.user', 'supervisors'])->where('organization_id', $orgId)->orderBy('name')->get();
        $gedungs = Gedung::where('organization_id', $orgId)->orderBy('nama')->get();
        $allSiswas = SiswaProfile::whereHas('user', fn ($query) => $query->where('organization_id', $orgId))
            ->orderBy('nama_lengkap')->get();
        $jenisIzin = JenisIzin::where('organization_id', $orgId)->orderBy('nama')->get();
        $attendanceTypes = AttendanceType::where('organization_id', $orgId)->where('is_active', true)->orderBy('name')->get();
        $jamTelat = Setting::where('organization_id', $orgId)->where('key', 'jam_telat')->value('value') ?? '08:15';
        $jamPulangMinimal = Setting::where('organization_id', $orgId)->where('key', 'jam_pulang_minimal')->value('value') ?? '17:00';
        $modeAbsensi = Setting::where('organization_id', $orgId)->where('key', 'mode_absensi')->value('value') ?? 'regulated';
        $hadirHariIni = Absensi::whereDate('tanggal', today())
            ->whereHas('siswa.user', fn ($query) => $query->where('organization_id', $orgId))
            ->whereNotNull('jam_masuk')->count();

        $stats = [
            'anggota' => $siswaUsers->count(),
            'tim_admin' => $adminUsers->count() + $pegawaiUsers->count() + $guruUsers->count(),
            'lokasi' => $gedungs->count(),
            'hadir_hari_ini' => $hadirHariIni,
        ];

        return view('admin.dashboard', compact(
            'organization', 'siswaUsers', 'pendingUsers', 'pegawaiUsers', 'allSiswas', 'guruUsers', 'teams',
            'adminUsers', 'gedungs', 'jamTelat', 'jamPulangMinimal', 'modeAbsensi', 'jenisIzin', 'attendanceTypes', 'stats'
        ));
    }

    public function policies(Request $request)
    {
        $organization = $request->user()->organization;
        $orgId = $organization->id;
        $jenisIzin = JenisIzin::where('organization_id', $orgId)->orderBy('nama')->get();
        $attendanceTypes = AttendanceType::where('organization_id', $orgId)->where('is_active', true)->orderBy('name')->get();
        $jamTelat = Setting::where('organization_id', $orgId)->where('key', 'jam_telat')->value('value') ?? '08:15';
        $jamPulangMinimal = Setting::where('organization_id', $orgId)->where('key', 'jam_pulang_minimal')->value('value') ?? '17:00';
        $modeAbsensi = Setting::where('organization_id', $orgId)->where('key', 'mode_absensi')->value('value') ?? 'regulated';

        return view('admin.policies', compact(
            'organization', 'jenisIzin', 'attendanceTypes', 'jamTelat', 'jamPulangMinimal', 'modeAbsensi'
        ));
    }

    public function organizationProfile(Request $request)
    {
        $organization = $request->user()->organization;

        return view('admin.organization', compact('organization'));
    }

    public function storeUser(Request $request)
    {
        $orgId = $request->user()->organization_id;
        $request->merge(['username' => strtolower(trim((string) $request->username))]);
        $rejectedUser = User::where('organization_id', $orgId)
            ->whereRaw('LOWER(username) = ?', [$request->username])
            ->where('approval_status', 'rejected')
            ->first();
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users')->where('organization_id', $orgId)->ignore($rejectedUser?->id)],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['siswa', 'pegawai', 'admin', 'guru'])],
            'nama_lengkap_siswa' => ['required_if:role,siswa', 'nullable', 'string', 'max:255'],
            'asal_pkl' => ['nullable', 'string', 'max:255'],
            'attendance_type_id' => ['required_if:role,siswa', 'nullable', Rule::exists('attendance_types', 'id')->where('organization_id', $orgId)],
            'team_id' => ['nullable', Rule::exists('teams', 'id')->where('organization_id', $orgId)],
            'gedung_id_siswa' => ['nullable', Rule::exists('gedungs', 'id')->where('organization_id', $orgId)],
            'nama_lengkap_pegawai' => ['required_if:role,pegawai', 'nullable', 'string', 'max:255'],
            'gedung_id_pegawai' => ['required_if:role,pegawai', Rule::exists('gedungs', 'id')->where('organization_id', $orgId)],
            'siswa_ids' => ['nullable', 'array'],
            'siswa_ids.*' => [Rule::exists('siswa_profiles', 'id')->where(fn ($query) => $query->whereIn('user_id', User::where('organization_id', $orgId)->select('id')))],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => [Rule::exists('teams', 'id')->where('organization_id', $orgId)],
        ], [
            'username.unique' => 'Username sudah digunakan di perusahaan ini. Silakan pilih username lain.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda minus.',
        ]);

        $organization = $request->user()->organization;
        $maxUsers = $organization->plan_details->max_users ?? 10;
        $currentUsers = User::where('organization_id', $orgId)->where('approval_status', 'approved')->count();
        if ($currentUsers >= $maxUsers) {
            return back()->withErrors(['username' => "Batas maksimal pengguna untuk paket langganan Anda ({$maxUsers} pengguna) telah tercapai. Silakan upgrade paket Anda untuk menambah pengguna baru."]);
        }

        if ($request->role === 'siswa') {
            $this->ensureLocationMatchesAttendanceType($orgId, (int) $request->attendance_type_id, $request->gedung_id_siswa);
        }

        $userData = [
            'organization_id' => $orgId,
            'requested_team_id' => null,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'approval_status' => 'approved',
            'approved_at' => now(),
        ];
        if ($rejectedUser) {
            $rejectedUser->update($userData + ['requested_at' => null]);
            $user = $rejectedUser;
        } else {
            $user = User::create($userData);
        }

        if ($request->role === 'siswa') {
            SiswaProfile::create([
                'user_id' => $user->id,
                'team_id' => $request->team_id,
                'nama_lengkap' => $request->nama_lengkap_siswa,
                'asal_pkl' => $request->asal_pkl ?: '-',
                'gedung_id' => $request->gedung_id_siswa,
                'attendance_type_id' => $request->attendance_type_id,
            ]);
        } elseif ($request->role === 'pegawai') {
            PegawaiProfile::create([
                'user_id' => $user->id,
                'nama_lengkap' => $request->nama_lengkap_pegawai,
                'gedung_id' => $request->gedung_id_pegawai,
            ]);
        } elseif ($request->role === 'guru') {
            $user->supervisedTeams()->sync($request->input('team_ids', []));
        }

        LogHelper::log($request, 'Tambah pengguna', "Menambahkan {$user->username} sebagai {$user->role}");
        return back()->with('success', "Pengguna berhasil dibuat. ID masuk: {$user->login_identity}");
    }

    public function updateUser(Request $request, $id)
    {
        $orgId = $request->user()->organization_id;
        $user = User::where('organization_id', $orgId)->findOrFail($id);
        $request->merge(['username' => strtolower(trim((string) $request->username))]);
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users')->where('organization_id', $orgId)->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'nama_lengkap' => ['required_if:role,siswa,pegawai', 'nullable', 'string', 'max:255'],
            'asal_pkl' => ['nullable', 'string', 'max:255'],
            'attendance_type_id' => ['required_if:role,siswa', 'nullable', Rule::exists('attendance_types', 'id')->where('organization_id', $orgId)],
            'team_id' => ['nullable', Rule::exists('teams', 'id')->where('organization_id', $orgId)],
            'gedung_id' => ['nullable', Rule::exists('gedungs', 'id')->where('organization_id', $orgId)],
            'siswa_ids' => ['nullable', 'array'],
            'siswa_ids.*' => [Rule::exists('siswa_profiles', 'id')->where(fn ($query) => $query->whereIn('user_id', User::where('organization_id', $orgId)->select('id')))],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => [Rule::exists('teams', 'id')->where('organization_id', $orgId)],
        ], [
            'username.unique' => 'Username sudah digunakan di perusahaan ini. Silakan pilih username lain.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda minus.',
        ]);

        if ($user->role === 'siswa') {
            $this->ensureLocationMatchesAttendanceType($orgId, (int) $request->attendance_type_id, $request->gedung_id);
        } elseif ($user->role === 'pegawai' && !$request->filled('gedung_id')) {
            throw ValidationException::withMessages(['gedung_id' => 'Lokasi wajib dipilih untuk operator.']);
        }

        $user->username = $request->username;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($user->role === 'siswa') {
            $user->siswaProfile?->update([
                'nama_lengkap' => $request->nama_lengkap,
                'team_id' => $request->team_id,
                'asal_pkl' => $request->asal_pkl ?: '-',
                'gedung_id' => $request->gedung_id,
                'attendance_type_id' => $request->attendance_type_id,
            ]);
        } elseif ($user->role === 'pegawai') {
            $user->pegawaiProfile?->update([
                'nama_lengkap' => $request->nama_lengkap,
                'gedung_id' => $request->gedung_id,
            ]);
        } elseif ($user->role === 'guru') {
            $user->supervisedTeams()->sync($request->input('team_ids', []));
            $user->siswaYangDiawasi()->sync([]);
        }

        LogHelper::log($request, 'Ubah pengguna', "Memperbarui {$user->username}");
        return back()->with('success', $user->role === 'guru'
            ? 'Data atasan dan anggota tim berhasil diperbarui.'
            : 'Data pengguna berhasil diperbarui.');
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::where('organization_id', $request->user()->organization_id)->findOrFail($id);
        if ($user->is($request->user())) {
            return back()->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }
        $username = $user->username;
        $user->delete();
        LogHelper::log($request, 'Hapus pengguna', "Menghapus {$username}");
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function approveRegistration(Request $request, $id)
    {
        $orgId = $request->user()->organization_id;
        $organization = $request->user()->organization;
        $maxUsers = $organization->plan_details->max_users ?? 10;
        $currentUsers = User::where('organization_id', $orgId)->where('approval_status', 'approved')->count();
        if ($currentUsers >= $maxUsers) {
            return back()->withErrors(['error' => "Batas maksimal pengguna untuk paket langganan Anda ({$maxUsers} pengguna) telah tercapai. Silakan upgrade paket Anda untuk menyetujui pengguna baru."]);
        }

        $user = User::where('organization_id', $orgId)
            ->where('role', 'siswa')
            ->where('approval_status', 'pending')
            ->findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'asal_pkl' => ['nullable', 'string', 'max:255'],
            'attendance_type_id' => ['required', Rule::exists('attendance_types', 'id')->where('organization_id', $orgId)],
            'team_id' => ['nullable', Rule::exists('teams', 'id')->where('organization_id', $orgId)],
            'gedung_id' => ['nullable', Rule::exists('gedungs', 'id')->where('organization_id', $orgId)],
        ]);

        $this->ensureLocationMatchesAttendanceType($orgId, (int) $validated['attendance_type_id'], $validated['gedung_id'] ?? null);

        SiswaProfile::create([
            'user_id' => $user->id,
            'team_id' => $validated['team_id'] ?? null,
            'nama_lengkap' => $validated['nama_lengkap'],
            'asal_pkl' => ($validated['asal_pkl'] ?? null) ?: '-',
            'gedung_id' => $validated['gedung_id'] ?? null,
            'attendance_type_id' => $validated['attendance_type_id'],
        ]);

        $user->update(['approval_status' => 'approved', 'approved_at' => now(), 'requested_team_id' => null]);
        LogHelper::log($request, 'Setujui pendaftaran', "Menyetujui {$user->login_identity}");

        return back()->with('success', "Pendaftaran {$user->login_identity} berhasil disetujui.");
    }

    public function rejectRegistration(Request $request, $id)
    {
        $user = User::where('organization_id', $request->user()->organization_id)
            ->where('role', 'siswa')
            ->where('approval_status', 'pending')
            ->findOrFail($id);

        $user->update(['approval_status' => 'rejected']);
        LogHelper::log($request, 'Tolak pendaftaran', "Menolak {$user->login_identity}");

        return back()->with('success', 'Pendaftaran ditolak.');
    }

    public function storeTeam(Request $request)
    {
        $orgId = $request->user()->organization_id;
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('teams')->where('organization_id', $orgId)],
            'description' => ['nullable', 'string', 'max:255'],
        ], ['name.unique' => 'Nama tim sudah digunakan di perusahaan ini.']);
        Team::create($validated + ['organization_id' => $orgId]);
        LogHelper::log($request, 'Tambah tim', "Menambahkan tim {$validated['name']}");
        return back()->with('success', 'Tim berhasil dibuat.');
    }

    public function destroyTeam(Request $request, $id)
    {
        $team = Team::where('organization_id', $request->user()->organization_id)->findOrFail($id);
        $name = $team->name;
        $team->delete();
        LogHelper::log($request, 'Hapus tim', "Menghapus tim {$name}");
        return back()->with('success', 'Tim berhasil dihapus. Anggota menjadi belum memiliki tim.');
    }

    public function storeGedung(Request $request)
    {
        $data = $this->validateGedung($request);
        $data['organization_id'] = $request->user()->organization_id;
        Gedung::create($data);
        LogHelper::log($request, 'Tambah lokasi', "Menambahkan {$data['nama']}");
        return back()->with('success', 'Lokasi kerja berhasil ditambahkan.');
    }

    public function updateGedung(Request $request, $id)
    {
        $gedung = Gedung::where('organization_id', $request->user()->organization_id)->findOrFail($id);
        $gedung->update($this->validateGedung($request));
        LogHelper::log($request, 'Ubah lokasi', "Memperbarui {$gedung->nama}");
        return back()->with('success', 'Lokasi kerja berhasil diperbarui.');
    }

    public function destroyGedung(Request $request, $id)
    {
        $gedung = Gedung::where('organization_id', $request->user()->organization_id)->findOrFail($id);
        if ($gedung->siswaProfiles()->exists() || $gedung->pegawaiProfiles()->exists()) {
            return back()->with('error', 'Lokasi masih digunakan. Pindahkan penggunanya sebelum menghapus.');
        }
        $nama = $gedung->nama;
        $gedung->delete();
        LogHelper::log($request, 'Hapus lokasi', "Menghapus {$nama}");
        return back()->with('success', 'Lokasi kerja berhasil dihapus.');
    }

    public function updateJamTelat(Request $request)
    {
        $validated = $request->validate([
            'mode_absensi' => ['required', Rule::in(['regulated', 'simple'])],
            'jam_telat' => ['required', 'date_format:H:i'],
            'jam_pulang_minimal' => ['required', 'date_format:H:i'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['organization_id' => $request->user()->organization_id, 'key' => $key],
                ['value' => $value]
            );
        }

        $mode = $validated['mode_absensi'] === 'simple' ? 'Fleksibel' : 'Terjadwal';
        LogHelper::log($request, 'Ubah kebijakan absensi', "Mode {$mode}; masuk {$validated['jam_telat']}; pulang minimal {$validated['jam_pulang_minimal']}");
        return back()->with('success', 'Kebijakan absensi berhasil diperbarui.');
    }

    public function storeAttendanceType(Request $request)
    {
        $orgId = $request->user()->organization_id;
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('attendance_types')->where('organization_id', $orgId)],
            'mode' => ['required', Rule::in(['scheduled', 'flexible', 'report'])],
            'jam_telat' => ['required_if:mode,scheduled', 'nullable', 'date_format:H:i'],
            'jam_pulang_minimal' => ['required_if:mode,scheduled', 'nullable', 'date_format:H:i'],
        ]);
        if ($validated['mode'] !== 'scheduled') {
            $validated['jam_telat'] = null;
            $validated['jam_pulang_minimal'] = null;
        }
        $type = AttendanceType::create($validated + ['organization_id' => $orgId, 'is_active' => true]);
        LogHelper::log($request, 'Tambah tipe absensi', "Menambahkan {$type->name} ({$type->mode})");
        return back()->with('success', 'Tipe absensi berhasil disimpan.');
    }

    public function destroyAttendanceType(Request $request, $id)
    {
        $type = AttendanceType::where('organization_id', $request->user()->organization_id)->findOrFail($id);
        if ($type->members()->exists()) {
            return back()->with('error', 'Tipe absensi masih digunakan anggota dan belum dapat dihapus.');
        }
        $name = $type->name;
        $type->delete();
        LogHelper::log($request, 'Hapus tipe absensi', "Menghapus {$name}");
        return back()->with('success', 'Tipe absensi berhasil dihapus.');
    }

    public function updateGuru(Request $request, $id)
    {
        $orgId = $request->user()->organization_id;
        $guru = User::where('organization_id', $orgId)->where('role', 'guru')->findOrFail($id);
        $request->merge(['username' => strtolower(trim((string) $request->username))]);
        $request->validate([
            'username' => ['required', 'regex:/^[a-zA-Z0-9._-]+$/', Rule::unique('users')->where('organization_id', $orgId)->ignore($guru->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => [Rule::exists('teams', 'id')->where('organization_id', $orgId)],
        ]);
        $guru->username = $request->username;
        if ($request->filled('password')) $guru->password = Hash::make($request->password);
        $guru->save();
        $guru->supervisedTeams()->sync($request->input('team_ids', []));
        $guru->siswaYangDiawasi()->sync([]);
        LogHelper::log($request, 'Atasan diperbarui', "Akun {$guru->username} memantau ".count($request->input('team_ids', [])).' tim.');
        return back()->with('success', 'Akses atasan dan anggota tim berhasil diperbarui.');
    }

    private function validateGedung(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meter' => ['required', 'integer', 'min:5', 'max:5000'],
        ]);
    }

    private function ensureLocationMatchesAttendanceType(int $organizationId, int $attendanceTypeId, $gedungId): void
    {
        $type = AttendanceType::where('organization_id', $organizationId)->findOrFail($attendanceTypeId);
        // Semua aturan merekam GPS aktual. Gedung hanya menjadi titik pembanding opsional.
    }
}
