<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $code }} · {{ $title }} · MWork</title>
    <meta name="robots" content="noindex,nofollow">
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#0f172a;background:#f6f7fb}a{text-decoration:none}.shell{position:relative;display:grid;min-height:100vh;place-items:center;overflow:hidden;padding:24px}.glow{position:absolute;width:440px;height:440px;border-radius:999px;filter:blur(90px);opacity:.16}.glow.one{left:-180px;top:-180px;background:#4f46e5}.glow.two{right:-180px;bottom:-180px;background:{{ $accent ?? '#4f46e5' }}}.card{position:relative;width:min(100%,760px);overflow:hidden;border:1px solid #e2e8f0;border-radius:32px;background:rgba(255,255,255,.94);box-shadow:0 30px 80px rgba(15,23,42,.12)}.top{display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;padding:22px 28px}.brand{display:flex;align-items:center;gap:12px;font-size:14px;font-weight:700}.logo{display:grid;width:42px;height:42px;place-items:center;border-radius:13px;color:#fff;background:#4f46e5}.by{font-size:12px;font-weight:500;color:#94a3b8}.status{border-radius:999px;padding:8px 12px;font-size:11px;font-weight:800;letter-spacing:.12em;color:{{ $accent ?? '#4f46e5' }};background:#f8fafc}.content{padding:52px 28px 42px;text-align:center}.number{font-size:clamp(72px,18vw,150px);font-weight:800;line-height:.8;letter-spacing:-.08em;color:#e2e8f0}.icon{display:grid;width:66px;height:66px;margin:-12px auto 26px;place-items:center;border-radius:22px;font-size:28px;color:#fff;background:{{ $accent ?? '#4f46e5' }};box-shadow:0 14px 30px color-mix(in srgb, {{ $accent ?? '#4f46e5' }} 25%, transparent)}h1{margin:0;font-size:clamp(28px,5vw,42px);letter-spacing:-.04em}p{max-width:540px;margin:14px auto 0;color:#64748b;font-size:15px;line-height:1.8}.actions{display:flex;flex-wrap:wrap;justify-content:center;gap:12px;margin-top:30px}.primary,.secondary{display:inline-flex;min-height:46px;align-items:center;justify-content:center;border-radius:13px;padding:0 20px;font-size:14px;font-weight:700}.primary{color:#fff;background:#111827}.secondary{border:1px solid #cbd5e1;color:#475569;background:#fff}.footer{margin-top:32px;font-size:12px;color:#94a3b8}@media(max-width:520px){.top{padding:18px 20px}.content{padding:42px 20px 34px}.status{display:none}.actions{flex-direction:column}.primary,.secondary{width:100%}}
    </style>
    @include('partials.pwa')
    <style>.glow{display:none!important}.shell .card{border-radius:16px!important;box-shadow:0 8px 30px rgba(15,23,42,.07)!important}.shell .icon,.shell .logo,.shell .primary,.shell .secondary{border-radius:10px!important}</style>
</head>
<body>
    <main class="shell">
        <div class="glow one"></div><div class="glow two"></div>
        <section class="card">
            <header class="top"><div class="brand"><span class="logo">M</span><span>MWork <span class="by">Attendance</span></span></div><span class="status">ERROR {{ $code }}</span></header>
            <div class="content">
                <div class="number">{{ $code }}</div>
                <div class="icon">{{ $icon ?? '!' }}</div>
                <h1>{{ $title }}</h1>
                <p>{{ $message }}</p>
                <div class="actions">
                    <a href="{{ url('/') }}" class="primary">Kembali ke halaman utama</a>
                    <a href="javascript:history.back()" class="secondary">← Kembali sebelumnya</a>
                </div>
                <div class="footer">Jika masalah terus terjadi, hubungi admin perusahaan Anda · by masimasa</div>
            </div>
        </section>
    </main>
</body>
</html>
