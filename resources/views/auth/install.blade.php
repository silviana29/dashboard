<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasang Aplikasi · MWork</title>
    <meta name="robots" content="noindex,follow">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { ink: '#101828' }, boxShadow: { soft: '0 24px 80px rgba(15,23,42,.14)' } } } }
    </script>
    @include('partials.pwa')
</head>
<body class="min-h-screen bg-[#eef1f8] text-ink antialiased">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-indigo-300/40 blur-3xl"></div>
        <div class="absolute -right-16 bottom-0 h-80 w-80 rounded-full bg-cyan-200/40 blur-3xl"></div>
    </div>

    <main class="relative flex min-h-screen items-center justify-center px-4 py-8 sm:px-6">
        <div class="w-full max-w-[420px]">
            <a href="{{ route('login') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-semibold text-slate-500 hover:text-indigo-600">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                Kembali ke login
            </a>

            <div class="overflow-hidden rounded-[1.75rem] border border-white/80 bg-white shadow-soft">
                <div class="px-6 pb-8 pt-8 sm:px-8">
                    <div class="flex flex-col items-center text-center">
                        <img src="{{ asset('mworkputih.png') }}" alt="Logo MWork" class="h-24 w-24 rounded-[1.4rem] object-contain">
                        <h1 class="mt-5 text-2xl font-semibold tracking-[-.03em]">MWork</h1>
                        <p class="mt-1 text-sm font-medium text-indigo-600">Your Work, In Sync.</p>
                        <p class="mt-3 text-sm leading-6 text-slate-500">Pasang MWork ke perangkat Anda agar absensi, izin, dan rekap kehadiran bisa dibuka seperti aplikasi, tanpa harus lewat browser setiap kali.</p>
                    </div>

                    <div class="mt-6 space-y-2.5 text-sm">
                        <div class="flex gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-indigo-100 text-indigo-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-4.4 7-11a7 7 0 1 0-14 0c0 6.6 7 11 7 11Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold">Lokasi</p>
                                <p class="mt-0.5 text-slate-500">Memvalidasi absensi di dalam radius gedung atau lokasi kerja.</p>
                            </div>
                        </div>
                        <div class="flex gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-indigo-100 text-indigo-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 8h16v10H4z"/><circle cx="12" cy="13" r="2.4"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold">Kamera</p>
                                <p class="mt-0.5 text-slate-500">Mengambil foto saat absen masuk dan keluar.</p>
                            </div>
                        </div>
                        <div class="flex gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5">
                            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-indigo-100 text-indigo-600">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold">Layar utama</p>
                                <p class="mt-0.5 text-slate-500">Membuka MWork dalam mode aplikasi, lebih cepat dan mudah diakses.</p>
                            </div>
                        </div>
                    </div>

                    <p id="installHint" class="mt-5 text-center text-xs leading-5 text-slate-400">Izin kamera dan lokasi hanya diminta saat Anda absen, bukan saat memasang aplikasi.</p>

                    <div id="iosGuide" hidden class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        <p class="font-semibold">Cara pasang di iPhone / iPad</p>
                        <ol class="mt-2 list-decimal space-y-1 pl-4 text-amber-800">
                            <li>Ketuk tombol Bagikan di Safari.</li>
                            <li>Pilih <strong>Add to Home Screen</strong>.</li>
                            <li>Ketuk <strong>Add</strong> untuk memasang MWork.</li>
                        </ol>
                    </div>

                    <div id="browserGuide" hidden class="mt-5 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                        <p class="font-semibold">Pasang melalui menu browser</p>
                        <ol id="browserGuideSteps" class="mt-2 list-decimal space-y-1 pl-4 text-blue-800"></ol>
                    </div>

                    <p id="installStatus" class="mt-5 hidden rounded-xl border px-3 py-2 text-center text-sm"></p>

                    <button id="installButton" type="button" class="mt-5 flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 text-sm font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:shadow-none">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12"/><path d="m8 11 4 4 4-4"/><path d="M5 21h14"/></svg>
                        <span id="installButtonLabel">Pasang aplikasi</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        const button = document.getElementById('installButton');
        const label = document.getElementById('installButtonLabel');
        const status = document.getElementById('installStatus');
        const iosGuide = document.getElementById('iosGuide');
        const browserGuide = document.getElementById('browserGuide');
        const browserGuideSteps = document.getElementById('browserGuideSteps');
        let justInstalled = false;

        const showStatus = (message, kind) => {
            status.textContent = message;
            status.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800', 'border-rose-200', 'bg-rose-50', 'text-rose-800', 'border-slate-200', 'bg-slate-50', 'text-slate-600');
            if (kind === 'ok') status.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-800');
            else if (kind === 'error') status.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-800');
            else status.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-600');
        };

        const showBrowserGuide = () => {
            const pwa = window.MWorkPWA;
            browserGuideSteps.replaceChildren();
            const steps = pwa?.isAndroid()
                ? ['Buka halaman ini menggunakan Chrome.', 'Buka menu tiga titik lalu pilih Install app. Jangan pilih Buat pintasan.', 'Jika pilihan Install app belum muncul, tutup Chrome lalu buka kembali halaman ini.']
                : ['Buka menu Chrome atau Edge.', 'Pilih Install MWork. Di Chrome, pilihan ini dapat berada di menu Simpan dan bagikan.', 'Konfirmasi pemasangan pada dialog browser.'];
            steps.forEach((step) => {
                const item = document.createElement('li');
                item.textContent = step;
                browserGuideSteps.appendChild(item);
            });
            browserGuide.hidden = false;
            browserGuide.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        };

        const syncInstalledUi = () => {
            const pwa = window.MWorkPWA;
            if (!pwa) return false;

            if (pwa.isStandalone() || justInstalled) {
                iosGuide.hidden = true;
                browserGuide.hidden = true;
                button.disabled = true;
                label.textContent = 'Sudah terpasang';
                showStatus('MWork sudah terpasang di perangkat ini.', 'ok');
                return true;
            }
            return false;
        };

        const initializeInstall = async () => {
            const pwa = window.MWorkPWA;
            if (!pwa) {
                button.disabled = true;
                label.textContent = 'Pemasangan tidak didukung';
                showStatus('Browser ini tidak menyediakan fitur pemasangan aplikasi.', 'error');
                return;
            }
            if (syncInstalledUi()) return;

            button.disabled = false;
            label.textContent = 'Pasang aplikasi';

            if (pwa.isIos()) {
                iosGuide.hidden = false;
                button.disabled = false;
                label.textContent = 'Lihat cara memasang';
                showStatus('iPhone dan iPad memasang aplikasi melalui menu Bagikan di Safari.', 'info');
                return;
            }

            if (pwa.isInAppBrowser()) {
                label.textContent = 'Buka menggunakan Chrome';
                showStatus('Browser di dalam WhatsApp, Instagram, atau aplikasi lain tidak mendukung pemasangan. Buka alamat MWork langsung menggunakan Chrome.', 'error');
                return;
            }

            if (pwa.isAndroidChrome() && !pwa.canInstall()) {
                showStatus('Chrome di HP sedang memverifikasi aplikasi. Tetap buka halaman ini dan gunakan MWork sekitar 30 detik; tombol akan siap otomatis.', 'info');
                pwa.waitForPrompt(32000);
            }

            const preparation = await Promise.race([
                pwa.prepare(),
                new Promise((resolve) => setTimeout(() => resolve({ ready: false, reason: 'timeout' }), 1200)),
            ]);
            if (!preparation.ready) {
                if (preparation.reason === 'insecure') {
                    label.textContent = 'Lihat cara memasang';
                    showStatus('Pemasangan aplikasi memerlukan koneksi HTTPS.', 'error');
                }
                return;
            }

            if (pwa.canInstall()) {
                label.textContent = 'Pasang aplikasi';
                showStatus('Pemasangan siap. Ketuk tombol di bawah untuk melanjutkan.', 'ok');
            }
        };

        button.addEventListener('click', async () => {
            const pwa = window.MWorkPWA;

            if (pwa?.isIos()) {
                iosGuide.hidden = false;
                iosGuide.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                showStatus('Di iPhone/iPad, pasang lewat Safari: Bagikan → Add to Home Screen.', 'info');
                return;
            }

            if (!pwa?.canInstall()) {
                button.disabled = true;
                label.textContent = 'Membuka pemasangan…';
                await pwa?.waitForPrompt(1200);
                button.disabled = false;
                label.textContent = 'Pasang aplikasi';
            }

            if (!pwa?.canInstall()) {
                showBrowserGuide();
                showStatus('Browser belum menyediakan pemasangan aplikasi. Gunakan pilihan Install app pada menu browser, bukan Buat pintasan.', 'info');
                return;
            }

            button.disabled = true;
            label.textContent = 'Memasang…';
            const choice = await pwa.promptInstall();

            if (choice?.outcome === 'accepted') {
                justInstalled = true;
                showStatus('Aplikasi sedang dipasang. Setelah selesai, buka MWork dari layar utama.', 'ok');
                label.textContent = 'Terpasang';
                return;
            }

            button.disabled = false;
            label.textContent = 'Pasang aplikasi';
            showStatus('Pemasangan dibatalkan. Anda bisa mencoba lagi kapan saja.', 'error');
        });

        initializeInstall();
        window.addEventListener('mwork-pwa-changed', (event) => {
            if (event.detail?.installed) {
                justInstalled = true;
                showStatus('MWork berhasil dipasang di perangkat ini.', 'ok');
            }
            if (!syncInstalledUi() && window.MWorkPWA?.canInstall()) {
                browserGuide.hidden = true;
                button.disabled = false;
                label.textContent = 'Pasang aplikasi';
                showStatus('Pemasangan siap. Ketuk tombol di bawah untuk melanjutkan.', 'ok');
            }
        });
    </script>
</body>
</html>
