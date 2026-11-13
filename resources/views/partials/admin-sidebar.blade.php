<aside id="sidebar" class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-slate-800 bg-slate-900 text-slate-300 transition-transform lg:translate-x-0">
    <div class="border-b border-white/10 px-6 py-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-base font-bold text-white" style="background:{{ $organization->primary_color }}">{{ strtoupper(substr($organization->name,0,1)) }}</span>
            <span class="min-w-0"><strong class="block truncate text-sm text-white">{{ $organization->name }}</strong><span class="text-xs text-slate-400">{{ $organization->type_label }} · by MWork</span></span>
        </a>
    </div>
    <nav class="flex-1 space-y-1 overflow-y-auto p-4 text-sm">
        <p class="px-3 pb-2 pt-3 text-[10px] font-bold uppercase tracking-[.18em] text-slate-500">Ruang kerja</p>
        
        @if(request()->routeIs('admin.dashboard'))
            <button data-view="overview" class="nav-item active flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-white/10 hover:text-white">Ringkasan</button>
            <button data-view="people" class="nav-item flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-white/10 hover:text-white">Pengguna & Akses</button>
            <button data-view="teams" class="nav-item flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-white/10 hover:text-white">Tim</button>
            <button data-view="locations" class="nav-item flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left hover:bg-white/10 hover:text-white">Lokasi</button>
            <p class="px-3 pb-2 pt-6 text-[10px] font-bold uppercase tracking-[.18em] text-slate-500">Pengaturan</p>
            <a href="{{ route('admin.policies') }}" class="flex items-center gap-3 rounded px-3 py-2.5 hover:bg-white/10 hover:text-white">Aturan Absensi</a>
            <a href="{{ route('admin.organization') }}" class="flex items-center gap-3 rounded px-3 py-2.5 hover:bg-white/10 hover:text-white">Profil Organisasi</a>
        @else
            <a href="{{ route('admin.dashboard', ['view' => 'overview']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/10 hover:text-white">Ringkasan</a>
            <a href="{{ route('admin.dashboard', ['view' => 'people']) }}" class="flex items-center gap-3 rounded px-3 py-2.5 hover:bg-white/10 hover:text-white">Pengguna & Akses</a>
            <a href="{{ route('admin.dashboard', ['view' => 'teams']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/10 hover:text-white">Tim</a>
            <a href="{{ route('admin.dashboard', ['view' => 'locations']) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/10 hover:text-white">Lokasi</a>
            <p class="px-3 pb-2 pt-6 text-[10px] font-bold uppercase tracking-[.18em] text-slate-500">Pengaturan</p>
            <a href="{{ route('admin.policies') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.policies') ? 'bg-white/10 text-white font-semibold' : '' }}">Aturan Absensi</a>
            <a href="{{ route('admin.organization') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.organization') ? 'bg-white/10 text-white font-semibold' : '' }}">Profil Organisasi</a>
        @endif

        <a href="{{ route('admin.logs') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.logs') ? 'bg-white/10 text-white font-semibold' : '' }}">Log Aktivitas</a>
        <a href="{{ route('admin.billing') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.billing') ? 'bg-white/10 text-white font-semibold' : '' }}">Tagihan & Paket</a>
        <a href="{{ route('admin.support') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-white/10 hover:text-white {{ request()->routeIs('admin.support') ? 'bg-white/10 text-white font-semibold' : '' }}">Bantuan / CS</a>
    </nav>
    <div class="border-t border-white/10 p-4">
        <div class="mb-3 flex items-center gap-3 px-2"><span class="grid h-9 w-9 place-items-center rounded-full bg-slate-700 text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->username,0,2)) }}</span><span class="min-w-0"><strong class="block truncate text-sm text-white">{{ auth()->user()->username }}</strong><span class="text-xs text-slate-500">Admin workspace</span></span></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full rounded-xl border border-white/10 px-4 py-2 text-left text-sm hover:bg-white/10 hover:text-white">Keluar</button></form>
    </div>
</aside>
