<link rel="manifest" href="/manifest.webmanifest">
<link rel="icon" type="image/png" href="{{ asset('mworkputih.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('mworkputih.png') }}">
<link rel="apple-touch-icon" sizes="192x192" href="/mwork-icon-192-v6.png">
<meta name="theme-color" content="#4f46e5">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="MWork">
@include('partials.ui-system')

<script>
    (() => {
        const dispatch = (name, detail) => window.dispatchEvent(new CustomEvent(name, { detail }));

        let registrationPromise = null;

        window.MWorkPWA = {
            deferredPrompt: null,
            registrationError: null,
            isStandalone() {
                return window.matchMedia('(display-mode: standalone)').matches
                    || window.navigator.standalone === true;
            },
            canInstall() {
                return Boolean(this.deferredPrompt) && !this.isStandalone();
            },
            isIos() {
                return /iphone|ipad|ipod/i.test(navigator.userAgent)
                    && !window.MSStream;
            },
            isAndroid() {
                return /android/i.test(navigator.userAgent);
            },
            isAndroidChrome() {
                return this.isAndroid()
                    && /chrome|crios/i.test(navigator.userAgent)
                    && !/edg|opr|samsungbrowser/i.test(navigator.userAgent);
            },
            isInAppBrowser() {
                return /; wv\)|\bwv\b|FBAN|FBAV|Instagram|Line\/|WhatsApp|TikTok/i.test(navigator.userAgent);
            },
            isSecure() {
                return window.isSecureContext || ['localhost', '127.0.0.1'].includes(window.location.hostname);
            },
            isSupported() {
                return 'serviceWorker' in navigator;
            },
            async prepare() {
                if (!this.isSupported()) return { ready: false, reason: 'unsupported' };
                if (!this.isSecure()) return { ready: false, reason: 'insecure' };

                try {
                    const registration = await registrationPromise;
                    if (!registration) return { ready: false, reason: 'service-worker' };
                    await navigator.serviceWorker.ready;
                    return {
                        ready: true,
                        controlled: Boolean(navigator.serviceWorker.controller),
                        canInstall: this.canInstall(),
                    };
                } catch (error) {
                    this.registrationError = error;
                    return { ready: false, reason: 'service-worker' };
                }
            },
            waitForPrompt(timeout = 2500) {
                if (this.canInstall()) return Promise.resolve(true);
                return new Promise((resolve) => {
                    const done = () => {
                        clearTimeout(timer);
                        window.removeEventListener('mwork-pwa-changed', changed);
                        resolve(this.canInstall());
                    };
                    const changed = () => this.canInstall() && done();
                    const timer = setTimeout(done, timeout);
                    window.addEventListener('mwork-pwa-changed', changed);
                });
            },
            async promptInstall() {
                if (!this.deferredPrompt) return { outcome: 'unavailable' };

                this.deferredPrompt.prompt();
                const choice = await this.deferredPrompt.userChoice;
                this.deferredPrompt = null;
                dispatch('mwork-pwa-changed');
                return choice;
            },
        };

        if ('serviceWorker' in navigator) {
            registrationPromise = navigator.serviceWorker.register('/service-worker.js?v=6', {
                scope: '/',
                updateViaCache: 'none',
            })
                .then((registration) => {
                    dispatch('mwork-pwa-changed', { serviceWorkerReady: true });
                    return registration;
                })
                .catch((error) => {
                    window.MWorkPWA.registrationError = error;
                    dispatch('mwork-pwa-changed', { serviceWorkerError: true });
                    return null;
                });
        }

        window.addEventListener('beforeinstallprompt', (event) => {
            // Di Android, biarkan Chrome ikut menampilkan promosi pemasangan native.
            // Desktop tetap memakai tombol MWork sebagai pemicu dialog.
            if (!window.MWorkPWA.isAndroid()) event.preventDefault();
            window.MWorkPWA.deferredPrompt = event;
            dispatch('mwork-pwa-changed');
        });

        window.addEventListener('appinstalled', () => {
            window.MWorkPWA.deferredPrompt = null;
            dispatch('mwork-pwa-changed', { installed: true });
        });
    })();
</script>
