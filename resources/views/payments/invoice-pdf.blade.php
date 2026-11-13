<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $payment->status === 'paid' ? 'Invoice' : 'Proforma Invoice' }} {{ $payment->invoice_number }}</title>
    <style>
        @page { margin: 34px 38px 40px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #1e293b; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; }
        table { width: 100%; border-collapse: collapse; }
        .header { border-bottom: 3px solid #1d4ed8; padding-bottom: 18px; }
        .brand { color: #0f172a; font-size: 25px; font-weight: bold; letter-spacing: -1px; }
        .brand-mark { display: inline-block; margin-right: 9px; width: 29px; height: 29px; vertical-align: middle; }
        .muted { color: #64748b; }
        .right { text-align: right; }
        .title { color: #0f172a; font-size: 23px; font-weight: bold; letter-spacing: .6px; }
        .number { margin-top: 3px; color: #475569; font-size: 10px; }
        .status { display: inline-block; margin-top: 9px; border: 1px solid #cbd5e1; padding: 4px 9px; font-size: 9px; font-weight: bold; letter-spacing: .4px; }
        .status-paid { border-color: #86efac; background: #f0fdf4; color: #166534; }
        .status-pending { border-color: #fde68a; background: #fffbeb; color: #92400e; }
        .status-failed { border-color: #fecaca; background: #fef2f2; color: #991b1b; }
        .section { margin-top: 24px; }
        .section-title { margin-bottom: 7px; color: #64748b; font-size: 8px; font-weight: bold; letter-spacing: .8px; text-transform: uppercase; }
        .box { border: 1px solid #cbd5e1; padding: 13px 15px; vertical-align: top; }
        .box strong { color: #0f172a; font-size: 11px; }
        .gap { width: 12px; }
        .meta td { padding: 2px 0; }
        .meta .label { width: 45%; color: #64748b; }
        .items thead th { border: 1px solid #94a3b8; background: #e2e8f0; padding: 9px 10px; color: #334155; font-size: 8px; letter-spacing: .5px; text-align: left; text-transform: uppercase; }
        .items tbody td { border: 1px solid #cbd5e1; padding: 12px 10px; vertical-align: top; }
        .items .qty { width: 48px; text-align: center; }
        .items .amount { width: 112px; text-align: right; }
        .total-table { margin-top: 10px; margin-left: auto; width: 285px; }
        .total-table td { border-bottom: 1px solid #cbd5e1; padding: 8px 10px; }
        .total-table .grand td { border-top: 2px solid #334155; border-bottom: 2px solid #334155; color: #0f172a; font-size: 12px; font-weight: bold; }
        .note { border-left: 3px solid #1d4ed8; background: #eff6ff; padding: 11px 13px; color: #1e3a8a; }
        .footer { position: fixed; right: 0; bottom: -19px; left: 0; border-top: 1px solid #cbd5e1; padding-top: 8px; color: #64748b; font-size: 8px; }
    </style>
</head>
@php
    $logoPath = public_path('mwork.png');
    $logoData = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
    $statusLabel = match($payment->status) {
        'paid' => 'LUNAS',
        'failed' => 'PEMBAYARAN GAGAL',
        'cancelled' => 'DIBATALKAN',
        default => 'MENUNGGU PEMBAYARAN',
    };
    $statusClass = match($payment->status) {
        'paid' => 'status-paid',
        'failed', 'cancelled' => 'status-failed',
        default => 'status-pending',
    };
    $issuedAt = $payment->invoice_issued_at ?? $payment->created_at;
    $durationLabel = ($payment->duration_value ?? 1).' '.($payment->duration_unit === 'days' ? 'hari' : 'bulan');
@endphp
<body>
    <table class="header">
        <tr>
            <td>
                @if($logoData)<img class="brand-mark" src="{{ $logoData }}" alt="MWork">@endif
                <span class="brand">MWork</span>
                <div class="muted" style="margin-top: 5px;">Sistem operasional dan kehadiran tim</div>
            </td>
            <td class="right">
                <div class="title">{{ $payment->status === 'paid' ? 'INVOICE' : 'PROFORMA INVOICE' }}</div>
                <div class="number">{{ $payment->invoice_number }}</div>
                <span class="status {{ $statusClass }}">{{ $statusLabel }}</span>
            </td>
        </tr>
    </table>

    <table class="section">
        <tr>
            <td class="box" width="52%">
                <div class="section-title">Ditagihkan kepada</div>
                <strong>{{ $organization->name }}</strong>
                <div class="muted" style="margin-top: 4px;">{{ $organization->type_label }}</div>
                <div style="margin-top: 10px;">{{ $payment->customer_email }}</div>
                @if($payment->customer_phone)<div>{{ $payment->customer_phone }}</div>@endif
            </td>
            <td class="gap"></td>
            <td class="box" width="43%">
                <div class="section-title">Detail invoice</div>
                <table class="meta">
                    <tr><td class="label">Tanggal terbit</td><td class="right">{{ $issuedAt?->translatedFormat('d F Y') }}</td></tr>
                    <tr><td class="label">Status</td><td class="right"><strong>{{ $statusLabel }}</strong></td></tr>
                    <tr><td class="label">Masa aktif</td><td class="right">{{ $durationLabel }}</td></tr>
                    @if($payment->paid_at)<tr><td class="label">Tanggal bayar</td><td class="right">{{ $payment->paid_at->translatedFormat('d F Y') }}</td></tr>@endif
                </table>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Rincian layanan</div>
        <table class="items">
            <thead>
                <tr><th>Deskripsi</th><th class="qty">Jumlah</th><th class="amount">Harga</th><th class="amount">Subtotal</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong style="font-size: 11px;">Paket MWork {{ $plan?->name ?? 'Workspace' }}</strong>
                        <div class="muted" style="margin-top: 4px;">Masa aktif {{ $durationLabel }}</div>
                        @if($payment->period_start && $payment->period_end)
                            <div class="muted">Perkiraan periode {{ $payment->period_start->translatedFormat('d M Y') }} - {{ $payment->period_end->translatedFormat('d M Y') }}</div>
                        @endif
                    </td>
                    <td class="qty">1</td>
                    <td class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="amount"><strong>Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
        <table class="total-table">
            <tr><td class="muted">Subtotal</td><td class="right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td></tr>
            <tr class="grand"><td>Total</td><td class="right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td></tr>
        </table>
    </div>

    <div class="section note">
        @if($payment->status === 'paid')
            Dokumen ini menyatakan pembayaran telah diterima dan dapat digunakan sebagai bukti pembelian layanan MWork.
        @else
            Dokumen ini dapat digunakan sebagai proposal pembelian. Paket akan aktif setelah pembayaran berhasil dikonfirmasi.
        @endif
    </div>

    <div class="footer">
        <table><tr><td>MWork by Masimasa</td><td class="right">Invoice {{ $payment->invoice_number }}</td></tr></table>
    </div>
</body>
</html>
