<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\JenisIzinController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\MemberRegistrationController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\DuitkuPaymentController;

Route::post('/payments/callback', [DuitkuPaymentController::class, 'callback'])->name('payments.gateway.callback');
Route::get('/payments/return', [DuitkuPaymentController::class, 'returnFromGateway'])->name('payments.gateway.return');


Route::get('/', function () {
    if (!Auth::check()) {
        // Belum login → tampilkan landing page
        return view('landing.index');
    }

    return redirect(match (Auth::user()->role) {
        'superadmin' => '/superadmin/dashboard',
        'admin' => '/admin/dashboard',
        'pegawai' => '/pegawai/rekap-absensi',
        'siswa' => '/siswa/absen',
        'guru' => '/guru/rekap-absensi',
        default => '/login',
    });
});

// Fallback untuk shared hosting yang tidak dapat membaca symlink public/storage.
Route::get('/media/absen/{path}', [SiswaController::class, 'showFotoAbsensi'])
    ->where('path', '.*')
    ->middleware('auth')
    ->name('absensi.media');

Route::get('/storage/absen/{path}', [SiswaController::class, 'showFotoAbsensi'])
    ->where('path', '.*')
    ->middleware('auth')
    ->name('absensi.photo');

// Kompatibilitas untuk URL lama ketika document root mengarah ke folder proyek.
Route::get('/public/storage/absen/{path}', [SiswaController::class, 'showFotoAbsensi'])
    ->where('path', '.*')
    ->middleware('auth')
    ->name('absensi.photo.legacy');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::get('/login/{organization:slug}', [AuthController::class, 'showLogin'])->name('login.organization')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/pasang-aplikasi', [AuthController::class, 'showInstall'])->name('pwa.install');
Route::get('/mulai', [OrganizationController::class, 'create'])->name('organizations.create')->middleware('guest');
Route::get('/mulai/cek-slug', [OrganizationController::class, 'slugAvailability'])->name('organizations.slug-availability')->middleware('guest');
Route::post('/mulai', [OrganizationController::class, 'store'])->name('organizations.store')->middleware('guest');
Route::get('/daftar', [MemberRegistrationController::class, 'choice'])->name('registrations.choice')->middleware('guest');
Route::get('/daftar/anggota', [MemberRegistrationController::class, 'create'])->name('members.register')->middleware('guest');
Route::get('/daftar/anggota/{organization:company_code}', [MemberRegistrationController::class, 'create'])->name('members.register.organization')->middleware('guest');
Route::post('/daftar/anggota', [MemberRegistrationController::class, 'store'])->name('members.register.store')->middleware('guest');
Route::get('/daftar/cek-username', [MemberRegistrationController::class, 'usernameAvailability'])->name('members.username-availability')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/users/store', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/teams', [AdminController::class, 'storeTeam'])->name('admin.teams.store');
    Route::delete('/teams/{id}', [AdminController::class, 'destroyTeam'])->name('admin.teams.destroy');
    Route::post('/registrations/{id}/approve', [AdminController::class, 'approveRegistration'])->name('admin.registrations.approve');
    Route::post('/registrations/{id}/reject', [AdminController::class, 'rejectRegistration'])->name('admin.registrations.reject');
    Route::post('/gedungs', [AdminController::class, 'storeGedung'])->name('admin.gedungs.store');
    Route::put('/gedungs/{id}', [AdminController::class, 'updateGedung'])->name('admin.gedungs.update');
    Route::delete('/gedungs/{id}', [AdminController::class, 'destroyGedung'])->name('admin.gedungs.destroy');
    Route::get('/log-aktivitas', [LogController::class, 'index'])->name('admin.log.index');
    Route::get('/logs', [LogController::class, 'index'])->name('admin.logs');
    Route::get('/policies', [AdminController::class, 'policies'])->name('admin.policies');
    Route::get('/organization-profile', [AdminController::class, 'organizationProfile'])->name('admin.organization');
    Route::put('/organization', [OrganizationController::class, 'update'])->name('admin.organization.update');
    Route::resource('jenis-izin', JenisIzinController::class)->only(['store', 'destroy'])->names('admin.jenis_izin');
    Route::post('/attendance-types', [AdminController::class, 'storeAttendanceType'])->name('admin.attendance-types.store');
    Route::delete('/attendance-types/{id}', [AdminController::class, 'destroyAttendanceType'])->name('admin.attendance-types.destroy');
    Route::put('/guru/{id}', [AdminController::class, 'updateGuru'])->name('admin.guru.update');
    Route::get('/support', [SupportController::class, 'adminIndex'])->name('admin.support');
    Route::post('/support/threads', [SupportController::class, 'storeThread'])->name('admin.support.threads.store');
    Route::post('/support/threads/{thread}/messages', [SupportController::class, 'adminReply'])->name('admin.support.messages.store');
    Route::get('/billing', [SupportController::class, 'adminBilling'])->name('admin.billing');
    Route::post('/billing/upgrade', [SupportController::class, 'adminUpgradePlan'])->name('admin.billing.upgrade');
    Route::post('/billing/checkout', [DuitkuPaymentController::class, 'checkout'])->name('admin.billing.checkout');
    Route::post('/billing/proposal', [DuitkuPaymentController::class, 'proposal'])->name('admin.billing.proposal');
    Route::post('/billing/payments/{payment}/pay', [DuitkuPaymentController::class, 'pay'])->name('admin.billing.payments.pay');
    Route::get('/billing/payments/{payment}/invoice', [DuitkuPaymentController::class, 'invoice'])->name('admin.billing.payments.invoice');
    Route::post('/billing/payments/{payment}/sync', [DuitkuPaymentController::class, 'sync'])->name('admin.billing.payments.sync');
});

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/organizations', [SuperAdminController::class, 'index'])->defaults('section', 'organizations')->name('superadmin.organizations');
    Route::get('/users', [SuperAdminController::class, 'index'])->defaults('section', 'users')->name('superadmin.users');
    Route::get('/billing', [SuperAdminController::class, 'index'])->defaults('section', 'billing')->name('superadmin.billing');
    Route::get('/support', [SuperAdminController::class, 'index'])->defaults('section', 'support')->name('superadmin.support');
    Route::get('/audit', [SuperAdminController::class, 'index'])->defaults('section', 'audit')->name('superadmin.audit');
    Route::patch('/organizations/{organization}/status', [SuperAdminController::class, 'updateOrganizationStatus'])->name('superadmin.organizations.status');
    Route::post('/organizations/{organization}/payments', [SuperAdminController::class, 'storePayment'])->name('superadmin.organizations.payments.store');
    Route::delete('/organizations/{organization}', [SuperAdminController::class, 'destroyOrganization'])->name('superadmin.organizations.destroy');
    Route::post('/plans', [SuperAdminController::class, 'storePlan'])->name('superadmin.plans.store');
    Route::put('/plans/{plan}', [SuperAdminController::class, 'updatePlan'])->name('superadmin.plans.update');
    Route::delete('/plans/{plan}', [SuperAdminController::class, 'destroyPlan'])->name('superadmin.plans.destroy');
    Route::get('/support/{thread}', [SupportController::class, 'superShow'])->name('superadmin.support.show');
    Route::post('/support/{thread}/messages', [SupportController::class, 'superReply'])->name('superadmin.support.messages.store');
    Route::patch('/support/{thread}/status', [SupportController::class, 'updateStatus'])->name('superadmin.support.status');
    Route::post('/support/{thread}/plan-offers', [SupportController::class, 'storePlanOffer'])->name('superadmin.support.plan-offers.store');
});

Route::middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/pegawai/rekap-absensi', [App\Http\Controllers\PegawaiController::class, 'rekapAbsensi'])->name('pegawai.absensi.rekap');
    Route::get('/pegawai/rekap/export', [PegawaiController::class, 'exportRekap'])->name('pegawai.absensi.export');
});

Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/siswa/absen', [SiswaController::class, 'absenForm'])->name('siswa.absen.form');
    Route::post('/siswa/absen-masuk', [SiswaController::class, 'absenMasuk'])->name('siswa.absen.masuk');
    Route::post('/siswa/absen-keluar', [SiswaController::class, 'absenKeluar'])->name('siswa.absen.keluar');
    Route::post('/siswa/lapor-harian', [SiswaController::class, 'laporHarian'])->name('siswa.lapor-harian');
    Route::get('/siswa/riwayat', [App\Http\Controllers\SiswaController::class, 'getRiwayat'])->name('siswa.riwayat');
    Route::post('/siswa/absen-izin', [SiswaController::class, 'absenIzin'])->name('siswa.absen.izin');
});

Route::middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/guru/rekap-absensi', [GuruController::class, 'rekapAbsensi'])->name('guru.guru.rekap');
    Route::get('/guru/export-rekap', [GuruController::class, 'exportRekap'])->name('guru.export.rekap');
});
