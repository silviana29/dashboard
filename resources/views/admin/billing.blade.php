<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan & Paket · {{ $organization->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.pwa')
    <style>
        dialog::backdrop { background: rgba(15, 23, 42, .48); }
        dialog[open] { animation: modal-in .14s ease-out; }
        @keyframes modal-in { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .billing-ui main > section, .billing-ui main article { border-radius: 14px; overflow: hidden; border-color: #e2e8f0 !important; }
        .billing-ui main > div[class*="border"], .billing-ui dialog [class*="border"] { border-radius: 10px; }
        .billing-ui main button, .billing-ui main a, .billing-ui dialog button { border-radius: 9px; }
    </style>
</head>
@php
    $currentPlan = $organization->plan_details;
    $subscriptionLabel = match($organization->subscription_status) {
        'active' => 'Aktif',
        'trial' => 'Percobaan',
        'past_due' => 'Jatuh tempo',
        'expired' => 'Kembali ke Free',
        'cancelled' => 'Dibatalkan',
        default => ucfirst(str_replace('_', ' ', $organization->subscription_status)),
    };
    $subscriptionClass = match($organization->subscription_status) {
        'active' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'trial' => 'border-blue-200 bg-blue-50 text-blue-700',
        'past_due' => 'border-amber-200 bg-amber-50 text-amber-700',
        default => 'border-slate-200 bg-slate-100 text-slate-600',
    };
@endphp
<body class="billing-ui min-h-screen text-slate-800 antialiased">
    @include('partials.admin-sidebar')
    <div id="overlay" class="fixed inset-0 z-30 hidden bg-slate-950/60 lg:hidden"></div>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-20 flex h-20 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                <button id="menuButton" type="button" class="grid h-9 w-9 shrink-0 place-items-center border border-slate-300 bg-white text-sm hover:bg-slate-50 lg:hidden">☰</button>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 text-[11px] text-slate-500"><a href="{{ route('admin.dashboard') }}" class="hover:text-blue-700">Admin</a><span>/</span><span>Keuangan</span></div>
                    <h1 class="truncate text-lg font-semibold text-slate-900">Tagihan & Paket</h1>
                </div>
            </div>
            <div class="hidden items-center gap-2 text-xs text-slate-500 sm:flex"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Sistem pembayaran tersedia</div>
        </header>

        <main class="mx-auto max-w-[1400px] p-4 sm:p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-4 flex items-start justify-between border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><span><strong>Berhasil.</strong> {{ session('success') }}</span><button type="button" onclick="this.parentElement.remove()" aria-label="Tutup">×</button></div>
            @endif
            @if(session('info'))
                <div class="mb-4 flex items-start justify-between border border-blue-300 bg-blue-50 px-4 py-3 text-sm text-blue-800"><span><strong>Informasi.</strong> {{ session('info') }}</span><button type="button" onclick="this.parentElement.remove()" aria-label="Tutup">×</button></div>
            @endif
            @if(session('error'))
                <div class="mb-4 border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800"><strong>Pembayaran gagal.</strong> {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800"><strong>Periksa kembali data pembayaran:</strong><ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                <div><h2 class="text-xl font-semibold text-slate-950">Langganan workspace</h2><p class="mt-1 text-sm text-slate-500">Kelola paket, masa aktif, dan pembayaran {{ $organization->name }}.</p></div>
                <button type="button" onclick="upgradePlanModal.showModal()" class="inline-flex h-10 items-center justify-center border border-blue-800 bg-blue-700 px-4 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">Pilih atau perpanjang paket</button>
            </div>

            <section class="mb-6">
                <article class="border border-slate-300 bg-white shadow-sm">
                    <div class="border-b border-slate-300 bg-slate-50 px-5 py-3"><div class="flex items-center justify-between gap-3"><h3 class="text-sm font-semibold text-slate-900">Paket saat ini</h3><span class="border px-2 py-0.5 text-[11px] font-semibold {{ $subscriptionClass }}">{{ $subscriptionLabel }}</span></div></div>
                    <div class="px-5 py-5">
                        <div class="text-3xl font-bold tracking-tight text-slate-950">{{ $currentPlan->name }}</div>
                        <div class="mt-1 text-sm text-slate-500">@if((float) $currentPlan->price > 0) Rp {{ number_format($currentPlan->price, 0, ',', '.') }} / {{ $currentPlan->duration_label }} @else Tidak dikenakan biaya @endif</div>
                        <dl class="mt-5 divide-y divide-slate-200 border-y border-slate-200 text-sm">
                            <div class="flex justify-between gap-4 py-2.5"><dt class="text-slate-500">Masa aktif</dt><dd class="text-right font-medium text-slate-900">{{ $organization->subscription_ends_at?->translatedFormat('d M Y') ?? 'Tanpa batas' }}</dd></div>
                            <div class="flex justify-between gap-4 py-2.5"><dt class="text-slate-500">Kuota pengguna</dt><dd class="text-right font-medium text-slate-900">{{ number_format($currentPlan->max_users) }} akun</dd></div>
                            <div class="flex justify-between gap-4 py-2.5"><dt class="text-slate-500">Penyimpanan riwayat</dt><dd class="text-right font-medium text-slate-900">{{ $currentPlan->retention_months ? $currentPlan->retention_months . ' bulan' : 'Tanpa batas' }}</dd></div>
                        </dl>
                    </div>
                </article>
            </section>

            <section class="border border-slate-300 bg-white shadow-sm">
                <div class="flex flex-col justify-between gap-2 border-b border-slate-300 bg-slate-50 px-5 py-3 sm:flex-row sm:items-center">
                    <div><h2 class="text-sm font-semibold text-slate-900">Riwayat transaksi</h2><p class="mt-0.5 text-xs text-slate-500">Daftar pembayaran paket workspace terbaru.</p></div>
                    <span class="text-xs text-slate-500">Total {{ number_format($payments->total()) }} transaksi</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-left text-sm">
                        <thead class="border-b border-slate-300 bg-white text-[11px] uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3 font-semibold">Tanggal / Order ID</th><th class="px-4 py-3 font-semibold">Paket</th><th class="px-4 py-3 font-semibold">Periode</th><th class="px-4 py-3 text-right font-semibold">Nominal</th><th class="px-4 py-3 font-semibold">Metode</th><th class="px-4 py-3 font-semibold">Status</th><th class="px-5 py-3 text-right font-semibold">Tindakan</th></tr></thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($payments as $payment)
                                @php
                                    $paymentStatusClass = match($payment->status) {
                                        'paid' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                        'pending' => 'border-amber-200 bg-amber-50 text-amber-700',
                                        'failed', 'cancelled' => 'border-rose-200 bg-rose-50 text-rose-700',
                                        default => 'border-slate-200 bg-slate-100 text-slate-600',
                                    };
                                    $paymentStatusLabel = match($payment->status) {
                                        'paid' => 'Berhasil', 'pending' => 'Menunggu', 'failed' => 'Gagal', 'cancelled' => 'Dibatalkan', 'refunded' => 'Dikembalikan', default => ucfirst($payment->status),
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-5 py-3.5 align-top"><div class="font-medium text-slate-900">{{ ($payment->paid_at ?? $payment->created_at)->format('d M Y, H:i') }}</div><div class="mt-1 font-mono text-[10px] text-slate-400">{{ $payment->merchant_order_id ?: ($payment->reference ?: 'Pembayaran manual') }}</div></td>
                                    <td class="px-4 py-3.5 align-top"><div class="font-medium text-slate-900">{{ $payment->plan?->name ?? 'Paket workspace' }}</div><div class="mt-1 text-xs text-slate-500">{{ $payment->duration_value ?? 1 }} {{ $payment->duration_unit === 'days' ? 'hari' : 'bulan' }}</div></td>
                                    <td class="whitespace-nowrap px-4 py-3.5 align-top text-xs text-slate-600">@if($payment->period_start && $payment->period_end) {{ $payment->period_start->format('d M Y') }}<br><span class="text-slate-400">sampai</span> {{ $payment->period_end->format('d M Y') }} @else <span class="text-slate-400">Belum ditentukan</span> @endif</td>
                                    <td class="whitespace-nowrap px-4 py-3.5 text-right align-top font-semibold text-slate-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5 align-top text-slate-600">{{ $payment->method ?: ($payment->gateway === 'invoice' ? 'Belum dipilih' : ($payment->gateway === 'manual' ? 'Manual' : 'Pembayaran online')) }}</td>
                                    <td class="px-4 py-3.5 align-top"><span class="inline-flex border px-2 py-0.5 text-[11px] font-semibold {{ $paymentStatusClass }}">{{ $paymentStatusLabel }}</span></td>
                                    <td class="px-5 py-3.5 align-top"><div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.billing.payments.invoice', $payment) }}" class="whitespace-nowrap border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Invoice PDF</a>
                                        @if($payment->status !== 'paid')<form method="POST" action="{{ route('admin.billing.payments.pay', $payment) }}">@csrf<button class="whitespace-nowrap border border-blue-700 bg-blue-700 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-blue-800">Bayar</button></form>@endif
                                        @if($payment->status === 'pending' && $payment->gateway === 'duitku')<form method="POST" action="{{ route('admin.billing.payments.sync', $payment) }}">@csrf<button class="whitespace-nowrap border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Cek status</button></form>@endif
                                    </div></td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-5 py-14 text-center"><strong class="block text-sm text-slate-700">Belum ada transaksi</strong><span class="mt-1 block text-xs text-slate-500">Pembayaran paket akan ditampilkan di tabel ini.</span></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($payments->hasPages())<div class="border-t border-slate-300 bg-slate-50 px-5 py-3">{{ $payments->links() }}</div>@endif
            </section>
        </main>
    </div>

    <dialog id="upgradePlanModal" class="m-auto w-[calc(100%-1.5rem)] max-w-3xl border border-slate-500 bg-white p-0 shadow-2xl outline-none">
        <form method="POST" action="{{ route('admin.billing.checkout') }}">
            @csrf
            <div class="flex items-start justify-between border-b border-slate-300 bg-slate-100 px-5 py-4">
                <div><h3 class="text-base font-semibold text-slate-950">Pembayaran paket</h3><p class="mt-1 text-xs text-slate-500">Pilih paket untuk membayar langsung atau mengunduh invoice penawaran.</p></div>
                <button type="button" onclick="upgradePlanModal.close()" class="grid h-7 w-7 place-items-center border border-slate-300 bg-white text-lg leading-none text-slate-500 hover:bg-slate-50" aria-label="Tutup">×</button>
            </div>
            <div class="max-h-[70vh] space-y-6 overflow-y-auto p-5 sm:p-6">
                <fieldset>
                    <legend class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-600">1. Pilih paket</legend>
                    <div class="divide-y divide-slate-200 border border-slate-300">
                        @foreach($planOptions->where('price', '>', 0) as $planOptionItem)
                            @php $isCurrent = $currentPlan->id === $planOptionItem->id; @endphp
                            <label class="group grid cursor-pointer grid-cols-[auto_minmax(0,1fr)] gap-3 bg-white px-4 py-4 hover:bg-blue-50 sm:grid-cols-[auto_minmax(0,1fr)_auto] sm:items-center">
                                <input type="radio" name="plan_id" value="{{ $planOptionItem->id }}" data-price="{{ (int) round($planOptionItem->price) }}" class="plan-option mt-1 h-4 w-4 accent-blue-700 sm:mt-0" {{ (string) old('plan_id') === (string) $planOptionItem->id || (!old('plan_id') && ($isCurrent || ((float) $currentPlan->price <= 0 && $loop->first))) ? 'checked' : '' }} required>
                                <span class="min-w-0"><span class="flex flex-wrap items-center gap-2"><strong class="text-sm text-slate-900">{{ $planOptionItem->name }}</strong>@if($isCurrent)<span class="border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-semibold text-blue-700">Paket aktif</span>@endif @if($planOptionItem->is_private)<span class="border border-violet-200 bg-violet-50 px-1.5 py-0.5 text-[10px] font-semibold text-violet-700">Khusus untuk perusahaan Anda</span>@endif</span><span class="mt-1 block text-xs text-slate-500">Masa aktif {{ $planOptionItem->duration_label }} · {{ number_format($planOptionItem->max_users) }} pengguna · Riwayat {{ $planOptionItem->retention_months ? $planOptionItem->retention_months . ' bulan' : 'tanpa batas' }} · {{ $planOptionItem->has_audit_log ? 'Termasuk audit log' : 'Tanpa audit log' }}</span></span>
                                <span class="col-start-2 whitespace-nowrap text-sm font-bold text-slate-950 sm:col-auto">Rp {{ number_format($planOptionItem->price, 0, ',', '.') }}<span class="font-normal text-slate-400">/{{ $planOptionItem->duration_label }}</span></span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <fieldset>
                    <legend class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-600">2. Detail pembayaran</legend>
                    <div class="grid gap-4 border border-slate-300 bg-slate-50 p-4 sm:grid-cols-2">
                        <label class="text-xs font-semibold text-slate-700">Email penerima invoice<input type="email" name="email" value="{{ old('email') }}" placeholder="finance@perusahaan.com" class="mt-1.5 h-10 w-full border border-slate-300 bg-white px-3 text-sm outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600" required></label>
                        <label class="text-xs font-semibold text-slate-700">Nomor telepon <span class="font-normal text-slate-400">(opsional)</span><input type="text" name="phone" value="{{ old('phone') }}" placeholder="081234567890" class="mt-1.5 h-10 w-full border border-slate-300 bg-white px-3 text-sm outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600"></label>
                    </div>
                </fieldset>
                <div class="border border-blue-300 bg-blue-50 p-4"><div class="flex items-end justify-between gap-4"><span class="text-sm font-medium text-blue-900">Total pembayaran</span><strong id="paymentTotal" class="text-2xl text-blue-950">Rp 0</strong></div><p class="mt-2 border-t border-blue-200 pt-2 text-xs leading-5 text-blue-800">Harga dan masa aktif mengikuti paket yang ditetapkan MWork. Paket aktif otomatis setelah pembayaran terkonfirmasi.</p></div>
                @unless($paymentConfigured)<div class="border border-amber-300 bg-amber-50 p-3 text-xs leading-5 text-amber-800"><strong>Pembayaran online belum siap.</strong> Konfigurasi pembayaran belum terbaca oleh aplikasi. Invoice penawaran tetap dapat diunduh.</div>@endunless
            </div>
            <div class="flex flex-wrap justify-end gap-2 border-t border-slate-300 bg-slate-100 px-5 py-3"><button type="button" onclick="upgradePlanModal.close()" class="h-9 border border-slate-400 bg-white px-4 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button><button type="submit" formaction="{{ route('admin.billing.proposal') }}" class="h-9 border border-slate-500 bg-white px-4 text-xs font-semibold text-slate-800 hover:bg-slate-50">Unduh invoice penawaran</button><button @disabled(!$paymentConfigured) class="h-9 border border-blue-800 bg-blue-700 px-5 text-xs font-semibold text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:border-slate-300 disabled:bg-slate-300">Lanjut ke pembayaran</button></div>
        </form>
    </dialog>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuButton = document.getElementById('menuButton');
        menuButton?.addEventListener('click', () => { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); });
        overlay.addEventListener('click', () => { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); });
        const rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });
        const total = document.getElementById('paymentTotal');
        const updateTotal = () => { const selected = document.querySelector('.plan-option:checked'); total.textContent = rupiah.format(Number(selected?.dataset.price) || 0); };
        document.querySelectorAll('.plan-option').forEach(option => option.addEventListener('change', updateTotal));
        updateTotal();
        @if($errors->any()) upgradePlanModal.showModal(); @endif
    </script>
</body>
</html>
