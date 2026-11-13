<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $organization->name }} · Admin Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{brand:'{{ $organization->primary_color }}',ink:'#101828'},boxShadow:{soft:'0 14px 45px rgba(15,23,42,.08)'}}}}}</script>
    <style>
        html,body{overflow-x:hidden}[x-cloak]{display:none!important}.nav-item.active{background:rgba(255,255,255,.12);color:#fff}.view{display:none}.view.active{display:block}
        .admin-classic main .rounded-2xl,.admin-classic main .rounded-\[1\.5rem\],.admin-classic main .rounded-\[1\.75rem\]{border-radius:6px!important}
        .admin-classic main .rounded-xl,.admin-classic main .rounded-lg{border-radius:4px!important}
        .admin-classic main .shadow-sm,.admin-classic main .shadow-soft{box-shadow:none!important}
        .admin-classic button,.admin-classic input,.admin-classic select,.admin-classic textarea{box-shadow:none!important}
        #supervisorFields label:has(input:checked)>span,#editSupervisorFields label:has(input:checked)>span{border-color:#6366f1;background:#eef2ff;box-shadow:0 0 0 2px #e0e7ff}#supervisorFields label:has(input:checked)>span>span:last-child,#editSupervisorFields label:has(input:checked)>span>span:last-child{border-color:#4f46e5;background:#4f46e5;color:#fff}
        dialog{opacity:0;transform:translateY(12px) scale(.98);transition:.18s ease;transition-behavior:allow-discrete}dialog[open]{opacity:1;transform:none}dialog::backdrop{background:rgba(15,23,42,.62);backdrop-filter:blur(5px)}
        @starting-style{dialog[open]{opacity:0;transform:translateY(12px) scale(.98)}}
    </style>
    @include('partials.pwa')
</head>
<body class="admin-classic min-h-screen bg-[#f2f2f2] text-ink antialiased">
    @include('partials.admin-sidebar')
    <div id="overlay" class="fixed inset-0 z-30 hidden bg-slate-950/60 lg:hidden"></div>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-20 flex h-20 items-center justify-between border-b border-slate-200 bg-white px-5 backdrop-blur-xl sm:px-8">
            <div class="flex min-w-0 items-center gap-3"><button id="menuButton" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-slate-200 lg:hidden">Menu</button><div class="min-w-0"><p id="eyebrow" class="text-[11px] font-semibold uppercase tracking-[.16em] text-slate-500">Pusat pengelolaan</p><h1 id="pageTitle" class="truncate text-lg font-semibold tracking-tight sm:text-xl text-slate-900">Ringkasan</h1></div></div>
            <div class="flex shrink-0 items-center gap-2"><button onclick="openUserModal()" class="rounded bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white hover:bg-slate-800">Tambah anggota</button></div>
        </header>

        <main class="max-w-full overflow-x-hidden p-4 sm:p-8 lg:p-10">
            @if(session('success'))<div class="mb-6 flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800"><span>{{ session('success') }}</span><button onclick="this.parentElement.remove()">×</button></div>@endif
            @if(session('error'))<div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800"><strong>Periksa kembali data:</strong><ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @if(session('onboarding'))
                <div class="mb-8 rounded-2xl border border-slate-200 bg-slate-100 p-6 sm:p-8">
                    <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-center">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-[.18em] text-slate-500">Mulai Dari Sini</span>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Workspace Berhasil Dibuat</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Tidak perlu login lagi. Buat lokasi absensi, lalu bagikan kode perusahaan atau link pendaftaran kepada tim.</p>
                            <div class="mt-5 flex flex-wrap gap-3 text-xs font-semibold text-slate-600"><span class="rounded-full bg-white border border-slate-200 px-3 py-2">1 · Buat aturan absensi</span><span class="rounded-full bg-white border border-slate-200 px-3 py-2">2 · Bagikan kode: {{ $organization->company_code }}</span><span class="rounded-full bg-white border border-slate-200 px-3 py-2">3 · Setujui pendaftaran</span></div>
                        </div>
                        <a href="{{ route('admin.policies') }}" class="shrink-0 rounded-xl bg-slate-900 px-5 py-3 text-xs font-semibold text-white hover:bg-slate-800 text-center">Atur Tipe Absensi</a>
                    </div>
                </div>
            @endif

            <section id="view-overview" class="view active">
                <div class="mb-6 overflow-hidden rounded-2xl bg-slate-900 p-6 text-white sm:p-8">
                    <div class="relative z-10 flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded border border-white/20 bg-white/10 px-2.5 py-1 text-xs font-normal text-slate-200">{{ now()->translatedFormat('l, d F Y') }}</span>
                                <span class="inline-flex items-center rounded border border-slate-700 bg-slate-800 px-2.5 py-1 text-xs font-medium text-slate-200 capitalize">
                                    Paket {{ $organization->plan_details->name }}
                                </span>
                            </div>
                            <h2 class="mt-4 max-w-2xl text-2xl font-bold tracking-tight sm:text-3xl">Ringkasan Workspace {{ $organization->name }}</h2>
                            <p class="mt-2 max-w-xl text-sm text-slate-400">Pantau kesiapan workspace, tim, dan kehadiran anggota dari satu tempat.</p>
                        </div>
                        <button onclick="openUserModal()" class="shrink-0 rounded-xl bg-white px-4 py-2.5 text-xs font-bold text-slate-900 hover:bg-slate-100">Tambah Anggota</button>
                    </div>
                </div>

                <!-- Plan Usage Summary Banner -->
                <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-900 text-sm sm:text-base">Penggunaan Kuota Pengguna</h3>
                                <span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700 capitalize">{{ $organization->plan_details->name }}</span>
                            </div>
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $stats['anggota'] }} dari {{ $organization->plan_details->max_users }} kuota akun anggota telah digunakan.
                            </p>
                        </div>
                        <div class="flex items-center gap-4 sm:justify-end">
                            <div class="text-right min-w-[90px]">
                                <span class="block text-xl font-bold text-slate-900">{{ $stats['anggota'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $organization->plan_details->max_users }}</span></span>
                                <span class="block text-[11px] text-slate-500">Terpakai</span>
                            </div>
                            <a href="{{ route('admin.billing') }}" class="shrink-0 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                Kelola Paket
                            </a>
                        </div>
                    </div>
                    @php
                        $percentage = min(100, round(($stats['anggota'] / max(1, $organization->plan_details->max_users)) * 100));
                    @endphp
                    <div class="mt-3.5 h-2 w-full overflow-hidden rounded-full bg-slate-100 border border-slate-200">
                        <div class="h-full rounded-full transition-all duration-300 {{ $percentage >= 90 ? 'bg-rose-600' : ($percentage >= 75 ? 'bg-amber-600' : 'bg-slate-900') }}" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach([
                        ['label'=>'Total Anggota','value'=>$stats['anggota'],'meta'=>$stats['anggota'].' / '.$organization->plan_details->max_users.' kuota terpakai'],
                        ['label'=>'Hadir Hari Ini','value'=>$stats['hadir_hari_ini'],'meta'=>'Check-in tercatat'],
                        ['label'=>'Tim Pengelola','value'=>$stats['tim_admin'],'meta'=>'Admin, operator, & atasan'],
                        ['label'=>'Lokasi Aktif','value'=>$stats['lokasi'],'meta'=>'Area lokasi geofence']
                    ] as $stat)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">{{ $stat['label'] }}</p>
                        <strong class="mt-3 block text-2xl font-bold text-slate-900">{{ number_format($stat['value']) }}</strong>
                        <p class="mt-1 text-xs text-slate-400">{{ $stat['meta'] }}</p>
                    </article>
                    @endforeach
                </div>
            </section>

            <section id="view-people" class="view">
                <div class="mb-5 flex flex-col justify-between gap-4 border-b border-slate-300 pb-4 sm:flex-row sm:items-end"><div><h2 class="text-xl font-semibold">Pengguna dan akses</h2><p class="mt-1 text-sm text-slate-600">Kelola anggota, atasan, operator, dan admin workspace.</p><p class="mt-1 text-xs text-slate-500">Format ID masuk: username@{{ $organization->slug }}{{ $organization->plan_details->has_custom_suffix ? '' : '.mm' }}</p></div><button onclick="openUserModal()" class="rounded bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Tambah pengguna</button></div>
                <div class="mb-5 grid gap-4 lg:grid-cols-2">
                    <article class="rounded border border-slate-300 bg-white p-5">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kode perusahaan</span>
                        <div class="mt-3 flex items-center justify-between gap-3 border border-slate-200 bg-slate-50 p-3"><code class="text-lg font-bold tracking-wider text-slate-900">{{ $organization->company_code }}</code><button type="button" data-copy="{{ $organization->company_code }}" class="rounded border border-slate-300 bg-white px-3 py-2 text-xs font-semibold">Salin kode</button></div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Dipakai anggota saat mendaftar secara mandiri.</p>
                    </article>
                    <article class="rounded border border-slate-300 bg-white p-5">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Link pendaftaran anggota</span>
                        <p class="mt-2 text-sm text-slate-600">Kode perusahaan sudah terisi otomatis. Akun baru tetap harus Anda setujui.</p>
                        <div class="mt-4 flex flex-col gap-3 sm:flex-row"><div class="min-w-0 flex-1 truncate border border-slate-200 bg-slate-50 px-3 py-3 text-xs text-slate-600">{{ route('members.register.organization', $organization) }}</div><button type="button" data-copy="{{ route('members.register.organization', $organization) }}" class="shrink-0 rounded border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold">Salin link</button></div>
                    </article>
                </div>
                @if($pendingUsers->isNotEmpty())
                    <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
                        <div class="mb-4"><h3 class="font-semibold text-amber-950">Menunggu persetujuan ({{ $pendingUsers->count() }})</h3><p class="mt-1 text-sm text-amber-800">Lengkapi profil dan lokasi anggota sebelum menyetujui aksesnya.</p></div>
                        <div class="space-y-3">
                            @foreach($pendingUsers as $pending)
                                <form method="POST" action="{{ route('admin.registrations.approve', $pending) }}" class="approval-form grid gap-3 rounded-xl border border-amber-200 bg-white p-4 md:grid-cols-2 xl:grid-cols-3 xl:items-end">
                                    @csrf
                                    <label><span class="mb-1.5 block text-xs font-semibold text-slate-600">Nama lengkap · {{ $pending->username }}</span><input name="nama_lengkap" required value="{{ $pending->username }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label>
                                    <label><span class="mb-1.5 block text-xs font-semibold text-slate-600">Bagian / asal</span><input name="asal_pkl" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm" placeholder="Opsional"></label>
                                    <label><span class="mb-1.5 block text-xs font-semibold text-slate-600">Tim</span><select name="team_id" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Belum ada tim</option>@foreach($teams as $team)<option value="{{ $team->id }}" @selected((string)$pending->requested_team_id===(string)$team->id)>{{ $team->name }}</option>@endforeach</select>@if($pending->requestedTeam)<span class="mt-1 block text-xs text-indigo-600">Pilihan pendaftar: {{ $pending->requestedTeam->name }}</span>@endif</label>
                                    <label><span class="mb-1.5 block text-xs font-semibold text-slate-600">Aturan absensi</span><select name="attendance_type_id" required class="attendance-type-select h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Pilih aturan</option>@foreach($attendanceTypes as $type)<option value="{{ $type->id }}" data-mode="{{ $type->mode }}">{{ $type->name }}</option>@endforeach</select></label>
                                    <label><span class="mb-1.5 block text-xs font-semibold text-slate-600">Lokasi pembanding (opsional)</span><select name="gedung_id" class="attendance-location h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Hanya rekam GPS aktual</option>@foreach($gedungs as $g)<option value="{{ $g->id }}">{{ $g->nama }}</option>@endforeach</select></label>
                                    <div class="flex gap-2"><button class="h-11 rounded-xl bg-emerald-600 px-4 text-sm font-semibold text-white">Setujui</button><button type="submit" form="reject-{{ $pending->id }}" class="h-11 rounded-xl px-3 text-sm font-semibold text-rose-600 hover:bg-rose-50">Tolak</button></div>
                                </form>
                                <form id="reject-{{ $pending->id }}" method="POST" action="{{ route('admin.registrations.reject', $pending) }}" onsubmit="return confirm('Tolak pendaftaran {{ $pending->username }}?')">@csrf</form>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap gap-2" id="roleFilters">
                            <button data-role="all" class="role-filter rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Semua</button>
                            <button data-role="siswa" class="role-filter rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100">Anggota</button>
                            <button data-role="guru" class="role-filter rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100">Atasan</button>
                            <button data-role="pegawai" class="role-filter rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100">Operator</button>
                            <button data-role="admin" class="role-filter rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100">Admin</button>
                        </div>
                        <input id="peopleSearch" class="h-10 w-full min-w-0 rounded-xl border border-slate-200 px-4 text-sm outline-none focus:border-indigo-500 sm:max-w-xs" placeholder="Cari anggota tim…">
                    </div>
                    <div id="peopleTable" class="divide-y divide-slate-100">
                    @php $allUsers=$siswaUsers->concat($guruUsers)->concat($pegawaiUsers)->concat($adminUsers); @endphp
                    @forelse($allUsers as $person)
                        @php
                            $profile=$person->siswaProfile ?? $person->pegawaiProfile;
                            $roleLabel=['siswa'=>'Anggota','guru'=>'Atasan','pegawai'=>'Operator','admin'=>'Admin'][$person->role];
                            $scope=$person->role==='siswa'
                                ? ($person->siswaProfile?->team?->name ?? 'Belum ada tim')
                                : ($person->role==='guru'
                                    ? ($person->supervisedTeams->pluck('name')->join(', ') ?: 'Belum memegang tim')
                                    : ($profile?->gedung?->nama ?? 'Seluruh workspace'));
                            $personDetail=[
                                'name'=>$profile?->nama_lengkap ?? $person->username,
                                'identity'=>$person->login_identity,
                                'role'=>$roleLabel,
                                'attendance'=>$person->role==='siswa' ? ($person->siswaProfile?->attendanceType?->name ?? 'Belum diatur') : null,
                                'scope'=>$scope,
                                'origin'=>$person->siswaProfile?->asal_pkl,
                                'canDelete'=>!$person->is(auth()->user()),
                            ];
                        @endphp
                        <article data-person-row data-role="{{ $person->role }}" data-search="{{ strtolower($person->username.' '.($profile?->nama_lengkap ?? '')) }}" data-detail='@json($personDetail)' class="flex flex-col gap-3 p-4 md:flex-row md:items-center md:justify-between sm:px-5">
                            <div class="flex items-center gap-3 min-w-0 md:w-1/3">
                                <div class="min-w-0 flex-1">
                                    <strong class="block truncate font-medium text-slate-900">{{ $profile?->nama_lengkap ?? $person->username }}</strong>
                                    <div class="mt-0.5 flex flex-wrap items-center gap-1.5">
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 md:hidden">{{ $roleLabel }}</span>
                                        <span class="truncate text-xs text-slate-400">{{ $person->login_identity }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Inline Desktop Columns (Visible on md and up) -->
                            <div class="hidden md:flex md:items-center md:gap-4 md:w-1/2 text-xs text-slate-600">
                                <div class="w-1/4">
                                    <span class="border border-slate-300 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $roleLabel }}</span>
                                </div>
                                <div class="w-2/4 truncate">
                                    <span class="font-medium text-slate-800">{{ $scope }}</span>
                                    @if($person->siswaProfile?->asal_pkl)
                                        <span class="block text-[11px] text-slate-400 truncate">{{ $person->siswaProfile->asal_pkl }}</span>
                                    @endif
                                </div>
                                <div class="w-1/4 truncate text-slate-500">
                                    {{ $person->role==='siswa' ? ($person->siswaProfile?->attendanceType?->name ?? '-') : '-' }}
                                </div>
                            </div>

                            <!-- Actions & Detail Button -->
                            <div class="flex items-center justify-end gap-2 shrink-0">
                                <button type="button" class="person-detail shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold hover:border-indigo-300 md:hidden">Detail</button>
                                <button type="button" class="desktop-edit-person hidden md:inline-flex shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold hover:bg-slate-50">Edit</button>
                                @if(!$person->is(auth()->user()))
                                    <button type="button" onclick="this.nextElementSibling.requestSubmit()" class="hidden md:inline-flex shrink-0 rounded-lg border border-rose-100 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                                @endif
                                <button type="button" hidden class="edit-person" data-url="{{ route('admin.users.update',$person) }}" data-username="{{ $person->username }}" data-role="{{ $person->role }}" data-name="{{ $profile?->nama_lengkap }}" data-origin="{{ $person->siswaProfile?->asal_pkl }}" data-team="{{ $person->siswaProfile?->team_id }}" data-location="{{ $profile?->gedung_id }}" data-attendance-type="{{ $person->siswaProfile?->attendance_type_id }}" data-supervised-teams='@json($person->role === "guru" ? $person->supervisedTeams->pluck("id")->values() : [])'></button>
                                @if(!$person->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.users.delete',$person) }}" class="hidden person-delete-form" onsubmit="return confirm('Hapus pengguna ini? Data kehadirannya juga akan terhapus.')">@csrf @method('DELETE')</form>
                                @endif
                            </div>
                        </article>
                    @empty
                        <p class="px-5 py-16 text-center text-slate-400">Belum ada pengguna di workspace ini.</p>
                    @endforelse
                    </div>
                </div>
            </section>

            <section id="view-teams" class="view">
                <div class="mb-6 border-b border-slate-200 pb-4">
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">Manajemen Tim</h2>
                    <p class="mt-1 text-xs text-slate-500">Daftar tim kerja, atasan penanggung jawab, dan anggota tim yang terdaftar.</p>
                </div>
                
                <div class="grid gap-6 xl:grid-cols-[280px_1fr]">
                    <!-- Form Tambah Tim -->
                    <form method="POST" action="{{ route('admin.teams.store') }}" class="h-fit border border-slate-300 bg-white p-5 rounded-lg shadow-sm">
                        @csrf
                        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Buat Tim Baru</h3>
                        
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">Nama Tim</label>
                                <input name="name" required maxlength="80" class="h-10 w-full rounded border border-slate-300 px-3 text-xs outline-none focus:border-slate-500" placeholder="Contoh: Marketing, IT Support">
                            </div>
                            
                            <div>
                                <label class="mb-1 block text-xs font-semibold text-slate-700">Keterangan <span class="text-slate-400 font-normal">(opsional)</span></label>
                                <textarea name="description" maxlength="255" class="min-h-20 w-full rounded border border-slate-300 p-3 text-xs outline-none focus:border-slate-500" placeholder="Keterangan tugas tim"></textarea>
                            </div>
                            
                            <button class="w-full rounded bg-slate-900 hover:bg-slate-800 py-2.5 text-xs font-bold text-white transition-colors">
                                Simpan Tim Baru
                            </button>
                        </div>
                    </form>

                    <!-- Daftar Tim -->
                    <div class="space-y-4">
                        @forelse($teams as $team)
                            <article class="border border-slate-300 bg-white p-5 rounded-lg shadow-sm">
                                <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                                    <div>
                                        <h3 class="font-bold text-slate-950 text-sm">{{ $team->name }}</h3>
                                        <p class="mt-1 text-xs text-slate-500">{{ $team->description ?: 'Tidak ada deskripsi' }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.teams.destroy',$team) }}" onsubmit="return confirm('Hapus tim {{ $team->name }}? Anggota tidak ikut terhapus.')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs font-bold text-rose-600 hover:underline">Hapus Tim</button>
                                    </form>
                                </div>
                                
                                <div class="mt-3 grid gap-4 sm:grid-cols-2 text-xs">
                                    <div>
                                        <h4 class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Atasan Pemantau</h4>
                                        <div class="mt-1">
                                            @php $supervisors = $team->supervisors; @endphp
                                            @if($supervisors->isNotEmpty())
                                                <p class="text-slate-900 font-semibold">{{ $supervisors->pluck('username')->join(', ') }}</p>
                                            @else
                                                <p class="text-slate-400 italic">Belum ada atasan pemantau</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Daftar Anggota Tim ({{ $team->members->count() }})</h4>
                                        <div class="mt-1">
                                            @php $members = $team->members; @endphp
                                            @if($members->isNotEmpty())
                                                <p class="text-slate-900">{{ $members->pluck('nama_lengkap')->join(', ') }}</p>
                                            @else
                                                <p class="text-slate-400 italic">Belum ada anggota tim</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="border border-dashed border-slate-300 bg-white py-12 text-center rounded-lg">
                                <p class="text-xs text-slate-500">Belum ada tim yang ditambahkan. Silakan gunakan form di samping untuk membuat tim baru.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section id="view-locations" class="view">
                <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><h2 class="text-2xl font-semibold tracking-tight">Lokasi kerja</h2><p class="mt-1 text-sm text-slate-500">Tentukan titik dan radius GPS yang dianggap bekerja di lokasi.</p></div><button onclick="document.getElementById('locationModal').showModal()" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white">+ Tambah lokasi</button></div>
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">@forelse($gedungs as $location)<article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-start justify-between"><span class="grid h-12 w-12 place-items-center rounded-2xl bg-cyan-50 text-xl text-cyan-700">⌖</span><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span></div><h3 class="mt-5 font-semibold">{{ $location->nama }}</h3><p class="mt-1 text-xs text-slate-400">{{ $location->latitude }}, {{ $location->longitude }}</p><div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4"><span class="text-sm text-slate-500">Radius <strong class="text-slate-900">{{ $location->radius_meter }} m</strong></span><div class="flex gap-2"><button class="edit-location text-xs font-semibold text-indigo-600" data-url="{{ route('admin.gedungs.update',$location) }}" data-name="{{ $location->nama }}" data-lat="{{ $location->latitude }}" data-long="{{ $location->longitude }}" data-radius="{{ $location->radius_meter }}">Edit</button><form method="POST" action="{{ route('admin.gedungs.destroy',$location) }}" onsubmit="return confirm('Hapus lokasi ini?')">@csrf @method('DELETE')<button class="text-xs font-semibold text-rose-600">Delete</button></form></div></div></article>@empty<div class="col-span-full rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-center"><div class="text-3xl">⌖</div><h3 class="mt-3 font-semibold">Belum ada lokasi pembanding</h3><p class="mt-1 text-sm text-slate-500">GPS absensi tetap tercatat. Tambahkan lokasi jika ingin melihat jarak anggota dari kantor.</p><button onclick="document.getElementById('locationModal').showModal()" class="mt-5 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white">Add first location</button></div>@endforelse</div>
            </section>

        </main>

        <dialog id="personDetailModal" class="m-auto w-[calc(100%-2rem)] max-w-md rounded-[1.5rem] bg-white p-0 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h3 class="font-semibold">Detail anggota</h3>
                <button type="button" onclick="personDetailModal.close()" class="text-2xl leading-none text-slate-400">×</button>
            </div>
        <div class="space-y-3 px-5 py-5 text-sm">
            <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nama</p><p id="personDetailName" class="mt-1 font-medium text-slate-900"></p></div>
            <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">ID masuk</p><p id="personDetailIdentity" class="mt-1 break-all text-slate-600"></p></div>
            <div class="grid grid-cols-2 gap-3">
                <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Peran</p><p id="personDetailRole" class="mt-1 text-slate-600"></p></div>
                <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Status</p><p class="mt-1 text-emerald-600">● Aktif</p></div>
            </div>
            <div id="personDetailAttendanceWrap"><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Aturan absensi</p><p id="personDetailAttendance" class="mt-1 text-slate-600"></p></div>
            <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tim / lokasi</p><p id="personDetailScope" class="mt-1 text-slate-600"></p></div>
            <div id="personDetailOriginWrap"><p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Bagian / asal</p><p id="personDetailOrigin" class="mt-1 text-slate-600"></p></div>
        </div>
        <div class="flex flex-wrap justify-end gap-2 border-t border-slate-200 px-5 py-4">
            <button type="button" id="personDetailDelete" class="mr-auto rounded-xl px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
            <button type="button" onclick="personDetailModal.close()" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold">Tutup</button>
            <button type="button" id="personDetailEdit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Edit</button>
        </div>
    </dialog>

    <dialog id="userModal" class="m-auto w-[calc(100%-2rem)] max-w-2xl rounded-[1.5rem] bg-white p-0 shadow-2xl">
        <form method="POST" action="{{ route('admin.users.store') }}">@csrf
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5"><div><h3 class="font-semibold">Tambah pengguna</h3><p class="text-sm text-slate-500">Buat akses di {{ $organization->name }}</p></div><button type="button" onclick="userModal.close()" class="text-2xl text-slate-400">×</button></div>
            <div class="max-h-[70vh] space-y-5 overflow-y-auto p-6">
                <div class="grid gap-4 sm:grid-cols-2"><label><span class="mb-2 block text-sm font-medium">Username</span><input name="username" value="{{ old('username') }}" required class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><label><span class="mb-2 block text-sm font-medium">Password sementara</span><input type="password" name="password" required minlength="8" class="h-11 w-full rounded-xl border border-slate-300 px-4"></label></div>
                <label><span class="mb-2 block text-sm font-medium">Peran akses</span><select id="newRole" name="role" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4"><option value="siswa">Anggota — dapat melakukan absensi</option><option value="guru">Atasan — memantau anggota tim tertentu</option><option value="pegawai">Operator — memantau satu lokasi</option><option value="admin">Admin — akses penuh workspace</option></select></label>
                <div id="memberFields" class="grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2"><label class="sm:col-span-2"><span class="mb-2 block text-sm font-medium">Nama lengkap</span><input name="nama_lengkap_siswa" class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><label><span class="mb-2 block text-sm font-medium">Tim</span><select name="team_id" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4"><option value="">Belum ada tim</option>@foreach($teams as $team)<option value="{{ $team->id }}">{{ $team->name }}</option>@endforeach</select></label><label><span class="mb-2 block text-sm font-medium">Bagian / asal</span><input name="asal_pkl" class="h-11 w-full rounded-xl border border-slate-300 px-4" placeholder="Contoh: Marketing, Kelas XII IPA"></label><label><span class="mb-2 block text-sm font-medium">Tipe absensi</span><select name="attendance_type_id" class="attendance-type-select h-11 w-full rounded-xl border border-slate-300 bg-white px-4"><option value="">Pilih tipe</option>@foreach($attendanceTypes as $type)<option value="{{ $type->id }}" data-mode="{{ $type->mode }}">{{ $type->name }}</option>@endforeach</select></label><label><span class="mb-2 block text-sm font-medium">Lokasi kerja</span><select name="gedung_id_siswa" class="attendance-location h-11 w-full rounded-xl border border-slate-300 bg-white px-4"><option value="">Hanya rekam GPS aktual</option>@foreach($gedungs as $g)<option value="{{ $g->id }}">{{ $g->nama }}</option>@endforeach</select></label></div>
                <div id="operatorFields" class="hidden grid gap-4 rounded-xl bg-slate-50 p-4 sm:grid-cols-2"><label><span class="mb-2 block text-sm font-medium">Nama lengkap</span><input name="nama_lengkap_pegawai" disabled class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><label><span class="mb-2 block text-sm font-medium">Lokasi yang dipantau</span><select name="gedung_id_pegawai" disabled class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4"><option value="">Pilih lokasi</option>@foreach($gedungs as $g)<option value="{{ $g->id }}">{{ $g->nama }}</option>@endforeach</select></label></div>
                <div id="supervisorFields" class="hidden rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4"><div class="mb-3"><span class="block text-sm font-semibold text-slate-800">Tim yang dipantau</span><span class="mt-1 block text-xs text-slate-500">Pilih satu atau beberapa tim untuk Atasan ini.</span></div><div class="grid gap-3 sm:grid-cols-2">@forelse($teams as $team)<label class="group cursor-pointer"><input type="checkbox" name="team_ids[]" value="{{ $team->id }}" disabled class="peer sr-only"><span class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4 transition hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:ring-2 peer-checked:ring-indigo-100"><span class="min-w-0"><strong class="block truncate text-sm text-slate-800">{{ $team->name }}</strong><small class="mt-1 block text-xs text-slate-400">{{ $team->members->count() }} anggota</small></span><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full border border-slate-300 text-xs text-transparent transition peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white">✓</span></span></label>@empty<div class="sm:col-span-2 rounded-xl border border-dashed border-slate-300 bg-white p-5 text-center text-sm text-slate-500">Belum ada Tim. Buat Tim terlebih dahulu dari menu Tim.</div>@endforelse</div><p class="mt-3 text-xs leading-5 text-slate-500">Atasan otomatis melihat seluruh anggota Tim, termasuk anggota yang ditambahkan nanti.</p></div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4"><button type="button" onclick="userModal.close()" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold">Batal</button><button class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white">Buat akses</button></div>
        </form>
    </dialog>

    <dialog id="editUserModal" class="m-auto w-[calc(100%-2rem)] max-w-2xl rounded-[1.5rem] bg-white p-0 shadow-2xl">
        <form id="editUserForm" method="POST">@csrf @method('PUT')<input type="hidden" name="role" id="editRole">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5"><div><h3 id="editUserTitle" class="font-semibold">Edit pengguna</h3><p id="editUserHint" class="text-sm text-slate-500">Perbarui akses pengguna</p></div><button type="button" onclick="editUserModal.close()" class="text-2xl text-slate-400">×</button></div>
            <div class="max-h-[70vh] space-y-4 overflow-y-auto p-6"><label><span class="mb-2 block text-sm font-medium">Username</span><input id="editUsername" name="username" required class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><label><span class="mb-2 block text-sm font-medium">Password baru <small class="text-slate-400">(opsional)</small></span><input type="password" name="password" minlength="8" class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><div id="editProfileFields" class="hidden space-y-4"><label><span class="mb-2 block text-sm font-medium">Nama lengkap</span><input id="editName" name="nama_lengkap" class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><label id="editTeamWrap"><span class="mb-2 block text-sm font-medium">Tim</span><select id="editTeam" name="team_id" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-4"><option value="">Belum ada tim</option>@foreach($teams as $team)<option value="{{ $team->id }}">{{ $team->name }}</option>@endforeach</select></label><label id="editOriginWrap"><span class="mb-2 block text-sm font-medium">Bagian / asal</span><input id="editOrigin" name="asal_pkl" class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><label id="editAttendanceTypeWrap"><span class="mb-2 block text-sm font-medium">Tipe absensi</span><select id="editAttendanceType" name="attendance_type_id" class="attendance-type-select h-11 w-full rounded-xl border border-slate-300 bg-white px-4"><option value="">Pilih tipe</option>@foreach($attendanceTypes as $type)<option value="{{ $type->id }}" data-mode="{{ $type->mode }}">{{ $type->name }}</option>@endforeach</select></label><label><span class="mb-2 block text-sm font-medium">Lokasi</span><select id="editLocation" name="gedung_id" class="attendance-location h-11 w-full rounded-xl border border-slate-300 bg-white px-4"><option value="">Hanya rekam GPS aktual</option>@foreach($gedungs as $g)<option value="{{ $g->id }}">{{ $g->nama }}</option>@endforeach</select></label></div><div id="editSupervisorFields" class="hidden rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4"><div class="mb-3"><span class="block text-sm font-semibold text-slate-800">Tim yang dipantau</span><span class="mt-1 block text-xs text-slate-500">Klik kartu Tim untuk memilih atau membatalkan pilihan.</span></div><div class="grid gap-3 sm:grid-cols-2">@forelse($teams as $team)<label class="group cursor-pointer"><input type="checkbox" name="team_ids[]" value="{{ $team->id }}" disabled class="edit-team-checkbox peer sr-only"><span class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4 transition hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:ring-2 peer-checked:ring-indigo-100"><span class="min-w-0"><strong class="block truncate text-sm text-slate-800">{{ $team->name }}</strong><small class="mt-1 block text-xs text-slate-400">{{ $team->members->count() }} anggota</small></span><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full border border-slate-300 text-xs text-transparent transition peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white">✓</span></span></label>@empty<div class="sm:col-span-2 rounded-xl border border-dashed border-slate-300 bg-white p-5 text-center text-sm text-slate-500">Belum ada Tim yang dapat dipilih.</div>@endforelse</div><p class="mt-3 text-xs leading-5 text-slate-500">Atasan hanya melihat anggota dari Tim yang dipilih.</p></div></div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4"><button type="button" onclick="editUserModal.close()" class="rounded-xl border px-4 py-2 text-sm font-semibold">Batal</button><button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Simpan perubahan</button></div>
        </form>
    </dialog>

    <dialog id="locationModal" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-[1.5rem] bg-white p-0 shadow-2xl"><form method="POST" action="{{ route('admin.gedungs.store') }}">@csrf<div class="flex items-center justify-between border-b px-6 py-5"><h3 class="font-semibold">Add work location</h3><button type="button" onclick="locationModal.close()" class="text-2xl text-slate-400">×</button></div><div class="space-y-4 p-6"><label><span class="mb-2 block text-sm font-medium">Location name</span><input name="nama" required class="h-11 w-full rounded-xl border border-slate-300 px-4" placeholder="Head Office Jakarta"></label><div class="grid grid-cols-2 gap-4"><label><span class="mb-2 block text-sm font-medium">Latitude</span><input name="latitude" required class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><label><span class="mb-2 block text-sm font-medium">Longitude</span><input name="longitude" required class="h-11 w-full rounded-xl border border-slate-300 px-4"></label></div><button type="button" data-gps class="w-full rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700">⌖ Use my current position</button><label><span class="mb-2 block text-sm font-medium">Allowed radius (meter)</span><input type="number" name="radius_meter" value="50" min="5" max="5000" required class="h-11 w-full rounded-xl border border-slate-300 px-4"></label></div><div class="flex justify-end gap-3 border-t px-6 py-4"><button type="button" onclick="locationModal.close()" class="rounded-xl border px-4 py-2 text-sm font-semibold">Cancel</button><button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Save location</button></div></form></dialog>

    <dialog id="editLocationModal" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-[1.5rem] bg-white p-0 shadow-2xl"><form id="editLocationForm" method="POST">@csrf @method('PUT')<div class="flex items-center justify-between border-b px-6 py-5"><h3 class="font-semibold">Edit work location</h3><button type="button" onclick="editLocationModal.close()" class="text-2xl text-slate-400">×</button></div><div class="space-y-4 p-6"><label><span class="mb-2 block text-sm font-medium">Location name</span><input id="locationName" name="nama" required class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><div class="grid grid-cols-2 gap-4"><label><span class="mb-2 block text-sm font-medium">Latitude</span><input id="locationLat" name="latitude" required class="h-11 w-full rounded-xl border border-slate-300 px-4"></label><label><span class="mb-2 block text-sm font-medium">Longitude</span><input id="locationLong" name="longitude" required class="h-11 w-full rounded-xl border border-slate-300 px-4"></label></div><button type="button" data-gps class="w-full rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700">⌖ Use my current position</button><label><span class="mb-2 block text-sm font-medium">Allowed radius (meter)</span><input id="locationRadius" type="number" name="radius_meter" min="5" max="5000" required class="h-11 w-full rounded-xl border border-slate-300 px-4"></label></div><div class="flex justify-end gap-3 border-t px-6 py-4"><button type="button" onclick="editLocationModal.close()" class="rounded-xl border px-4 py-2 text-sm font-semibold">Cancel</button><button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Save changes</button></div></form></dialog>

    <script>
        const titles={overview:['Ringkasan workspace','Ringkasan'],people:['Manajemen pengguna','Pengguna & akses'],teams:['Manajemen organisasi','Tim'],locations:['Pengaturan lokasi','Lokasi']};
        
        // Handle URL search parameter views redirection
        const urlParams = new URLSearchParams(window.location.search);
        const viewParam = urlParams.get('view');
        if (viewParam && titles[viewParam]) {
            localStorage.setItem('adminView', viewParam);
        }

        function showView(name){document.querySelectorAll('.view').forEach(v=>v.classList.toggle('active',v.id===`view-${name}`));document.querySelectorAll('.nav-item').forEach(v=>v.classList.toggle('active',v.dataset.view===name));document.getElementById('eyebrow').textContent=titles[name][0];document.getElementById('pageTitle').textContent=titles[name][1];localStorage.setItem('adminView',name);sidebar.classList.add('-translate-x-full');overlay.classList.add('hidden')}
        document.querySelectorAll('[data-view]').forEach(b=>b.addEventListener('click',()=>showView(b.dataset.view)));document.querySelectorAll('[data-view-link]').forEach(b=>b.addEventListener('click',()=>showView(b.dataset.viewLink)));
        const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('overlay'),menuButton=document.getElementById('menuButton');menuButton.addEventListener('click',()=>{sidebar.classList.remove('-translate-x-full');overlay.classList.remove('hidden')});overlay.addEventListener('click',()=>{sidebar.classList.add('-translate-x-full');overlay.classList.add('hidden')});
        const userModal=document.getElementById('userModal'),newRole=document.getElementById('newRole'),editUserModal=document.getElementById('editUserModal'),editUserForm=document.getElementById('editUserForm'),editUsername=document.getElementById('editUsername'),editRole=document.getElementById('editRole'),editProfileFields=document.getElementById('editProfileFields'),editSupervisorFields=document.getElementById('editSupervisorFields'),editTeamCheckboxes=document.querySelectorAll('.edit-team-checkbox'),editTeam=document.getElementById('editTeam'),editTeamWrap=document.getElementById('editTeamWrap'),editUserTitle=document.getElementById('editUserTitle'),editUserHint=document.getElementById('editUserHint'),editName=document.getElementById('editName'),editOrigin=document.getElementById('editOrigin'),editOriginWrap=document.getElementById('editOriginWrap'),editLocation=document.getElementById('editLocation'),editAttendanceType=document.getElementById('editAttendanceType'),editAttendanceTypeWrap=document.getElementById('editAttendanceTypeWrap'),editLocationModal=document.getElementById('editLocationModal'),editLocationForm=document.getElementById('editLocationForm'),locationName=document.getElementById('locationName'),locationLat=document.getElementById('locationLat'),locationLong=document.getElementById('locationLong'),locationRadius=document.getElementById('locationRadius'),peopleSearch=document.getElementById('peopleSearch');
        function openUserModal(){userModal.showModal()}
        function syncRoleFields(){const role=newRole.value;[['memberFields','siswa'],['operatorFields','pegawai'],['supervisorFields','guru']].forEach(([id,target])=>{const el=document.getElementById(id),on=role===target;el.classList.toggle('hidden',!on);el.querySelectorAll('input,select').forEach(i=>i.disabled=!on)})}newRole.addEventListener('change',syncRoleFields);syncRoleFields();
        document.querySelectorAll('[data-team-picker]').forEach(picker=>picker.addEventListener('change',()=>{if(!picker.value)return;const target=document.getElementById(picker.dataset.target);Array.from(target.options).forEach(option=>{if(option.dataset.team===picker.value)option.selected=true});picker.value=''}));
        document.querySelectorAll('.edit-person').forEach(b=>b.addEventListener('click',()=>{editUserForm.action=b.dataset.url;editUsername.value=b.dataset.username;editRole.value=b.dataset.role;const isSupervisor=b.dataset.role==='guru',hasProfile=['siswa','pegawai'].includes(b.dataset.role);editUserTitle.textContent=isSupervisor?'Atur atasan dan tim':'Edit pengguna';editUserHint.textContent=isSupervisor?'Pilih tim yang boleh dipantau oleh atasan ini.':'Perbarui akses dan profil pengguna.';editProfileFields.classList.toggle('hidden',!hasProfile);editProfileFields.querySelectorAll('input,select').forEach(i=>i.disabled=!hasProfile);editSupervisorFields.classList.toggle('hidden',!isSupervisor);editSupervisorFields.querySelectorAll('input,select').forEach(i=>i.disabled=!isSupervisor);const supervisedTeams=isSupervisor?JSON.parse(b.dataset.supervisedTeams||'[]').map(String):[];editTeamCheckboxes.forEach(input=>input.checked=supervisedTeams.includes(input.value));editName.value=b.dataset.name||'';editOrigin.value=b.dataset.origin||'';editTeam.value=b.dataset.team||'';editTeamWrap.classList.toggle('hidden',b.dataset.role!=='siswa');editOriginWrap.classList.toggle('hidden',b.dataset.role!=='siswa');editAttendanceTypeWrap.classList.toggle('hidden',b.dataset.role!=='siswa');editAttendanceType.disabled=b.dataset.role!=='siswa';editAttendanceType.value=b.dataset.attendanceType||'';editLocation.value=b.dataset.location||'';syncAttendanceLocation(editAttendanceType);editUserModal.showModal()}));
        document.querySelectorAll('.edit-location').forEach(b=>b.addEventListener('click',()=>{editLocationForm.action=b.dataset.url;locationName.value=b.dataset.name;locationLat.value=b.dataset.lat;locationLong.value=b.dataset.long;locationRadius.value=b.dataset.radius;editLocationModal.showModal()}));
        document.querySelectorAll('[data-gps]').forEach(b=>b.addEventListener('click',()=>navigator.geolocation.getCurrentPosition(p=>{const form=b.closest('form');form.querySelector('[name=latitude]').value=p.coords.latitude;form.querySelector('[name=longitude]').value=p.coords.longitude;b.textContent='✓ Position captured'},e=>alert('Lokasi tidak dapat diambil: '+e.message),{enableHighAccuracy:true})));
        document.querySelectorAll('[data-copy]').forEach(button=>button.addEventListener('click',async()=>{await navigator.clipboard.writeText(button.dataset.copy);const label=button.textContent;button.textContent='Tersalin';setTimeout(()=>button.textContent=label,1600)}));
        function syncAttendanceLocation(select){const location=select.closest('form')?.querySelector('.attendance-location');if(location)location.required=false}document.querySelectorAll('.attendance-type-select').forEach(select=>syncAttendanceLocation(select));
        document.querySelectorAll('.attendance-location option[value=""]').forEach(option=>option.textContent='Hanya rekam GPS aktual');

        let activeRole='all';document.querySelectorAll('.role-filter').forEach(b=>b.addEventListener('click',()=>{activeRole=b.dataset.role;document.querySelectorAll('.role-filter').forEach(x=>{x.classList.toggle('bg-slate-900',x===b);x.classList.toggle('text-white',x===b)});filterPeople()}));peopleSearch.addEventListener('input',filterPeople);function filterPeople(){const q=peopleSearch.value.toLowerCase();document.querySelectorAll('[data-person-row]').forEach(r=>r.classList.toggle('hidden',(activeRole!=='all'&&r.dataset.role!==activeRole)||!r.dataset.search.includes(q)))}
        const personDetailModal=document.getElementById('personDetailModal');
        let activePersonCard=null;
        document.querySelectorAll('.person-detail').forEach(button=>button.addEventListener('click',()=>{
            activePersonCard=button.closest('[data-person-row]');
            const data=JSON.parse(activePersonCard.dataset.detail||'{}');
            document.getElementById('personDetailName').textContent=data.name||'—';
            document.getElementById('personDetailIdentity').textContent=data.identity||'—';
            document.getElementById('personDetailRole').textContent=data.role||'—';
            document.getElementById('personDetailScope').textContent=data.scope||'—';
            document.getElementById('personDetailAttendance').textContent=data.attendance||'—';
            document.getElementById('personDetailAttendanceWrap').hidden=!data.attendance;
            document.getElementById('personDetailOrigin').textContent=data.origin||'—';
            document.getElementById('personDetailOriginWrap').hidden=!data.origin;
            document.getElementById('personDetailDelete').hidden=!data.canDelete;
            personDetailModal.showModal();
        }));
        document.getElementById('personDetailEdit').addEventListener('click',()=>{
            if(!activePersonCard)return;
            personDetailModal.close();
            activePersonCard.querySelector('.edit-person')?.click();
        });
        document.getElementById('personDetailDelete').addEventListener('click',()=>{
            if(!activePersonCard)return;
            activePersonCard.querySelector('.person-delete-form')?.requestSubmit();
        });
        document.querySelectorAll('.desktop-edit-person').forEach(b => {
            b.addEventListener('click', () => {
                b.closest('[data-person-row]').querySelector('.edit-person')?.click();
            });
        });
        showView(localStorage.getItem('adminView')&&titles[localStorage.getItem('adminView')]?localStorage.getItem('adminView'):'overview');
        @if($errors->any()) openUserModal(); @endif
    </script>
</body>
</html>
