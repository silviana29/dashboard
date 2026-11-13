<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $thread->subject }} · CS MWork</title>
    
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
                        }
                    },
                    boxShadow: {
                        soft: '0 12px 40px -4px rgba(15, 23, 42, 0.04)',
                        premium: '0 20px 50px -12px rgba(124, 58, 237, 0.08)',
                    }
                }
            }
        }
    </script>
    @include('partials.pwa')
    
    <style>
        body { background: #f6f7f9 !important; }
        [class*="rounded-"] { border-radius: 10px !important; }
        [class*="shadow"] { box-shadow: 0 1px 2px rgba(15,23,42,.05), 0 8px 24px rgba(15,23,42,.05) !important; }
        [class*="backdrop-blur"] { backdrop-filter: none !important; }
        .bg-brand-600 { background-color: #4f46e5 !important; }
        .text-brand-600 { color: #4f46e5 !important; }
        input, select, textarea, button { border-radius: 10px !important; }
        input:focus, select:focus, textarea:focus { box-shadow: 0 0 0 3px rgba(79,70,229,.12) !important; border-color: #4f46e5 !important; }
        .classic-title { background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    </style>
    @include('partials.ui-system')
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased font-sans flex flex-col">

    <!-- Top Navbar Header -->
    <header class="sticky top-0 z-30 border-b border-slate-500 bg-slate-900 px-5 py-3 text-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('superadmin.support') }}" class="p-2 -ml-2 rounded-xl border border-slate-600 bg-slate-800 text-white hover:bg-slate-700 transition-colors shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" x2="5" y1="12" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                </a>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">MWork Administrator</span>
                    <h1 class="text-base sm:text-lg font-bold tracking-tight text-white leading-tight">Layanan Pelanggan</h1>
                </div>
            </div>
            <span class="inline-flex border border-slate-600 bg-slate-800 px-3 py-1 text-xs font-bold text-slate-100 tracking-wide">{{ $thread->organization->name }}</span>
        </div>
    </header>

    <!-- Main Chat Workspace -->
    <main class="flex-1 mx-auto w-full max-w-6xl p-5 sm:p-8">
        
        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/50 p-4 text-sm text-emerald-800 shadow-sm backdrop-blur-md transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <div class="font-medium">{{ session('success') }}</div>
            </div>
        @endif
        @if($errors->any())<div class="mb-6 border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ $errors->first() }}</div>@endif

        <div class="grid gap-6 lg:grid-cols-[1.1fr_.9fr] xl:grid-cols-[1.3fr_.7fr]">
            
            <!-- Chat Box Thread Container -->
            <article class="flex flex-col min-h-[600px] max-h-[750px] overflow-hidden border border-slate-400 bg-white">
                <!-- Chat Header info -->
                <header class="classic-title p-4 flex justify-between items-center">
                    <div>
                        <h2 class="font-bold text-slate-800 text-base leading-tight">{{ $thread->subject }}</h2>
                        <div class="mt-1 flex flex-wrap gap-1.5 text-xs text-slate-400 font-semibold uppercase tracking-wider">
                            <span class="text-slate-500">{{ $thread->category }}</span>
                            <span>·</span>
                            <span class="text-slate-500">Prioritas {{ $thread->priority }}</span>
                        </div>
                    </div>
                    
                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider {{ $thread->status === 'open' ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-slate-100 border border-slate-200 text-slate-650' }}">
                        {{ str_replace('_', ' ', $thread->status) }}
                    </span>
                </header>
                
                <!-- Messages Body Area -->
                <div class="flex-1 space-y-4 overflow-y-auto bg-slate-50/50 p-5">
                    @foreach($thread->messages as $message)
                        <div class="flex {{ $message->sender_role === 'support' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] flex flex-col gap-1">
                                <!-- Message bubble wrapper -->
                                <div class="px-4.5 py-3 text-sm leading-6 border {{ $message->sender_role === 'support' ? 'bg-blue-800 border-blue-900 text-white' : 'bg-white border-slate-300 text-slate-700' }}">
                                    <p class="whitespace-pre-line font-medium">{{ $message->body }}</p>
                                </div>
                                <!-- Metadata sub-label -->
                                <span class="px-1 block text-[10px] font-bold opacity-60 text-slate-400 {{ $message->sender_role === 'support' ? 'text-right' : 'text-left' }}">
                                    {{ $message->sender_role === 'support' ? 'CS MWork' : ($message->user?->display_name ?? $thread->organization->name) }} · {{ $message->created_at->format('d M, H:i') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Chat Footer Input Form -->
                <form method="POST" action="{{ route('superadmin.support.messages.store', $thread) }}" class="flex items-end gap-3 border-t border-slate-100 p-4 bg-white">
                    @csrf
                    <textarea name="message" required maxlength="5000" class="min-h-[50px] max-h-[160px] flex-1 rounded-xl border border-slate-200 p-3 text-sm font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all shadow-sm resize-none" placeholder="Balasan CS…"></textarea>
                    <button class="rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.98] px-5 py-3 text-sm font-bold text-white transition-all shadow-sm shrink-0 flex items-center gap-1.5">
                        <span>Kirim</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" x2="11" y1="2" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </form>
            </article>
            
            <!-- Contextual Sidebar Panel -->
            <aside class="space-y-5">
                
                <!-- Workspace detail info widget -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft space-y-4">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">Informasi workspace</p>
                        <h3 class="mt-1.5 font-extrabold text-slate-800 text-lg leading-tight">{{ $thread->organization->name }}</h3>
                        <p class="text-xs text-slate-400 font-semibold">{{ $thread->organization->slug }} · <span class="capitalize text-slate-500 font-bold">{{ $thread->organization->type_label }}</span></p>
                    </div>
                    
                    <div class="divide-y divide-slate-100 border-t border-slate-100 text-xs font-semibold text-slate-600">
                        <div class="flex justify-between py-3">
                            <span class="text-slate-500">Paket aktif</span>
                            <span class="uppercase tracking-wider text-brand-600 font-extrabold">{{ $thread->organization->plan }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-slate-500">Status langganan</span>
                            <span class="uppercase tracking-wider font-extrabold text-emerald-600">{{ str_replace('_', ' ', $thread->organization->subscription_status) }}</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-slate-500">Jumlah pengguna</span>
                            <span class="text-slate-900 font-bold">{{ $thread->organization->users()->count() }} pengguna</span>
                        </div>
                        <div class="flex justify-between py-3">
                            <span class="text-slate-500">Lokasi / geofence</span>
                            <span class="text-slate-900 font-bold">{{ $thread->organization->gedungs()->count() }} lokasi</span>
                        </div>
                        <div class="flex flex-col gap-1 py-3">
                            <span class="text-slate-500">Pelapor</span>
                            <span class="text-slate-800 font-bold truncate">{{ $thread->creator?->display_name ?? 'Admin perusahaan' }}</span>
                            <span class="text-[10px] text-slate-400 font-medium truncate">{{ $thread->creator?->login_identity }}</span>
                        </div>
                    </div>
                </div>

                <div class="border border-violet-200 bg-white p-6 shadow-soft">
                    <p class="text-[10px] font-extrabold uppercase tracking-widest text-violet-700">Paket khusus</p>
                    <h3 class="mt-1 text-base font-bold text-slate-900">Berikan penawaran perusahaan</h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Setelah diberikan, paket tampil di halaman Tagihan perusahaan dan tetap harus dibayar melalui sistem pembayaran.</p>
                    <form method="POST" action="{{ route('superadmin.support.plan-offers.store', $thread) }}" class="mt-4 space-y-3">
                        @csrf
                        <label class="block text-xs font-semibold text-slate-700">Pilih paket
                            <select name="plan_id" required class="mt-1 h-11 w-full border border-slate-300 bg-white px-3 text-sm">
                                <option value="">Pilih paket khusus</option>
                                @foreach($privatePlans as $privatePlan)
                                    <option value="{{ $privatePlan->id }}">{{ $privatePlan->name }} · Rp {{ number_format($privatePlan->price, 0, ',', '.') }} / {{ $privatePlan->duration_label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block text-xs font-semibold text-slate-700">Batas penawaran <span class="font-normal text-slate-400">(opsional)</span>
                            <input type="datetime-local" name="expires_at" class="mt-1 h-11 w-full border border-slate-300 px-3 text-sm">
                        </label>
                        <button @disabled($privatePlans->isEmpty()) class="w-full bg-violet-700 py-3 text-sm font-bold text-white hover:bg-violet-800 disabled:bg-slate-300">Berikan paket khusus</button>
                    </form>
                    <div class="mt-4 divide-y divide-slate-100 border-t border-slate-200">
                        @forelse($thread->planOffers->sortByDesc('created_at') as $offer)
                            <div class="flex items-start justify-between gap-3 py-3 text-xs">
                                <div><strong class="text-slate-800">{{ $offer->plan?->name ?? 'Paket dihapus' }}</strong><span class="mt-0.5 block text-slate-400">{{ $offer->offered_at?->format('d M Y, H:i') }}{{ $offer->expires_at ? ' · s.d. '.$offer->expires_at->format('d M Y, H:i') : '' }}</span></div>
                                <span class="border px-2 py-1 font-bold uppercase {{ $offer->status === 'paid' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($offer->status === 'offered' ? 'border-violet-200 bg-violet-50 text-violet-700' : 'border-slate-200 bg-slate-50 text-slate-500') }}">{{ $offer->status }}</span>
                            </div>
                        @empty
                            <p class="py-3 text-xs text-slate-400">Belum ada paket khusus pada percakapan ini.</p>
                        @endforelse
                    </div>
                </div>
                
                <!-- Ticket status state control form widget -->
                <form method="POST" action="{{ route('superadmin.support.status', $thread) }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-soft space-y-4">
                    @csrf 
                    @method('PATCH')
                    
                    <label class="block">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Status Percakapan</span>
                        <div class="relative mt-2">
                            <select name="status" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-sm font-semibold text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 transition-all shadow-sm appearance-none">
                                <option value="open" @selected($thread->status === 'open')>Terbuka</option>
                                <option value="waiting_admin" @selected($thread->status === 'waiting_admin')>Menunggu admin perusahaan</option>
                                <option value="resolved" @selected($thread->status === 'resolved')>Selesai</option>
                                <option value="closed" @selected($thread->status === 'closed')>Ditutup</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </span>
                        </div>
                    </label>
                    <button class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 active:scale-[0.98] py-3 text-sm font-bold text-white transition-all shadow-sm">Perbarui Status Tiket</button>
                </form>
            </aside>
        </div>
    </main>

</body>
</html>
