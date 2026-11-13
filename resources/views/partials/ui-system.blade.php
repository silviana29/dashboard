<style id="mwork-ui-system">
    :root {
        color-scheme: light;
        --mw-bg: #f6f7f9;
        --mw-surface: #ffffff;
        --mw-surface-muted: #f8fafc;
        --mw-border: #e2e8f0;
        --mw-border-strong: #cbd5e1;
        --mw-text: #1e293b;
        --mw-muted: #64748b;
        --mw-primary: #4f46e5;
        --mw-primary-hover: #4338ca;
        --mw-radius: 12px;
        --mw-radius-lg: 16px;
        --mw-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 8px 24px rgba(15, 23, 42, .05);
    }

    html { background: var(--mw-bg); }
    body {
        background-color: var(--mw-bg) !important;
        color: var(--mw-text);
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
    }

    body > .pointer-events-none.fixed.inset-0 { display: none; }
    body [class*="backdrop-blur"] { backdrop-filter: none !important; -webkit-backdrop-filter: none !important; }
    body [class*="bg-gradient"] { background-image: none !important; }
    body [class*="shadow-soft"],
    body [class*="shadow-premium"],
    body [class*="shadow-float"],
    body [class*="shadow-glow"] { box-shadow: var(--mw-shadow) !important; }

    body .rounded-3xl,
    body [class*="rounded-[2"],
    body [class*="rounded-[1.7"] { border-radius: var(--mw-radius-lg) !important; }
    body .rounded-2xl,
    body [class*="rounded-[1.5"] { border-radius: var(--mw-radius) !important; }
    body .rounded-xl,
    body .rounded-lg { border-radius: 10px !important; }

    input:not([type="checkbox"]):not([type="radio"]), select, textarea {
        border-radius: 10px !important;
        border-color: var(--mw-border-strong) !important;
        background-color: #fff;
        color: var(--mw-text);
        box-shadow: none !important;
    }
    input:not([type="checkbox"]):not([type="radio"]):focus, select:focus, textarea:focus {
        border-color: var(--mw-primary) !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, .12) !important;
        outline: none;
    }
    button, a { transition-duration: .15s !important; }
    button:focus-visible, a:focus-visible { outline: 3px solid rgba(79, 70, 229, .22); outline-offset: 2px; }

    table { border-collapse: collapse; }
    table thead { background: var(--mw-surface-muted) !important; }
    table th { color: #475569; font-weight: 650; }
    table tbody tr:hover { background: #f8fafc; }

    dialog {
        border: 1px solid var(--mw-border) !important;
        border-radius: var(--mw-radius-lg) !important;
        box-shadow: 0 24px 64px rgba(15, 23, 42, .18) !important;
    }
    dialog::backdrop { background: rgba(15, 23, 42, .45) !important; backdrop-filter: none !important; }

    body > div[class*="lg:pl-72"] > header {
        min-height: 72px;
        border-color: var(--mw-border) !important;
        background: #fff !important;
    }
    body > div[class*="lg:pl-72"] > main { width: 100%; max-width: 1440px; margin-inline: auto; }
    body .bg-blue-700 { background-color: var(--mw-primary) !important; }
    body .hover\:bg-blue-800:hover { background-color: var(--mw-primary-hover) !important; }
    body .border-blue-800, body .border-blue-700 { border-color: var(--mw-primary) !important; }
    body .text-blue-700 { color: var(--mw-primary) !important; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; }
    }
</style>
