<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\JenisIzin;
use App\Models\AttendanceType;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganizationController extends Controller
{
    public function create()
    {
        return view('auth.register-organization');
    }

    public function store(Request $request)
    {
        $request->merge([
            'name' => trim((string) $request->name),
            'slug' => Str::slug((string) ($request->slug ?: $request->name)),
            'username' => strtolower(trim((string) $request->username)),
        ]);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', function ($attribute, $value, $fail) {
                if (Organization::whereRaw('LOWER(name) = ?', [mb_strtolower(trim($value))])->exists()) {
                    $fail('Nama perusahaan/workspace sudah digunakan. Silakan gunakan nama lain.');
                }
            }],
            'slug' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', function ($attribute, $value, $fail) {
                if (Organization::whereRaw('LOWER(slug) = ?', [mb_strtolower($value)])->exists()) {
                    $fail('Slug perusahaan sudah digunakan. Silakan pilih slug lain.');
                }
            }],
            'type' => ['required', Rule::in(['company', 'small_business', 'school', 'government', 'community'])],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda minus.',
            'username.regex' => 'Username admin hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda minus.',
        ]);

        [$organization, $admin] = DB::transaction(function () use ($validated) {
            $organization = Organization::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'company_code' => $this->uniqueCompanyCode(),
                'type' => $validated['type'],
                'timezone' => 'Asia/Jakarta',
                'primary_color' => '#4f46e5',
                'is_active' => true,
            ]);

            $admin = User::create([
                'organization_id' => $organization->id,
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => 'admin',
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]);

            foreach ([
                'jam_telat' => '08:15',
                'jam_pulang_minimal' => '17:00',
                'mode_absensi' => 'regulated',
            ] as $key => $value) {
                Setting::create([
                    'organization_id' => $organization->id,
                    'key' => $key,
                    'value' => $value,
                ]);
            }

            foreach (['Sakit', 'Cuti', 'Keperluan keluarga'] as $nama) {
                JenisIzin::create([
                    'organization_id' => $organization->id,
                    'nama' => $nama,
                ]);
            }

            foreach ([['Terjadwal', 'scheduled'], ['Fleksibel', 'flexible'], ['Lapor Harian', 'report']] as [$name, $mode]) {
                AttendanceType::create([
                    'organization_id' => $organization->id,
                    'name' => $name,
                    'mode' => $mode,
                    'jam_telat' => $mode === 'scheduled' ? '08:15' : null,
                    'jam_pulang_minimal' => $mode === 'scheduled' ? '17:00' : null,
                    'is_active' => true,
                ]);
            }

            return [$organization, $admin];
        });

        Auth::login($admin, true);
        $request->session()->regenerate();
        LogHelper::record($admin, $organization->id, 'Workspace dibuat', "Workspace {$organization->name} dibuat dengan kode perusahaan {$organization->company_code}.", $request);

        return redirect()->route('admin.dashboard')
            ->with('onboarding', true)
            ->with('success', "Workspace {$organization->name} berhasil dibuat. Kode perusahaan Anda: {$organization->company_code}");
    }

    public function slugAvailability(Request $request)
    {
        $slug = Str::slug((string) $request->query('slug'));

        if (strlen($slug) < 3) {
            return response()->json([
                'available' => null,
                'slug' => $slug,
                'message' => 'Slug minimal 3 karakter.',
            ]);
        }

        $available = ! Organization::whereRaw('LOWER(slug) = ?', [mb_strtolower($slug)])->exists();

        return response()->json([
            'available' => $available,
            'slug' => $slug,
            'message' => $available
                ? 'Slug tersedia dan dapat digunakan.'
                : 'Slug sudah digunakan perusahaan lain. Silakan pilih slug berbeda.',
        ]);
    }

    public function update(Request $request)
    {
        $organization = $request->user()->organization;
        $request->merge(['name' => trim((string) $request->name)]);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', function ($attribute, $value, $fail) use ($organization) {
                if (Organization::where('id', '!=', $organization->id)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($value))])->exists()) {
                    $fail('Nama perusahaan/workspace sudah digunakan. Silakan gunakan nama lain.');
                }
            }],
            'type' => ['required', Rule::in(['company', 'small_business', 'school', 'government', 'community'])],
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $before = $organization->only(['name', 'type', 'primary_color']);
        $organization->update($validated);
        LogHelper::log($request, 'Profil organisasi diubah', 'Perubahan: '.collect($validated)->filter(fn ($value, $key) => ($before[$key] ?? null) !== $value)->map(fn ($value, $key) => "{$key}={$value}")->implode(', '));

        return back()->with('success', 'Identitas organisasi berhasil diperbarui.');
    }

    private function uniqueCompanyCode(): string
    {
        do {
            $code = 'MM-' . Str::upper(Str::random(8));
        } while (Organization::where('company_code', $code)->exists());
        return $code;
    }
}
