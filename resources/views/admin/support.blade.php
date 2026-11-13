<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantuan & Support · {{ $organization->name }}</title>
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
<body class="min-h-screen bg-[#f8fafc] text-slate-900 antialiased">
    @include('partials.admin-sidebar')
    <div id="overlay" class="fixed inset-0 z-30 hidden bg-slate-950/60 lg:hidden"></div>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-20 flex h-20 items-center justify-between border-b border-slate-200 bg-white px-5 backdrop-blur-xl sm:px-8">
            <div class="flex items-center gap-4">
                <button id="menuButton" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 lg:hidden">Menu</button>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[.16em] text-slate-500">Bantuan workspace</p>
                    <h1 class="text-xl font-semibold tracking-tight text-slate-900">Bantuan / Customer Service</h1>
                </div>
            </div>
            <div class="hidden items-center gap-3 sm:flex">
                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Sistem aktif</span>
            </div>
        </header>

        <main class="p-4 sm:p-8 lg:p-10">
            @if(session('success'))<div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ $errors->first() }}</div>@endif

            <section class="grid gap-6 md:grid-cols-[.38fr_.62fr]">
                <!-- Sidebar Ticket List -->
                <div class="space-y-4">
                    <button onclick="newTicket.showModal()" class="w-full rounded-xl bg-slate-900 hover:bg-slate-800 py-3 text-xs font-bold text-white transition-colors">
                        + Buat Tiket / Bantuan Baru
                    </button>
                    
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white divide-y divide-slate-100 shadow-sm">
                        <div class="border-b border-slate-100 bg-slate-50 px-4 py-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Daftar Percakapan</span>
                        </div>
                        @forelse($threads as $thread)
                            <a href="{{ route('admin.support', ['thread' => $thread->id]) }}" class="block p-4 hover:bg-slate-50 transition-colors {{ $activeThread?->id === $thread->id ? 'bg-slate-100 border-l-4 border-slate-900 font-medium' : '' }}">
                                <div class="flex items-center justify-between gap-2">
                                    <strong class="truncate text-sm text-slate-900">{{ $thread->subject }}</strong>
                                    <span class="shrink-0 text-[10px] font-bold uppercase px-2 py-0.5 rounded border {{ $thread->status === 'open' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-slate-100 border-slate-300 text-slate-600' }}">
                                        {{ $thread->status }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">
                                    Kategori: <span class="capitalize font-medium text-slate-700">{{ $thread->category }}</span> · {{ $thread->last_message_at?->diffForHumans() }}
                                </p>
                            </a>
                        @empty
                            <div class="p-8 text-center text-xs text-slate-400">Belum ada percakapan tiket bantuan.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Chat Box Area -->
                <article class="flex min-h-[500px] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    @if($activeThread)
                        <header class="border-b border-slate-200 p-4 bg-slate-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="font-bold text-slate-900 text-base">{{ $activeThread->subject }}</h2>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Layanan CS MWork · Kategori: <span class="capitalize font-medium text-slate-700">{{ $activeThread->category }}</span>
                                    </p>
                                </div>
                                <span class="rounded border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 capitalize">
                                    Status: {{ $activeThread->status }}
                                </span>
                            </div>
                        </header>
                        @if($activeThread->planOffers->contains(fn ($offer) => $offer->isAvailable()))
                            <div class="border-b border-violet-200 bg-violet-50 px-4 py-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-violet-700">Penawaran paket khusus</p>
                                @foreach($activeThread->planOffers->filter(fn ($offer) => $offer->isAvailable()) as $offer)
                                    <div class="mt-1 flex flex-wrap items-center justify-between gap-2 text-xs"><span><strong class="text-slate-900">{{ $offer->plan?->name }}</strong> · Rp {{ number_format($offer->plan?->price ?? 0, 0, ',', '.') }} / {{ $offer->plan?->duration_label }}</span><a href="{{ route('admin.billing') }}" class="border border-violet-300 bg-white px-3 py-1.5 font-bold text-violet-700">Lihat & bayar</a></div>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Messages Body -->
                        <div class="flex-1 space-y-4 overflow-y-auto bg-slate-50/50 p-4 sm:p-5 min-h-[300px]">
                            @foreach($activeThread->messages as $message)
                                <div class="flex {{ $message->sender_role === 'admin' ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[85%] sm:max-w-[75%] space-y-1">
                                        <div class="rounded-xl px-4 py-3 text-xs leading-relaxed border {{ $message->sender_role === 'admin' ? 'bg-slate-900 border-slate-900 text-white rounded-br-none' : 'bg-white border-slate-200 text-slate-800 rounded-bl-none' }}">
                                            <p class="whitespace-pre-line font-normal text-xs sm:text-sm">{{ $message->body }}</p>
                                        </div>
                                        <span class="block text-[10px] text-slate-400 font-mono {{ $message->sender_role === 'admin' ? 'text-right' : 'text-left' }}">
                                            {{ $message->sender_role === 'admin' ? 'Anda (Admin)' : 'Customer Service MWork' }} · {{ $message->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Reply Form -->
                        <form method="POST" action="{{ route('admin.support.messages.store', $activeThread) }}" class="flex items-end gap-3 border-t border-slate-200 p-4 bg-white">
                            @csrf
                            <textarea name="message" required maxlength="5000" class="min-h-[48px] flex-1 rounded-xl border border-slate-300 p-3 text-xs text-slate-800 outline-none focus:border-slate-500 resize-none" placeholder="Tuliskan balasan Anda di sini..."></textarea>
                            <button class="shrink-0 rounded-xl bg-slate-900 hover:bg-slate-800 px-5 py-3 text-xs font-bold text-white transition-colors">Kirim</button>
                        </form>
                    @else
                        <div class="grid flex-1 place-items-center p-8 text-center text-xs text-slate-400">
                            <div>
                                <p class="font-medium text-slate-600 text-sm">Belum Ada Percakapan Dipilih</p>
                                <p class="mt-1 text-slate-400">Pilih salah satu percakapan di sebelah kiri atau buat tiket bantuan baru.</p>
                            </div>
                        </div>
                    @endif
                </article>
            </section>
        </main>
    </div>

    <!-- Create Ticket Dialog -->
    <dialog id="newTicket" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl p-0 shadow-2xl border border-slate-200 outline-none">
        <form method="POST" action="{{ route('admin.support.threads.store') }}">
            @csrf
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <h3 class="font-bold text-slate-900 text-sm sm:text-base">Buat Tiket / Bantuan Baru</h3>
                <button type="button" onclick="newTicket.close()" class="text-2xl text-slate-400 hover:text-slate-600">×</button>
            </div>
            
            <div class="space-y-4 p-6 text-xs text-slate-700">
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold text-slate-600">Subjek Tiket</span>
                    <input name="subject" required maxlength="150" placeholder="Tuliskan topik masalah / pertanyaan" class="h-10 w-full rounded-xl border border-slate-300 px-3 text-xs outline-none focus:border-slate-500">
                </label>
                
                <div class="grid grid-cols-2 gap-3">
                    <label class="block">
                        <span class="mb-1 block text-xs font-semibold text-slate-600">Kategori</span>
                        <select name="category" class="h-10 w-full rounded-xl border border-slate-300 bg-white px-3 text-xs outline-none focus:border-slate-500">
                            <option value="general">Umum</option>
                            <option value="technical">Teknis</option>
                            <option value="billing">Pembayaran</option>
                            <option value="consultation">Konsultasi</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-xs font-semibold text-slate-600">Prioritas</span>
                        <select name="priority" class="h-10 w-full rounded-xl border border-slate-300 bg-white px-3 text-xs outline-none focus:border-slate-500">
                            <option value="normal">Normal</option>
                            <option value="high">Penting</option>
                            <option value="urgent">Mendesak</option>
                        </select>
                    </label>
                </div>
                
                <label class="block">
                    <span class="mb-1 block text-xs font-semibold text-slate-600">Pesan Detail</span>
                    <textarea name="message" required maxlength="5000" placeholder="Jelaskan pertanyaan atau kendala Anda secara lengkap..." class="min-h-[110px] w-full rounded-xl border border-slate-300 p-3 text-xs outline-none focus:border-slate-500"></textarea>
                </label>
            </div>
            
            <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4 bg-slate-50">
                <button type="button" onclick="newTicket.close()" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Batal</button>
                <button class="rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-bold text-white px-4 py-2">Kirim Tiket</button>
            </div>
        </form>
    </dialog>

    <script>
        const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('overlay'),menuButton=document.getElementById('menuButton');
        menuButton.addEventListener('click',()=>{sidebar.classList.remove('-translate-x-full');overlay.classList.remove('hidden')});
        overlay.addEventListener('click',()=>{sidebar.classList.add('-translate-x-full');overlay.classList.add('hidden')});
    </script>
</body>
</html>
