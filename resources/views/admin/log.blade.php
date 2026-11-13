<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log · {{ $organization->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html,body{overflow-x:hidden}
        dialog{opacity:0;transform:translateY(12px) scale(.98);transition:.18s ease;transition-behavior:allow-discrete}
        dialog[open]{opacity:1;transform:none}
        dialog::backdrop{background:rgba(15,23,42,.62);backdrop-filter:blur(5px)}
        @starting-style{dialog[open]{opacity:0;transform:translateY(12px) scale(.98)}}
    </style>
    @include('partials.pwa')
</head>
<body class="min-h-screen bg-[#f6f7fb] text-slate-900 antialiased">
    @include('partials.admin-sidebar')
    <div id="overlay" class="fixed inset-0 z-30 hidden bg-slate-950/60 lg:hidden"></div>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-20 flex h-20 items-center justify-between border-b border-slate-200/80 bg-white/90 px-5 backdrop-blur-xl sm:px-8">
            <div class="flex items-center gap-4">
                <button id="menuButton" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 lg:hidden">☰</button>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-indigo-600">Keamanan workspace</p>
                    <h1 class="text-xl font-semibold tracking-tight">Log aktivitas</h1>
                </div>
            </div>
            <div class="hidden items-center gap-3 sm:flex">
                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Sistem aktif</span>
            </div>
        </header>

        <main class="p-4 sm:p-8 lg:p-10">
            <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Audit Log Perusahaan</h1>
                    <p class="mt-1 text-sm text-slate-500">Aktivitas dan keamanan khusus workspace {{ $organization->name }}.</p>
                </div>
                @if(!$hasAuditLog)
                    <a href="{{ route('admin.billing') }}" class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white shadow hover:bg-slate-800">
                        Upgrade Paket
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('admin.logs') }}" class="mb-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 md:grid-cols-4 sm:p-5">
                <label><span class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal</span><input type="date" name="tanggal" value="{{ request('tanggal') }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-slate-500"></label>
                <label><span class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Role</span><select name="role" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Semua role</option><option value="admin" @selected(request('role')==='admin')>Admin</option><option value="pegawai" @selected(request('role')==='pegawai')>Operator</option><option value="guru" @selected(request('role')==='guru')>Atasan</option><option value="siswa" @selected(request('role')==='siswa')>Anggota</option></select></label>
                <label><span class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500">Pengguna</span><select name="user_id" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Semua pengguna</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string)request('user_id')===(string)$user->id)>{{ $user->login_identity }}</option>@endforeach</select></label>
                <div class="flex items-end gap-2"><button class="h-11 flex-1 rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white hover:bg-slate-800">Terapkan</button><a href="{{ route('admin.logs') }}" class="grid h-11 place-items-center rounded-xl border border-slate-300 px-4 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a></div>
            </form>

            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 sm:p-5">
                    <div class="min-w-0">
                        <h2 class="font-semibold text-sm">Aktivitas Terbaru</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Riwayat perubahan dan tindakan pengguna.</p>
                    </div>
                    @if($hasAuditLog)
                        <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Audit Aktif</span>
                    @else
                        <span class="shrink-0 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Preview 3 Aktivitas</span>
                    @endif
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($logs as $index => $log)
                        @php
                            $isBlurred = !$hasAuditLog && $index >= 3;
                        @endphp
                        <article class="flex flex-col gap-3 p-4 sm:px-5 md:flex-row md:items-center md:justify-between transition-colors hover:bg-slate-50/50 {{ $isBlurred ? 'blur-[5px] select-none pointer-events-none opacity-40' : '' }}">
                            <div class="min-w-0 md:w-1/3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium text-slate-400">{{ $log->created_at->format('d M Y · H:i') }}</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 uppercase md:hidden">{{ $log->role }}</span>
                                </div>
                                <strong class="mt-1 block truncate text-sm font-semibold text-slate-900">{{ $log->aksi }}</strong>
                                <p class="mt-0.5 truncate text-xs text-slate-500 md:hidden">Oleh: <span class="font-medium text-slate-700">{{ $log->user ?: 'System' }}</span></p>
                            </div>

                            <!-- Inline Desktop Columns (Visible on md and up) -->
                            <div class="hidden md:flex md:items-center md:gap-4 md:w-1/2 text-xs text-slate-600">
                                <div class="w-1/4">
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 capitalize">{{ $log->role }}</span>
                                </div>
                                <div class="w-2/4 truncate">
                                    <span class="font-semibold text-slate-800">{{ $log->user ?: 'System' }}</span>
                                    <span class="block text-[11px] text-slate-400 truncate">{{ $log->keterangan ?: '-' }}</span>
                                </div>
                                <div class="w-1/4 font-mono text-[11px] text-slate-400 truncate">
                                    {{ $log->ip_address ?: '-' }}
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end shrink-0">
                                <button type="button" class="log-detail shrink-0 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:border-slate-400 md:hidden"
                                    data-time="{{ $log->created_at->format('d M Y H:i:s') }}"
                                    data-user="{{ $log->user ?: 'System' }}"
                                    data-role="{{ $log->role }}"
                                    data-action="{{ $log->aksi }}"
                                    data-note="{{ $log->keterangan ?: '-' }}"
                                    data-ip="{{ $log->ip_address ?: '-' }}"
                                    data-device="{{ $log->user_agent ?: '-' }}">Detail</button>
                            </div>
                        </article>
                    @empty
                        <p class="px-5 py-16 text-center text-sm text-slate-400">Tidak ada aktivitas yang sesuai dengan filter.</p>
                    @endforelse
                </div>

                @if(!$hasAuditLog && count($logs) > 3)
                    <div class="absolute inset-x-0 bottom-0 top-[180px] z-10 flex flex-col items-center justify-center bg-slate-950/40 p-6 text-center backdrop-blur-md">
                        <div class="max-w-md rounded-2xl border border-slate-700 bg-slate-900 p-6 text-white shadow-2xl">
                            <h3 class="text-xl font-bold">Audit Log Terkunci</h3>
                            <p class="mt-2 text-xs leading-relaxed text-slate-300">
                                Paket langganan Anda saat ini (<strong class="text-white capitalize">{{ $organization->plan_details->name }}</strong>) hanya menampilkan 3 log terbaru. Upgrade paket Anda untuk membuka seluruh riwayat audit keamanan.
                            </p>
                            <a href="{{ route('admin.billing') }}" class="mt-5 inline-flex w-full justify-center rounded-xl bg-slate-100 hover:bg-white py-2.5 text-xs font-bold text-slate-900 transition-all">
                                Upgrade Paket Sekarang
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            @if($hasAuditLog && $logs->hasPages())
                <div class="mt-6">{{ $logs->links() }}</div>
            @endif
        </main>
    </div>

    <dialog id="logDetailModal" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-[1.5rem] bg-white p-0 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
            <h3 class="font-semibold text-slate-900">Detail Aktivitas</h3>
            <button type="button" onclick="logDetailModal.close()" class="text-2xl text-slate-400 hover:text-slate-600">×</button>
        </div>
        <div class="space-y-3.5 px-6 py-5 text-sm">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Waktu Kejadian</p>
                <p id="modalLogTime" class="mt-0.5 font-medium text-slate-900"></p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pengguna</p>
                    <p id="modalLogUser" class="mt-0.5 font-medium text-slate-900 break-all"></p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Peran Access</p>
                    <p id="modalLogRole" class="mt-0.5 font-medium text-indigo-600 capitalize"></p>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tindakan / Aksi</p>
                <p id="modalLogAction" class="mt-0.5 rounded-xl bg-slate-100 p-3 text-slate-800 font-medium leading-relaxed"></p>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Keterangan Tambahan</p>
                <p id="modalLogNote" class="mt-0.5 text-slate-600 break-words"></p>
            </div>
            <div class="border-t border-slate-100 pt-3 text-xs space-y-1 text-slate-400">
                <p>IP Address: <span id="modalLogIp" class="font-mono text-slate-600"></span></p>
                <p>User Agent: <span id="modalLogDevice" class="break-all font-mono text-slate-600"></span></p>
            </div>
        </div>
        <div class="flex justify-end border-t border-slate-200 px-6 py-4">
            <button type="button" onclick="logDetailModal.close()" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Tutup</button>
        </div>
    </dialog>

    <script>
        const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('overlay'),menuButton=document.getElementById('menuButton');
        menuButton.addEventListener('click',()=>{sidebar.classList.remove('-translate-x-full');overlay.classList.remove('hidden')});
        overlay.addEventListener('click',()=>{sidebar.classList.add('-translate-x-full');overlay.classList.add('hidden')});

        const logDetailModal = document.getElementById('logDetailModal');
        document.querySelectorAll('.log-detail').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('modalLogTime').textContent = button.dataset.time || '-';
                document.getElementById('modalLogUser').textContent = button.dataset.user || '-';
                document.getElementById('modalLogRole').textContent = button.dataset.role || '-';
                document.getElementById('modalLogAction').textContent = button.dataset.action || '-';
                document.getElementById('modalLogNote').textContent = button.dataset.note || '-';
                document.getElementById('modalLogIp').textContent = button.dataset.ip || '-';
                document.getElementById('modalLogDevice').textContent = button.dataset.device || '-';
                logDetailModal.showModal();
            });
        });
    </script>
</body>
</html>
