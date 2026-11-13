<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrasi Platform · MWork</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Arial', 'Tahoma', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c084fc',
                            400: '#a855f7',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                            950: '#0f0f1b',
                        },
                        night: {
                            50: '#f8fafc',
                            900: '#0f172a',
                            955: '#070a13',
                            950: '#0b0f19',
                        }
                    },
                    boxShadow: {
                        soft: '0 12px 40px -4px rgba(15, 23, 42, 0.04)',
                        premium: '0 20px 50px -12px rgba(124, 58, 237, 0.08)',
                        glow: '0 0 20px rgba(139, 92, 246, 0.15)',
                    }
                }
            }
        }
    </script>
    @include('partials.pwa')
    
    <style>
        :root { color-scheme: light; }
        html { background: #f6f7f9; }
        body { background: #f6f7f9 !important; }
        * { scrollbar-color: #8a939f #e5e7eb; scrollbar-width: thin; }
        [class*="rounded-"] { border-radius: 10px !important; }
        [class*="shadow"] { box-shadow: 0 1px 2px rgba(15, 23, 42, .05), 0 8px 24px rgba(15,23,42,.05) !important; }
        [class*="backdrop-blur"] { backdrop-filter: none !important; }
        [class*="bg-gradient"] { background-image: none !important; }
        [class*="animate-"] { animation: none !important; }
        .bg-brand-600, .hover\:bg-brand-700:hover { background-color: #4f46e5 !important; }
        .bg-brand-50 { background-color: #eef2ff !important; }
        .text-brand-600, .text-brand-700, .group:hover .group-hover\:text-brand-600 { color: #4f46e5 !important; }
        .border-brand-200, .border-brand-300, .border-brand-500 { border-color: #c7d2fe !important; }
        .transition-all-300 { transition: background-color .12s ease, border-color .12s ease; }
        input, select, textarea, button { border-radius: 10px !important; }
        input:focus, select:focus, textarea:focus { box-shadow: 0 0 0 3px rgba(79,70,229,.12) !important; border-color: #4f46e5 !important; }
        table thead { background: #e5e7eb !important; color: #334155 !important; }
        table th { border-bottom: 1px solid #94a3b8 !important; }
        table td { border-color: #d5d9df !important; }
        dialog { border-radius: 16px !important; }
        dialog::backdrop { background: rgba(15, 23, 42, .55); backdrop-filter: none; }
        #sidebarDrawer { width: 272px !important; background: #0f172a !important; border-right: 1px solid #1e293b !important; }
        #sidebarDrawer nav a { border: 1px solid transparent; border-radius: 10px !important; }
        #sidebarDrawer nav a:hover { background: #1e293b !important; border-color: #334155; }
        #sidebarDrawer nav a.bg-gradient-to-r { background: #312e81 !important; color: #fff !important; border-color: #4338ca !important; box-shadow: none !important; }
        .classic-panel-title { background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #334155; }
        @media (min-width: 1024px) { .lg\:pl-72 { padding-left: 272px !important; } }
    </style>
    @include('partials.ui-system')
</head>
@php
    $navigation = [
        'overview' => [
            'route' => 'superadmin.dashboard',
            'label' => 'Ringkasan',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>'
        ],
        'organizations' => [
            'route' => 'superadmin.organizations',
            'label' => 'Perusahaan',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 22h20"/><path d="M4 22V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v18"/><path d="M6 12h4"/><path d="M6 16h4"/><path d="M14 12h4"/><path d="M14 16h4"/><path d="M8 8h8"/><path d="M10 22V18h4v4"/></svg>'
        ],
        'users' => [
            'route' => 'superadmin.users',
            'label' => 'Pengguna & Absensi',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'
        ],
        'billing' => [
            'route' => 'superadmin.billing',
            'label' => 'Paket & Pembayaran',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/><path d="M6 14h.01"/><path d="M10 14h.01"/></svg>'
        ],
        'support' => [
            'route' => 'superadmin.support',
            'label' => 'Layanan Pelanggan',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/></svg>'
        ],
        'audit' => [
            'route' => 'superadmin.audit',
            'label' => 'Audit Aktivitas',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M12 6v6l4 2"/></svg>'
        ],
    ];

    $headings = [
        'overview' => ['Administrasi sistem', 'Ringkasan Platform'],
        'organizations' => ['Data utama', 'Daftar Perusahaan'],
        'users' => ['Data pengguna', 'Pengguna dan Absensi'],
        'billing' => ['Administrasi layanan', 'Paket dan Pembayaran'],
        'support' => ['Layanan pelanggan', 'Percakapan Bantuan'],
        'audit' => ['Keamanan sistem', 'Audit Aktivitas'],
    ];
    $heading = $headings[$section] ?? ['Administrasi platform', 'Dashboard'];
@endphp

<body class="classic-admin min-h-screen bg-slate-50 text-slate-900 antialiased font-sans">

<!-- Backdrop for mobile sidebar drawer -->
<div id="sidebarBackdrop" class="fixed inset-0 z-40 hidden bg-slate-900/40 backdrop-blur-sm lg:hidden transition-opacity duration-300 opacity-0" onclick="toggleMobileSidebar()"></div>

<!-- Sidebar Container -->
<aside id="sidebarDrawer" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-night-950 text-slate-300 lg:translate-x-0 transition-transform duration-300 ease-in-out border-r border-slate-900">
    <!-- Sidebar Header -->
    <div class="border-b border-slate-700 bg-slate-950 px-5 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('mworkputih.png') }}" alt="MWork" class="h-9 w-9 object-contain">
            <div>
                <strong class="block text-sm font-bold text-white tracking-wide">MWork Administrator</strong>
                <span class="text-[11px] font-medium text-slate-400">Panel pengelola platform</span>
            </div>
        </div>
        <!-- Close button for mobile -->
        <button class="lg:hidden p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white" onclick="toggleMobileSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/></svg>
        </button>
    </div>
    
    <!-- Sidebar Navigation -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-1.5">
        <p class="px-3 py-2 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500">Menu administrasi</p>
        @foreach($navigation as $key => $item)
            <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ $section === $key ? 'bg-gradient-to-r from-brand-600 to-indigo-600 text-white shadow-md shadow-brand-600/10' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                <span class="w-5 flex items-center justify-center opacity-85">{!! $item['svg'] !!}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="border-t border-slate-800/60 p-4 space-y-3 bg-slate-950/20">
        <div class="border border-slate-700 bg-slate-900 p-3.5">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Superadmin</p>
            <p class="mt-1 truncate text-xs font-semibold text-white">{{ auth()->user()->login_identity }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex w-full items-center justify-center gap-2.5 rounded-xl border border-slate-800 bg-slate-900/40 hover:bg-rose-500/10 hover:border-rose-500/20 hover:text-rose-400 px-4 py-2.5 text-xs font-semibold transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

<!-- Main Page Wrapper -->
<div class="lg:pl-72 flex flex-col min-h-screen">
    
    <!-- Top Navbar Header -->
    <header class="sticky top-0 z-30 border-b border-slate-400 bg-white px-5 py-3 sm:px-8">
        <div class="mx-auto flex max-w-[1500px] items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Hamburger menu button -->
                <button class="lg:hidden p-2 -ml-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors shadow-sm" onclick="toggleMobileSidebar()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-blue-700">{{ $headings[$section][0] ?? 'Administrasi platform' }}</p>
                    <h1 class="mt-0.5 text-lg sm:text-xl font-bold tracking-tight text-slate-900">{{ $headings[$section][1] ?? 'Dashboard' }}</h1>
                </div>
            </div>
            
            <div class="flex items-center gap-3.5">
                <span class="hidden border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 sm:flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Sistem berjalan normal</span>
                </span>
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-900 font-bold text-sm text-white shadow-sm">SA</span>
            </div>
        </div>
    </header>
    
    <!-- Main Dashboard Area -->
    <main class="flex-1 mx-auto w-full max-w-[1500px] p-5 sm:p-8 lg:p-10">
        
        <!-- Alerts notification banner -->
        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/55 p-4 text-sm text-emerald-800 shadow-sm backdrop-blur-md transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <div class="font-medium">{{ session('success') }}</div>
            </div>
        @endif
        
        @if($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50/55 p-4 text-sm text-rose-800 shadow-sm backdrop-blur-md transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                <div class="font-medium">{{ $errors->first() }}</div>
            </div>
        @endif

        <!-- OVERVIEW SECTION -->
        @if($section === 'overview')
            <section class="overflow-hidden border border-slate-400 bg-white text-slate-900">
                <div class="classic-panel-title px-5 py-2 text-xs font-bold uppercase tracking-wider">Status platform</div>
                <div class="flex flex-col justify-between gap-6 p-6 xl:flex-row xl:items-end">
                    <div class="space-y-3 max-w-2xl">
                        <span class="inline-flex items-center gap-1.5 border border-slate-300 bg-slate-100 px-3 py-1.5 text-xs text-slate-600 font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            <span>{{ now()->translatedFormat('l, d F Y · H:i') }} WIB</span>
                        </span>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-900">Pusat administrasi MWork</h2>
                        <p class="text-sm text-slate-600">Pantau perusahaan, pengguna, pembayaran, layanan pelanggan, dan keamanan platform dari satu tempat.</p>
                    </div>
                    <a href="{{ route('superadmin.organizations') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-slate-950 hover:bg-slate-100 active:scale-[0.98] transition-all-300 shadow-lg shadow-white/5">
                        <span>Kelola perusahaan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" x2="19" y1="12" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </section>

            <!-- Metrics Statistics Cards -->
            <section class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @php
                    $statItems = [
                        [
                            'label' => 'Perusahaan',
                            'value' => $stats['organizations'],
                            'sub' => 'Workspace terdaftar',
                            'theme' => 'from-violet-500/10 to-indigo-500/10 text-indigo-600',
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 22h20"/><path d="M4 22V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v18"/><path d="M10 22V18h4v4"/></svg>'
                        ],
                        [
                            'label' => 'Total pengguna',
                            'value' => $stats['users'],
                            'sub' => 'Dari seluruh perusahaan',
                            'theme' => 'from-blue-500/10 to-cyan-500/10 text-blue-600',
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>'
                        ],
                        [
                            'label' => 'Absensi hari ini',
                            'value' => $stats['attendance_today'],
                            'sub' => 'Absensi masuk tercatat',
                            'theme' => 'from-emerald-500/10 to-teal-500/10 text-emerald-600',
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
                        ],
                        [
                            'label' => 'Lokasi aktif',
                            'value' => $stats['locations'],
                            'sub' => 'Geofence terdaftar',
                            'theme' => 'from-amber-500/10 to-orange-500/10 text-amber-600',
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>'
                        ],
                        [
                            'label' => 'Total pendapatan',
                            'value' => 'Rp ' . number_format($stats['revenue'], 0, ',', '.'),
                            'sub' => 'Pembayaran berhasil tercatat',
                            'theme' => 'from-brand-500/10 to-pink-500/10 text-brand-600',
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
                        ],
                        [
                            'label' => 'Bantuan terbuka',
                            'value' => $stats['open_support'],
                            'sub' => 'Percakapan perlu ditangani',
                            'theme' => 'from-rose-500/10 to-red-500/10 text-rose-600',
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>'
                        ]
                    ];
                @endphp
                
                @foreach($statItems as $item)
                    <article class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-soft hover:shadow-md hover:border-slate-300/80 transition-all-300 relative overflow-hidden">
                        <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-gradient-to-br {{ explode(' ', $item['theme'])[0] }} opacity-15 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500 animate-pulse"></div>
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-semibold text-slate-500 tracking-tight">{{ $item['label'] }}</p>
                            <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br {{ $item['theme'] }} shadow-sm">
                                {!! $item['svg'] !!}
                            </span>
                        </div>
                        <strong class="mt-4 block text-3xl font-extrabold tracking-tight text-slate-900">
                            {{ is_numeric($item['value']) ? number_format($item['value']) : $item['value'] }}
                        </strong>
                        <p class="mt-1 text-xs text-slate-400 font-medium">{{ $item['sub'] }}</p>
                    </article>
                @endforeach
            </section>

            <!-- Quick Navigation Shortcuts -->
            <section class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach($navigation as $navKey => $navItem) 
                    @continue($navKey === 'overview') 
                    <a href="{{ route($navItem['route']) }}" class="group flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-soft hover:border-brand-300 hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 transition-all-300">
                        <div class="flex items-center gap-4">
                            <span class="grid h-11 w-11 place-items-center rounded-xl bg-violet-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all-300">
                                {!! $navItem['svg'] !!}
                            </span>
                            <div>
                                <strong class="block text-sm font-bold text-slate-800 transition-colors group-hover:text-brand-600">{{ $navItem['label'] }}</strong>
                                <span class="text-[11px] font-medium text-slate-400">Buka panel pengelolaan</span>
                            </div>
                        </div>
                        <span class="text-slate-400 group-hover:text-brand-600 group-hover:translate-x-1.5 transition-all-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" x2="19" y1="12" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </a> 
                @endforeach
            </section>
        @endif

        <!-- ORGANIZATIONS SECTION -->
        @if($section === 'organizations')
            <section class="space-y-6">
                <!-- Section Header with Search Bar -->
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Data perusahaan</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Perusahaan terdaftar</h2>
                        <p class="mt-1 text-xs text-slate-500 font-medium">Pilih workspace untuk mengelola status, langganan, dan data pembayaran.</p>
                    </div>
                    
                    <form method="GET" action="{{ route('superadmin.organizations') }}" class="flex w-full md:w-auto gap-2">
                        <div class="relative flex-1 md:w-72">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/></svg>
                            </span>
                            <input name="search" value="{{ $search }}" class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 text-sm font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all shadow-sm" placeholder="Cari nama atau kode perusahaan…">
                        </div>
                        <button class="h-11 rounded-xl bg-slate-900 px-5 text-sm font-bold text-white hover:bg-slate-800 active:scale-[0.98] transition-all-300 shadow-sm">Cari</button>
                    </form>
                </div>

                <!-- Organization Grid Cards -->
                <div class="grid gap-5 md:grid-cols-2 2xl:grid-cols-3">
                    @forelse($organizations as $organization)
                        <a href="{{ route('superadmin.organizations', ['organization' => $organization->id]) }}" class="group rounded-2xl border bg-white p-6 shadow-soft hover:shadow-md hover:border-brand-300 transition-all-300 relative {{ $selectedOrganization?->id === $organization->id ? 'border-brand-500 ring-2 ring-brand-500/10' : 'border-slate-200' }}">
                            <div class="flex justify-between items-start">
                                <span class="grid h-12 w-12 place-items-center rounded-xl text-lg font-bold text-white shadow-sm relative overflow-hidden transition-transform group-hover:scale-105 duration-300" style="background: {{ $organization->primary_color }}">
                                    <!-- Dynamic glass glow -->
                                    <span class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></span>
                                    <span>{{ strtoupper(substr($organization->name, 0, 1)) }}</span>
                                </span>
                                
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $organization->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/50' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                    @if($organization->is_active)
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Aktif</span>
                                    @else
                                        <span>Nonaktif</span>
                                    @endif
                                </span>
                            </div>
                            
                            <h3 class="mt-4 truncate text-base font-bold text-slate-800 group-hover:text-brand-600 transition-colors">{{ $organization->name }}</h3>
                            <p class="mt-1 text-xs text-slate-400 font-medium">{{ $organization->slug }} · <span class="capitalize text-slate-500 font-semibold">{{ $organization->type_label }}</span></p>
                            
                            <!-- Stats badges nested inside card -->
                            <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-xs font-semibold text-slate-500">
                                <div class="flex items-center gap-2 rounded-xl bg-slate-50 p-2.5">
                                    <span class="text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    </span>
                                    <span><strong class="text-slate-800 block text-sm font-bold">{{ $organization->users_count }}</strong>pengguna</span>
                                </div>
                                <div class="flex items-center gap-2 rounded-xl bg-slate-50 p-2.5">
                                    <span class="text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    </span>
                                    <span><strong class="text-slate-800 block text-sm font-bold">{{ $organization->gedungs_count }}</strong>lokasi</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-2xl border-2 border-dashed border-slate-200 bg-white py-16 text-center shadow-soft">
                            <span class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-slate-50 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 22h20"/><path d="M4 22V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v18"/></svg>
                            </span>
                            <h3 class="mt-4 text-sm font-bold text-slate-700">Tidak ada organisasi</h3>
                            <p class="mt-1 text-xs text-slate-400">Gunakan kata kunci pencarian lain.</p>
                        </div>
                    @endforelse
                </div>
                
                @if($organizations->hasPages())
                    <div class="mt-6">
                        {{ $organizations->links() }}
                    </div>
                @endif
            </section>

            <!-- Detailed Actions for Selected Organization -->
            @if($selectedOrganization)
                <section class="mt-10 border-t border-slate-200 pt-10">
                    <div class="mb-5">
                        <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-brand-600">
                            <span>Dipilih:</span>
                            <span class="text-slate-800">{{ $selectedOrganization->name }}</span>
                        </span>
                        <h2 class="mt-1 text-xl font-bold text-slate-900">Pengaturan workspace</h2>
                    </div>
                    
                    <div class="grid gap-6 xl:grid-cols-3">
                        
                        <!-- Workspace Control -->
                        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                                    <span class="text-amber-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <span>Status workspace</span>
                                </h3>
                                <p class="mt-1.5 text-xs text-slate-500 font-medium">Tangguhkan akses jika terjadi pelanggaran penggunaan.</p>
                                
                                <form method="POST" action="{{ route('superadmin.organizations.status', $selectedOrganization) }}" class="mt-5 space-y-3.5">
                                    @csrf 
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $selectedOrganization->is_active ? 'suspended' : 'active' }}">
                                    
                                    @if($selectedOrganization->is_active)
                                        <div class="space-y-1">
                                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Alasan Penangguhan</label>
                                            <textarea name="reason" required class="min-h-[100px] w-full rounded-xl border border-slate-200 p-3 text-sm font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all shadow-sm" placeholder="Sebutkan pelanggaran…"></textarea>
                                        </div>
                                        <button class="w-full rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-[0.98] py-3 text-sm font-bold text-white transition-all shadow-sm shadow-amber-500/10">Tangguhkan Workspace</button>
                                    @else
                                        <div class="rounded-xl bg-rose-50 border border-rose-100 p-4 text-xs text-rose-800 font-medium space-y-1">
                                            <span class="block text-[10px] font-bold uppercase text-rose-600 tracking-wider">Alasan Ditangguhkan</span>
                                            <p>{{ $selectedOrganization->suspended_reason }}</p>
                                        </div>
                                        <button class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] py-3 text-sm font-bold text-white transition-all shadow-sm shadow-emerald-600/10">Aktifkan Kembali</button>
                                    @endif
                                </form>
                            </div>
                        </article>

                        <!-- Subscription is read-only: changes happen through a verified payment. -->
                        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft">
                            <h3 class="text-base font-bold text-slate-800">Langganan</h3>
                            <p class="mt-1.5 text-xs leading-5 text-slate-500">Paket tidak dapat diubah langsung dari daftar perusahaan. Berikan paket khusus melalui percakapan CS atau tunggu pembayaran paket publik.</p>
                            <dl class="mt-5 divide-y divide-slate-100 border-y border-slate-100 text-sm">
                                <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Paket aktif</dt><dd class="font-bold text-slate-900">{{ $selectedOrganization->plan_details->name }}</dd></div>
                                <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Status</dt><dd class="font-semibold capitalize text-slate-800">{{ str_replace('_', ' ', $selectedOrganization->subscription_status) }}</dd></div>
                                <div class="flex justify-between gap-4 py-3"><dt class="text-slate-500">Berlaku sampai</dt><dd class="font-semibold text-slate-800">{{ $selectedOrganization->subscription_ends_at?->format('d M Y') ?? 'Tidak ditentukan' }}</dd></div>
                            </dl>
                        </article>

                        <!-- Permanent Deletion of Workspace -->
                        <article class="rounded-2xl border border-rose-200 bg-rose-50/50 p-6 shadow-soft flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-rose-950 text-base flex items-center gap-2">
                                    <span class="text-rose-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                    </span>
                                    <span>Hapus Workspace</span>
                                </h3>
                                <p class="mt-1.5 text-xs text-rose-700 font-medium">Seluruh data workspace dan database terkait akan dihapus secara permanen.</p>
                                
                                <form method="POST" action="{{ route('superadmin.organizations.destroy', $selectedOrganization) }}" class="mt-5" onsubmit="return confirm('Penghapusan permanen tidak dapat dibatalkan. Lanjutkan?')">
                                    @csrf 
                                    @method('DELETE')
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold uppercase tracking-wider text-rose-800/80">Konfirmasi Nama Organisasi</label>
                                        <input name="confirmation" required class="h-11 w-full rounded-xl border border-rose-200 bg-white px-3 text-sm font-semibold text-rose-900 placeholder-rose-300 outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100 transition-all shadow-sm" placeholder="Ketik {{ $selectedOrganization->name }}">
                                    </div>
                                    <button class="mt-4.5 w-full rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-[0.98] py-3 text-sm font-bold text-white transition-all shadow-sm shadow-rose-500/10">Hapus Permanen</button>
                                </form>
                            </div>
                        </article>
                    </div>
                </section>
            @endif
        @endif

        <!-- USERS & ATTENDANCE SECTION -->
        @if($section === 'users')
            @include('superadmin.partials.organization-filter', ['action' => route('superadmin.users')])
            
            <section class="space-y-6">
                <div class="mb-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-700">{{ $selectedOrganization ? 'Data perusahaan terpilih' : 'Data seluruh platform' }}</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $selectedOrganization?->name ?? 'Seluruh pengguna workspace' }}</h2>
                </div>
                
                <div class="grid gap-6 2xl:grid-cols-[1.1fr_.9fr]">
                    <!-- User Directory Table Card -->
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
                        <div class="border-b border-slate-100 p-5 flex items-center justify-between bg-slate-50/50">
                            <h3 class="font-bold text-slate-800 text-sm">Daftar pengguna</h3>
                            <span class="rounded-full bg-indigo-50 border border-indigo-200/50 px-2.5 py-1 text-[10px] font-bold text-indigo-700 uppercase">20 data terbaru</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[600px] text-left text-sm">
                                <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                    <tr>
                                        <th class="px-6 py-3.5">Identitas</th>
                                        <th class="px-6 py-3.5">Perusahaan</th>
                                        <th class="px-6 py-3.5">Peran</th>
                                        <th class="px-6 py-3.5">Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @forelse($users as $user)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 flex items-center gap-3">
                                                <span class="grid h-8 w-8 place-items-center rounded-lg bg-slate-100 font-bold text-xs text-slate-700 border">
                                                    {{ strtoupper(substr($user->display_name ?? 'U', 0, 1)) }}
                                                </span>
                                                <div>
                                                    <strong class="block text-slate-800 font-bold text-sm">{{ $user->display_name }}</strong>
                                                    <span class="text-xs text-slate-400 font-medium">{{ $user->login_identity }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 text-xs">{{ $user->organization?->name ?? 'Admin platform' }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border">{{ $user->role }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-xs text-slate-400">{{ $user->created_at?->diffForHumans() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-10 text-center text-slate-400 font-medium">Belum ada pengguna.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </article>
                    
                    <!-- Latest Attendance List Card -->
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
                        <div class="border-b border-slate-100 p-5 flex items-center justify-between bg-slate-50/50">
                            <h3 class="font-bold text-slate-800 text-sm">Absensi terbaru</h3>
                            <span class="rounded-full bg-emerald-50 border border-emerald-200/50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 uppercase">15 data terbaru</span>
                        </div>
                        <div class="divide-y divide-slate-100 overflow-y-auto max-h-[600px]">
                            @forelse($recentAttendances as $attendance)
                                <div class="flex items-center justify-between gap-4 p-4 hover:bg-slate-50/50 transition-colors">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl font-bold text-sm shadow-sm {{ $attendance->izin ? 'bg-amber-50 border border-amber-200/50 text-amber-700' : 'bg-emerald-50 border border-emerald-200/50 text-emerald-700' }}">
                                            @if($attendance->izin)
                                                <span>I</span>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            @endif
                                        </span>
                                        <div class="min-w-0">
                                            <strong class="block truncate text-sm font-bold text-slate-800">{{ $attendance->siswa?->nama_lengkap ?? 'Pengguna tidak dikenal' }}</strong>
                                            <p class="truncate text-xs text-slate-500 font-medium">
                                                <span class="font-semibold text-slate-600">{{ $attendance->siswa?->user?->organization?->name }}</span> · 
                                                <span>{{ $attendance->izin ? 'Izin' : ($attendance->jam_masuk ?? 'Belum masuk') }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-slate-400 font-semibold whitespace-nowrap bg-slate-50 border px-2.5 py-1 rounded-lg">{{ optional($attendance->tanggal)->format('d M') }}</span>
                                </div>
                            @empty
                                <div class="p-12 text-center text-sm text-slate-400 font-medium">Belum ada catatan absensi hari ini.</div>
                            @endforelse
                        </div>
                    </article>
                </div>
            </section>
        @endif

        <!-- BILLING & PAYMENTS SECTION -->
        @if($section === 'billing')
            @include('superadmin.partials.organization-filter', ['action' => route('superadmin.billing')])
            
            <section class="space-y-6">
                <div class="mb-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Keuangan platform</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">Catatan pembayaran</h2>
                </div>
                
                <div class="grid gap-6 {{ $selectedOrganization ? 'xl:grid-cols-[1.1fr_.9fr]' : '' }}">
                    <!-- Payments History Card -->
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
                        <div class="border-b border-slate-100 p-5 flex items-center justify-between bg-slate-50/50">
                            <h3 class="font-bold text-slate-800 text-sm">Riwayat transaksi</h3>
                            <span class="rounded-full bg-brand-50 border border-brand-200/50 px-2.5 py-1 text-[10px] font-bold text-brand-700 uppercase">12 data terbaru</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse($payments as $payment)
                                <div class="flex justify-between gap-4 p-5 hover:bg-slate-50/50 transition-colors">
                                    <div class="space-y-1">
                                        <strong class="block text-sm font-bold text-slate-800">{{ $payment->organization?->name }}</strong>
                                        <p class="text-xs text-slate-400 font-medium">
                                            <span>{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}</span> · 
                                            <span class="font-semibold text-slate-500">{{ $payment->method ?: 'Manual' }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right space-y-1 shrink-0">
                                        <strong class="block text-sm font-extrabold text-slate-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                                        <span class="inline-flex rounded-full border px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider {{ $payment->status === 'paid' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">{{ $payment->status }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-12 text-center text-sm text-slate-400 font-medium">Belum ada catatan pembayaran.</div>
                            @endforelse
                        </div>
                    </article>
                    
                    <!-- Create Payment Form (Only visible when Organization is selected) -->
                    @if($selectedOrganization)
                        <form method="POST" action="{{ route('superadmin.organizations.payments.store', $selectedOrganization) }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft space-y-4">
                            @csrf
                            <div>
                                <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                                    <span class="text-brand-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    </span>
                                    <span>Catat Pembayaran Baru</span>
                                </h3>
                                <p class="mt-1 text-xs text-slate-500 font-medium">Rekam pembayaran manual untuk workspace: <span class="font-bold text-slate-700">{{ $selectedOrganization->name }}</span></p>
                            </div>
                            
                            <div class="grid gap-3.5 text-sm">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nominal Transfer (IDR)</label>
                                    <input type="number" name="amount" min="0" required placeholder="0" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800 placeholder-slate-400 shadow-sm">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</label>
                                        <select name="status" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 font-semibold text-slate-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all shadow-sm">
                                            @foreach(['paid','pending','failed','refunded'] as $status)
                                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Metode</label>
                                        <input name="method" placeholder="Transfer Bank" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800 placeholder-slate-400 shadow-sm">
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor Referensi / Resi</label>
                                    <input name="reference" placeholder="TRX-123456" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800 placeholder-slate-400 shadow-sm">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Periode Awal</label>
                                        <input type="date" name="period_start" class="h-11 w-full rounded-xl border border-slate-200 px-3 font-medium text-slate-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all shadow-sm">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Periode Akhir</label>
                                        <input type="date" name="period_end" class="h-11 w-full rounded-xl border border-slate-200 px-3 font-medium text-slate-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all shadow-sm">
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Waktu Bayar</label>
                                    <input type="datetime-local" name="paid_at" class="h-11 w-full rounded-xl border border-slate-200 px-3 font-medium text-slate-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all shadow-sm">
                                </div>
                                
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Catatan Tambahan</label>
                                    <textarea name="notes" class="min-h-[80px] w-full rounded-xl border border-slate-200 p-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all text-sm font-medium text-slate-800 placeholder-slate-400 shadow-sm" placeholder="Catatan internal…"></textarea>
                                </div>
                            </div>
                            
                            <button class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.98] py-3 text-sm font-bold text-white transition-all shadow-sm">Simpan Catatan Pembayaran</button>
                        </form>
                    @endif
                </div>
            </section>

            <!-- Packages & Subscriptions Section -->
            <section class="mt-8 space-y-6">
                <div class="flex items-center justify-between border-t border-slate-200 pt-8">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Paket langganan</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-900">Pengaturan paket dan harga</h2>
                        <p class="text-xs text-slate-400 font-medium">Buat, ubah, atau hapus paket dan aturan langganan di MWork.</p>
                    </div>
                    <button type="button" onclick="openAddPlanModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 hover:bg-brand-700 active:scale-[0.98] px-5 py-2.5 text-sm font-bold text-white transition-all shadow-md shadow-brand-500/10">
                        <span>+ Tambah Paket</span>
                    </button>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($planOptions as $planOptionItem)
                        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft hover:shadow-md transition-all relative flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <strong class="text-lg font-bold text-slate-800">{{ $planOptionItem->name }}</strong>
                                    <span class="text-xs font-extrabold text-brand-600 bg-brand-50 px-2.5 py-1 rounded-full">Rp {{ number_format($planOptionItem->price, 0, ',', '.') }} / {{ $planOptionItem->duration_label }}</span>
                                </div>
                                @if($planOptionItem->is_private)<span class="mt-2 inline-flex border border-violet-200 bg-violet-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-violet-700">Paket khusus</span>@endif
                                <div class="mt-4 space-y-2.5 text-sm font-medium text-slate-600 border-t border-slate-100 pt-4">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Maks. pengguna: <strong class="text-slate-800">{{ $planOptionItem->max_users }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Penyimpanan: <strong class="text-slate-800">{{ $planOptionItem->retention_months ? $planOptionItem->retention_months . ' bulan' : 'Selamanya' }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                        <span>Masa aktif: <strong class="text-slate-800">{{ $planOptionItem->duration_label }}</strong></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="grid h-4 w-4 shrink-0 place-items-center">
                                            @if($planOptionItem->has_audit_log)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            @endif
                                        </span>
                                        <span class="{{ $planOptionItem->has_audit_log ? 'text-slate-600' : 'text-slate-400 line-through' }}">Audit Log Aktivitas</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="grid h-4 w-4 shrink-0 place-items-center">
                                            @if($planOptionItem->has_custom_suffix)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            @endif
                                        </span>
                                        <span class="{{ $planOptionItem->has_custom_suffix ? 'text-slate-600' : 'text-slate-400 line-through' }}">Login Tanpa .mm Suffix</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex gap-2 border-t border-slate-100 pt-4">
                                <button type="button" onclick="openEditPlanModal({{ json_encode($planOptionItem) }})" class="flex-1 py-2 text-center rounded-xl bg-slate-50 hover:bg-slate-100 active:scale-[0.98] text-xs font-bold text-slate-600 transition-all">Edit</button>
                                <form method="POST" action="{{ route('superadmin.plans.destroy', $planOptionItem->id) }}" onsubmit="return confirm('Paket akan hilang dari penjualan. Pelanggan aktif tetap dapat menggunakannya sampai masa berlaku selesai. Lanjutkan?')" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full py-2 text-center rounded-xl bg-rose-50 hover:bg-rose-100 active:scale-[0.98] text-xs font-bold text-rose-600 transition-all">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <!-- Dialog Modal: Add Plan -->
            <dialog id="addPlanModal" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm border border-slate-200 outline-none">
                <form method="POST" action="{{ route('superadmin.plans.store') }}" class="flex flex-col">
                    @csrf
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">Tambah Paket Baru</h3>
                            <p class="text-xs text-slate-400 font-medium">Buat jenis paket dan atur fiturnya.</p>
                        </div>
                        <button type="button" onclick="addPlanModal.close()" class="text-2xl text-slate-400 hover:text-slate-600 outline-none">×</button>
                    </div>
                    <div class="space-y-4 p-6 text-sm">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Paket</label>
                            <input name="name" required placeholder="Contoh: Pro Plus" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Harga (IDR)</label>
                                <input type="number" name="price" min="0" required placeholder="0" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Maks. Akun (User)</label>
                                <input type="number" name="max_users" min="1" required placeholder="10" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Masa Aktif</label>
                                <input type="number" name="duration_value" min="1" max="1200" value="1" required class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Satuan Masa Aktif</label>
                                <select name="duration_unit" required class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800"><option value="days">Hari</option><option value="months" selected>Bulan</option></select>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Penyimpanan Absen (Bulan)</label>
                            <input type="number" name="retention_months" min="1" placeholder="Kosongkan jika tidak terbatas" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                            <p class="text-[10px] text-slate-400">Data absen yang lebih tua dari batas bulan ini akan otomatis dihapus.</p>
                        </div>
                        
                        <div class="space-y-3.5 border-t border-slate-100 pt-4">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="has_audit_log" value="1" class="h-4.5 w-4.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-slate-700">Aktifkan Audit Log Aktivitas</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="has_custom_suffix" value="1" class="h-4.5 w-4.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-slate-700">Login Tanpa Akhiran .mm (Sesuai nama perusahaan)</span>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input type="checkbox" name="is_private" value="1" class="mt-0.5 h-4.5 w-4.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span><span class="block font-medium text-slate-700">Paket khusus</span><span class="mt-0.5 block text-[10px] leading-4 text-slate-400">Tidak tampil untuk umum. Paket hanya muncul setelah diberikan ke perusahaan melalui percakapan CS.</span></span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4 bg-slate-50/50">
                        <button type="button" onclick="addPlanModal.close()" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 transition-all">Batal</button>
                        <button class="rounded-xl bg-brand-600 hover:bg-brand-700 text-xs font-bold text-white px-5 py-2.5 transition-all shadow-md shadow-brand-500/10">Simpan Paket</button>
                    </div>
                </form>
            </dialog>

            <!-- Dialog Modal: Edit Plan -->
            <dialog id="editPlanModal" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm border border-slate-200 outline-none">
                <form id="editPlanForm" method="POST" action="" class="flex flex-col">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">Ubah Paket</h3>
                            <p class="text-xs text-slate-400 font-medium">Ubah jenis paket dan atur fiturnya.</p>
                        </div>
                        <button type="button" onclick="editPlanModal.close()" class="text-2xl text-slate-400 hover:text-slate-600 outline-none">×</button>
                    </div>
                    <div class="space-y-4 p-6 text-sm">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Paket</label>
                            <input id="edit_plan_name" name="name" required placeholder="Contoh: Pro Plus" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Harga (IDR)</label>
                                <input id="edit_plan_price" type="number" name="price" min="0" required placeholder="0" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Maks. Akun (User)</label>
                                <input id="edit_plan_max_users" type="number" name="max_users" min="1" required placeholder="10" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Masa Aktif</label>
                                <input id="edit_plan_duration_value" type="number" name="duration_value" min="1" max="1200" required class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Satuan Masa Aktif</label>
                                <select id="edit_plan_duration_unit" name="duration_unit" required class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800"><option value="days">Hari</option><option value="months">Bulan</option></select>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Penyimpanan Absen (Bulan)</label>
                            <input id="edit_plan_retention_months" type="number" name="retention_months" min="1" placeholder="Kosongkan jika tidak terbatas" class="h-11 w-full rounded-xl border border-slate-200 px-3 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all font-semibold text-slate-800">
                            <p class="text-[10px] text-slate-400">Data absen yang lebih tua dari batas bulan ini akan otomatis dihapus.</p>
                        </div>
                        
                        <div class="space-y-3.5 border-t border-slate-100 pt-4">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input id="edit_plan_has_audit_log" type="checkbox" name="has_audit_log" value="1" class="h-4.5 w-4.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-slate-700">Aktifkan Audit Log Aktivitas</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input id="edit_plan_has_custom_suffix" type="checkbox" name="has_custom_suffix" value="1" class="h-4.5 w-4.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="font-medium text-slate-700">Login Tanpa Akhiran .mm (Sesuai nama perusahaan)</span>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer select-none">
                                <input id="edit_plan_is_private" type="checkbox" name="is_private" value="1" class="mt-0.5 h-4.5 w-4.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span><span class="block font-medium text-slate-700">Paket khusus</span><span class="mt-0.5 block text-[10px] leading-4 text-slate-400">Hanya perusahaan yang menerima penawaran dari CS yang dapat membelinya.</span></span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4 bg-slate-50/50">
                        <button type="button" onclick="editPlanModal.close()" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 transition-all">Batal</button>
                        <button class="rounded-xl bg-brand-600 hover:bg-brand-700 text-xs font-bold text-white px-5 py-2.5 transition-all shadow-md shadow-brand-500/10">Simpan Perubahan</button>
                    </div>
                </form>
            </dialog>

            <script>
                function openAddPlanModal() {
                    const modal = document.getElementById('addPlanModal');
                    if (modal) {
                        modal.showModal();
                    }
                }
                function openEditPlanModal(plan) {
                    const modal = document.getElementById('editPlanModal');
                    const form = document.getElementById('editPlanForm');
                    if (modal && form) {
                        form.action = `/superadmin/plans/${plan.id}`;
                        document.getElementById('edit_plan_name').value = plan.name;
                        document.getElementById('edit_plan_price').value = Math.round(plan.price);
                        document.getElementById('edit_plan_duration_value').value = plan.duration_value || 1;
                        document.getElementById('edit_plan_duration_unit').value = plan.duration_unit || 'months';
                        document.getElementById('edit_plan_max_users').value = plan.max_users;
                        document.getElementById('edit_plan_retention_months').value = plan.retention_months || '';
                        document.getElementById('edit_plan_has_audit_log').checked = !!plan.has_audit_log;
                        document.getElementById('edit_plan_has_custom_suffix').checked = !!plan.has_custom_suffix;
                        document.getElementById('edit_plan_is_private').checked = !!plan.is_private;
                        modal.showModal();
                    }
                }
            </script>
            @endif

        <!-- CUSTOMER SUPPORT SECTION -->
        @if($section === 'support')
            @include('superadmin.partials.organization-filter', ['action' => route('superadmin.support')])
            
            <section class="space-y-6">
                <div class="mb-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Layanan pelanggan</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-900">Percakapan bantuan</h2>
                </div>
                
                <!-- Ticket List Card -->
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
                    <div class="divide-y divide-slate-100">
                        @forelse($supportThreads as $thread)
                            <a href="{{ route('superadmin.support.show', $thread) }}" class="group flex flex-col justify-between gap-4 p-5 hover:bg-slate-50/50 sm:flex-row transition-all duration-200">
                                <div class="space-y-1 min-w-0">
                                    <strong class="block text-sm font-bold text-slate-800 group-hover:text-brand-600 transition-colors">{{ $thread->subject }}</strong>
                                    <p class="text-xs text-slate-500 font-medium">
                                        <span class="font-semibold text-slate-700">{{ $thread->organization?->name }}</span> · 
                                        <span class="capitalize">{{ $thread->category }}</span> · 
                                        <span class="font-semibold text-slate-400">{{ $thread->messages_count }} pesan</span>
                                    </p>
                                </div>
                                <div class="flex items-center sm:items-end justify-between sm:flex-col gap-2 shrink-0">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $thread->status === 'open' ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-slate-100 border border-slate-200 text-slate-600' }}">{{ str_replace('_', ' ', $thread->status) }}</span>
                                    <p class="text-xs text-slate-400 font-semibold">{{ $thread->last_message_at?->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-16 text-center text-slate-400 font-medium">
                                <span class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-slate-50 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                </span>
                                <h3 class="mt-4 text-sm font-bold text-slate-700">Belum ada percakapan</h3>
                                <p class="mt-1 text-xs text-slate-400">Belum ada tiket support yang masuk dari klien.</p>
                            </div>
                        @endforelse
                    </div>
                </article>
            </section>
        @endif

        <!-- AUDIT LOG SECTION -->
        @if($section === 'audit')
            @include('superadmin.partials.organization-filter', ['action' => route('superadmin.audit')])
            
            <section class="space-y-6">
                @if(!$selectedOrganization)
                    <div class="flex items-start gap-3.5 rounded-2xl border border-brand-200 bg-brand-50/30 p-5 text-sm text-brand-850 shadow-sm backdrop-blur-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="16" y2="12"/><line x1="12" x2="12.01" y1="8" y2="8"/></svg>
                        <div class="font-medium">Pilih organisasi pada filter di atas untuk melihat data audit log aktivitas khusus perusahaan tersebut.</div>
                    </div>
                @endif
                
                <!-- Audit Trail Table Card -->
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
                    <div class="border-b border-slate-100 p-5 flex items-center justify-between bg-slate-50/50">
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Aktivitas perusahaan</h3>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">Catatan aktivitas pengguna di dalam perusahaan.</p>
                        </div>
                        <span class="inline-flex rounded-full bg-slate-100 border border-slate-200 px-2.5 py-1 text-[10px] font-bold text-slate-600 uppercase">Hanya baca</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[850px] text-left text-sm">
                            <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-6 py-3.5">Waktu</th>
                                    <th class="px-6 py-3.5">Perusahaan</th>
                                    <th class="px-6 py-3.5">Pelaku</th>
                                    <th class="px-6 py-3.5">Aksi</th>
                                    <th class="px-6 py-3.5">Keterangan</th>
                                    <th class="px-6 py-3.5">Alamat IP</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4 text-xs text-slate-400 whitespace-nowrap">{{ $log->created_at?->format('d M Y, H:i') }}</td>
                                        <td class="px-6 py-4 text-slate-700 text-xs font-semibold">{{ $log->organization?->name }}</td>
                                        <td class="px-6 py-4 text-slate-800 text-xs">{{ $log->user ?: 'Sistem' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-brand-50 border border-brand-200/50 text-brand-700">{{ $log->aksi }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500 font-medium max-w-[200px] truncate" title="{{ $log->keterangan }}">{{ $log->keterangan ?: '-' }}</td>
                                        <td class="px-6 py-4 font-mono text-xs text-slate-400 whitespace-nowrap">{{ $log->ip_address ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-12 text-center text-slate-400 font-medium">Pilih organisasi atau belum ada aktivitas tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        @endif
    </main>
</div>

<!-- Mobile menu toggle helper script -->
<script>
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebarDrawer');
        const backdrop = document.getElementById('sidebarBackdrop');
        
        if (sidebar.classList.contains('-translate-x-full')) {
            // Open sidebar
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.add('opacity-100');
            }, 10);
        } else {
            // Close sidebar
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.remove('opacity-100');
            setTimeout(() => {
                backdrop.classList.add('hidden');
            }, 300);
        }
    }
</script>
</body>
</html>
