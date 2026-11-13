<!DOCTYPE html>
<html lang="id">
<body style="margin:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#1e293b">
    <div style="max-width:620px;margin:0 auto;padding:32px 16px">
        <div style="background:#ffffff;border:1px solid #cbd5e1">
            <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0">
                <strong style="font-size:20px;color:#1d4ed8">MWork</strong>
            </div>
            <div style="padding:28px 24px">
                <h1 style="margin:0 0 12px;font-size:22px">Masa aktif paket akan segera berakhir</h1>
                <p style="margin:0 0 20px;line-height:1.6;color:#475569">Paket <strong>{{ $plan?->name }}</strong> untuk workspace <strong>{{ $organization->name }}</strong> aktif sampai {{ $organization->subscription_ends_at->translatedFormat('d F Y') }}.</p>
                <div style="padding:14px 16px;background:#eff6ff;border:1px solid #bfdbfe;line-height:1.6;color:#1e40af">Silakan perpanjang sebelum tanggal tersebut agar akun anggota dan fitur paket tetap dapat digunakan.</div>
                <p style="margin:22px 0 0;line-height:1.6;color:#64748b;font-size:13px">Jika tidak diperpanjang, workspace kembali ke paket Free. Admin tetap dapat masuk untuk melakukan perpanjangan.</p>
            </div>
        </div>
        <p style="margin:14px 0 0;text-align:center;color:#94a3b8;font-size:12px">Email otomatis dari MWork</p>
    </div>
</body>
</html>
