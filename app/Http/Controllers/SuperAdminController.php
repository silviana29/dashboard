<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Absensi;
use App\Models\Gedung;
use App\Models\LogActivity;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\SupportThread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    public function index(Request $request)
    {
        $section = $request->route('section', 'overview');
        abort_unless(in_array($section, ['overview','organizations','users','billing','support','audit'], true), 404);
        $search = trim((string) $request->input('search'));
        $organizationId = $request->integer('organization');

        $organizations = Organization::query()
            ->withCount(['users', 'gedungs'])
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $selectedOrganization = $organizationId
            ? Organization::withCount(['users', 'gedungs'])->findOrFail($organizationId)
            : null;

        $organizationOptions = Organization::query()->orderBy('name')->get(['id', 'name']);

        $userQuery = User::query()->with(['organization', 'siswaProfile.gedung', 'pegawaiProfile.gedung']);
        $logQuery = LogActivity::query()->with('organization');
        $attendanceQuery = Absensi::query()->with(['siswa.user.organization', 'siswa.gedung']);
        $paymentQuery = Payment::query()->with('organization');
        $supportQuery = SupportThread::query()->with(['organization', 'creator'])->withCount('messages');

        if ($selectedOrganization) {
            $userQuery->where('organization_id', $selectedOrganization->id);
            $logQuery->where('organization_id', $selectedOrganization->id);
            $attendanceQuery->whereHas('siswa.user', fn ($query) => $query->where('organization_id', $selectedOrganization->id));
            $paymentQuery->where('organization_id', $selectedOrganization->id);
            $supportQuery->where('organization_id', $selectedOrganization->id);
        } else {
            // Audit log sengaja ditampilkan per organisasi agar konteks tenant
            // tidak tercampur. Superadmin memilih organisasi terlebih dahulu.
            $logQuery->whereRaw('1 = 0');
        }

        $stats = [
            'organizations' => Organization::count(),
            'users' => User::where('role', '!=', 'superadmin')->count(),
            'locations' => Gedung::count(),
            'attendance_today' => Absensi::whereDate('tanggal', today())->whereNotNull('jam_masuk')->count(),
            'revenue' => Payment::where('status', 'paid')->sum('amount'),
            'open_support' => SupportThread::whereIn('status', ['open','waiting_admin'])->count(),
        ];

        $roleCounts = User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $planOptions = Plan::orderBy('price')->get();

        return view('superadmin.dashboard', [
            'organizations' => $organizations,
            'organizationOptions' => $organizationOptions,
            'selectedOrganization' => $selectedOrganization,
            'users' => $userQuery->latest()->limit(20)->get(),
            'logs' => $logQuery->latest()->limit(20)->get(),
            'recentAttendances' => $attendanceQuery->latest('tanggal')->latest('id')->limit(15)->get(),
            'stats' => $stats,
            'roleCounts' => $roleCounts,
            'search' => $search,
            'section' => $section,
            'payments' => $paymentQuery->latest('paid_at')->limit(12)->get(),
            'supportThreads' => $supportQuery->latest('last_message_at')->limit(20)->get(),
            'planOptions' => $planOptions,
            'plans' => $planOptions,
        ]);
    }

    public function updateOrganizationStatus(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active','suspended'])],
            'reason' => ['nullable','string','max:1000'],
        ]);
        if ($validated['status'] === 'suspended' && blank($validated['reason'] ?? null)) {
            return back()->withErrors(['reason'=>'Alasan penangguhan wajib diisi.']);
        }
        $organization->update([
            'is_active' => $validated['status'] === 'active',
            'suspended_reason' => $validated['status'] === 'suspended' ? $validated['reason'] : null,
        ]);
        LogHelper::record($request->user(), $organization->id, 'Status workspace diubah', "Status workspace {$organization->name} diubah menjadi {$validated['status']}.".($validated['reason'] ?? false ? " Alasan: {$validated['reason']}" : ''), $request);
        return back()->with('success', $organization->is_active ? 'Workspace diaktifkan kembali.' : 'Workspace berhasil ditangguhkan.');
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:plans,name'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_value' => ['required', 'integer', 'min:1', 'max:1200'],
            'duration_unit' => ['required', Rule::in(['days', 'months'])],
            'max_users' => ['required', 'integer', 'min:1'],
            'retention_months' => ['nullable', 'integer', 'min:1'],
            'has_audit_log' => ['nullable', 'boolean'],
            'has_custom_suffix' => ['nullable', 'boolean'],
            'is_private' => ['nullable', 'boolean'],
        ]);

        $validated['has_audit_log'] = $request->boolean('has_audit_log');
        $validated['has_custom_suffix'] = $request->boolean('has_custom_suffix');
        $validated['is_private'] = $request->boolean('is_private');

        $plan = Plan::create($validated);
        LogHelper::record($request->user(), null, 'Paket dibuat', "Paket {$plan->name} dibuat sebagai ".($plan->is_private ? 'paket khusus.' : 'paket publik.'), $request);

        return back()->with('success', 'Paket berhasil ditambahkan.');
    }

    public function updatePlan(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('plans', 'name')->ignore($plan->id)],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_value' => ['required', 'integer', 'min:1', 'max:1200'],
            'duration_unit' => ['required', Rule::in(['days', 'months'])],
            'max_users' => ['required', 'integer', 'min:1'],
            'retention_months' => ['nullable', 'integer', 'min:1'],
            'has_audit_log' => ['nullable', 'boolean'],
            'has_custom_suffix' => ['nullable', 'boolean'],
            'is_private' => ['nullable', 'boolean'],
        ]);

        $validated['has_audit_log'] = $request->boolean('has_audit_log');
        $validated['has_custom_suffix'] = $request->boolean('has_custom_suffix');
        $validated['is_private'] = $request->boolean('is_private');

        $plan->update($validated);

        // Update all organizations that reference this plan name
        Organization::where('plan_id', $plan->id)->update(['plan' => $plan->name]);
        LogHelper::record($request->user(), null, 'Paket diperbarui', "Pengaturan paket {$plan->name} diperbarui.", $request);

        return back()->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroyPlan(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        if ((float) $plan->price === 0.0 && Plan::where('price', 0)->count() <= 1) {
            return back()->withErrors(['error' => 'Paket gratis utama tidak dapat dihapus karena digunakan saat langganan berakhir.']);
        }
        $activeOrganizations = Organization::where('plan_id', $plan->id)->where('subscription_status', 'active')->count();
        $plan->delete();
        LogHelper::record($request->user(), null, 'Paket diarsipkan', "Paket {$plan->name} dihapus dari penjualan; {$activeOrganizations} workspace aktif tetap dapat menggunakannya sampai masa berlaku selesai.", $request);
        return back()->with('success', $activeOrganizations
            ? "Paket dihapus dari penjualan. {$activeOrganizations} workspace aktif tetap memakai paket sampai masa berlakunya selesai."
            : 'Paket berhasil dihapus dari penjualan.');
    }

    public function storePayment(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'amount' => ['required','numeric','min:0'],
            'status' => ['required', Rule::in(['paid','pending','failed','refunded'])],
            'method' => ['nullable','string','max:50'],
            'reference' => ['nullable','string','max:100'],
            'period_start' => ['nullable','date'],
            'period_end' => ['nullable','date','after_or_equal:period_start'],
            'paid_at' => ['nullable','date'],
            'notes' => ['nullable','string','max:1000'],
        ]);
        $payment = $organization->payments()->create($validated + ['recorded_by'=>$request->user()->id]);
        if ($validated['status'] === 'paid') {
            $organization->update(['subscription_status'=>'active','subscription_ends_at'=>$validated['period_end'] ?? $organization->subscription_ends_at]);
        }
        LogHelper::record($request->user(), $organization->id, 'Pembayaran manual dicatat', "Pembayaran {$payment->status} sebesar Rp ".number_format((float) $payment->amount, 0, ',', '.').'.', $request);
        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function destroyOrganization(Request $request, Organization $organization)
    {
        abort_if($organization->users()->where('role', 'superadmin')->exists(), 403, 'Workspace platform tidak dapat dihapus.');
        $request->validate(['confirmation'=>['required', Rule::in([$organization->name])]], [
            'confirmation.in'=>'Ketik nama organisasi dengan tepat untuk mengonfirmasi penghapusan.',
        ]);
        $name = $organization->name;
        LogHelper::record($request->user(), $organization->id, 'Workspace dihapus', "Workspace {$name} dihapus permanen oleh superadmin.", $request);
        $organization->delete();
        return redirect()->route('superadmin.dashboard')->with('success', "Workspace {$name} beserta seluruh datanya telah dihapus.");
    }
}
