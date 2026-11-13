<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status pembayaran · MWork</title>
    <meta name="robots" content="noindex,nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.pwa')
</head>
<body class="grid min-h-screen place-items-center bg-slate-50 p-5 text-slate-900">
    <main class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm">
        <img src="{{ asset('mwork.png') }}" alt="MWork" class="mx-auto mb-6 h-12 w-12 rounded-xl object-contain">
        @if($payment?->status === 'paid')
            <h1 class="text-2xl font-bold">Pembayaran berhasil</h1>
            <p class="mt-3 text-sm leading-6 text-slate-500">Paket workspace sudah diperbarui. Silakan masuk kembali ke MWork.</p>
        @elseif($payment)
            <h1 class="text-2xl font-bold">Pembayaran sedang diverifikasi</h1>
            <p class="mt-3 text-sm leading-6 text-slate-500">Status paket diperbarui otomatis setelah pembayaran dikonfirmasi.</p>
        @else
            <h1 class="text-2xl font-bold">Transaksi tidak ditemukan</h1>
            <p class="mt-3 text-sm leading-6 text-slate-500">Silakan buka riwayat tagihan dari akun admin workspace.</p>
        @endif
        <a href="{{ route('login') }}" class="mt-7 inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">Kembali ke MWork</a>
    </main>
</body>
</html>
