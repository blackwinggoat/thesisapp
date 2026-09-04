<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Persebaran Jenis Tugas Akhir</title>
    <style>
        @page { margin: 8mm 10mm 10mm; }
        * { box-sizing: border-box; }
        body { color: #111827; font-family: "Times New Roman", serif; font-size: 8.5pt; line-height: 1.18; margin: 0; }
        .letterhead table { border-collapse: collapse; width: 100%; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 42px; width: auto; }
        .logo-fikom { height: 34px; margin-left: 8px; vertical-align: middle; width: auto; }
        .letterhead-title { font-size: 10.5pt; font-weight: bold; line-height: 1.04; text-align: right; }
        .letterhead-divider { border-top: 3px double #111; margin-top: 4px; }
        .letterhead-address { font-size: 7pt; line-height: 1.15; padding-top: 4px; text-align: center; }
        .report-title { font-size: 13.5pt; font-weight: bold; margin: 9px 0 1px; text-align: center; text-decoration: underline; }
        .report-subtitle { font-size: 10pt; font-weight: bold; line-height: 1.18; margin-bottom: 7px; text-align: center; }
        .meta { border-collapse: collapse; margin-bottom: 6px; width: 100%; }
        .meta td { padding: 1.5px 4px; vertical-align: top; }
        .meta-label { color: #374151; font-weight: bold; width: 13%; }
        .meta-separator { width: 1.5%; }
        .summary { border-collapse: collapse; margin: 0 0 7px; width: 100%; }
        .summary td { border: 1px solid #aeb8c4; padding: 5px 8px; text-align: center; vertical-align: middle; width: 25%; }
        .summary strong { display: block; font-size: 13pt; line-height: 1; margin-bottom: 2px; }
        .section-title { background: #354052; color: #fff; font-size: 9pt; font-weight: bold; margin: 7px 0 0; padding: 4px 7px; }
        .section-note { color: #374151; font-size: 6.8pt; line-height: 1.2; padding: 3px 1px; }
        .comparison-page-break { page-break-before: always; }
        table.report-table { border-collapse: collapse; font-size: 7.3pt; width: 100%; }
        .report-table thead { display: table-header-group; }
        .report-table tr { page-break-inside: avoid; }
        .report-table th, .report-table td { border: 1px solid #4b5563; padding: 2.4px 4px; vertical-align: middle; }
        .report-table th { background: #e5e9ee; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }
        .type-code { font-weight: bold; }
        .comparison-table { font-size: 6.8pt !important; }
        .quality-note { background: #fff8e6; border: 1px solid #d7b56d; margin-top: 6px; padding: 5px 8px; }
        .signature-wrap { border-collapse: collapse; margin: 7px 0 0; page-break-inside: avoid; width: 100%; }
        .signature-wrap td { vertical-align: top; }
        .signature-note { color: #374151; font-size: 7.2pt; line-height: 1.25; padding: 6px 12px 0 0; }
        .signature { text-align: center; width: 38%; }
        .signature-heading { line-height: 1.3; }
        .signature-qr-box { height: 58px; padding: 5px 0; text-align: center; }
        .signature-identity { line-height: 1.3; }
        .verification-qr { display: block; height: 48px; margin: 0 auto; width: 48px; }
        .official-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="letterhead">
        <table>
            <tr>
                <td width="43%">
                    <img class="logo-umi" src="{{ \App\Helper::publicImageDataUri('images/branding/umi-pdf.jpg') }}" alt="Logo UMI">
                    <img class="logo-fikom" src="{{ \App\Helper::publicImageDataUri('images/branding/fikom-pdf.jpg') }}" alt="Logo FIKOM">
                </td>
                <td class="letterhead-title" width="57%">
                    YAYASAN WAKAF UMI<br>
                    UNIVERSITAS MUSLIM INDONESIA<br>
                    FAKULTAS ILMU KOMPUTER
                </td>
            </tr>
        </table>
        <div class="letterhead-divider"></div>
        <div class="letterhead-address">
            Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI HP/WA. 0811-4224-449 Makassar 90231<br>
            website: fikom.umi.ac.id, email: {{ $emailProgramStudi }}
        </div>
    </div>

    <div class="report-title">LAPORAN PERSEBARAN JENIS TUGAS AKHIR</div>
    <div class="report-subtitle">
        MAHASISWA LULUS PROGRAM STUDI {{ strtoupper($scope['program_studi']) }}<br>
        BERDASARKAN {{ strtoupper($report['mode_label']) }} {{ strtoupper($report['selected_period'] ?: '-') }}
    </div>

    <table class="meta">
        <tr>
            <td class="meta-label">Program Studi</td><td class="meta-separator">:</td><td>{{ $scope['program_studi'] }}</td>
            <td class="meta-label">Tanggal Cetak</td><td class="meta-separator">:</td><td>{{ helper::tgl_indo_lengkap($report['generated_at']->format('Y-m-d')) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Dasar Laporan</td><td class="meta-separator">:</td><td>Status lulus dan jadwal ujian akhir Thesis Apps</td>
            <td class="meta-label">Waktu Cetak</td><td class="meta-separator">:</td><td>{{ $report['generated_at']->format('H:i') }} WITA</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td><strong>{{ number_format($report['summary']['total']) }}</strong>Mahasiswa lulus</td>
            <td><strong>{{ number_format($report['summary']['type_count']) }}</strong>Jenis tugas akhir</td>
            <td><strong>{{ $report['summary']['dominant_code'] }}</strong>Jenis terbanyak</td>
            <td><strong>{{ number_format($report['summary']['context_count']) }}</strong>{{ $report['summary']['context_label'] }}</td>
        </tr>
    </table>

    <div class="section-title">A. Distribusi Jenis Tugas Akhir</div>
    <table class="report-table">
        <thead>
            <tr><th style="width: 5%;">No</th><th style="width: 12%;">Kode</th><th>Jenis Tugas Akhir</th><th style="width: 12%;">Jumlah</th><th style="width: 14%;">Persentase</th></tr>
        </thead>
        <tbody>
            @forelse ($report['distribution'] as $index => $type)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center type-code">{{ $type['code'] }}</td>
                    <td>{{ $type['description'] }}</td>
                    <td class="center">{{ number_format($type['count']) }}</td>
                    <td class="center">{{ number_format($type['percentage'], 2, ',', '.') }}%</td>
                </tr>
            @empty
                <tr><td colspan="5" class="center">Belum ada data mahasiswa lulus pada periode ini.</td></tr>
            @endforelse
            @if ($report['summary']['total'] > 0)
                <tr>
                    <td colspan="3" class="right"><strong>TOTAL</strong></td>
                    <td class="center"><strong>{{ number_format($report['summary']['total']) }}</strong></td>
                    <td class="center"><strong>100,00%</strong></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="section-title{{ count($report['cross_distribution']) > 10 ? ' comparison-page-break' : '' }}">B. {{ $report['cross_title'] }}</div>
    <div class="section-note">{{ $report['cross_note'] }} Nilai pada setiap sel menunjukkan jumlah mahasiswa dan persentasenya.</div>
    <table class="report-table comparison-table">
        <thead>
            <tr>
                <th>{{ $report['cross_dimension_label'] }}</th>
                @foreach ($report['type_columns'] as $type)
                    <th>{{ $type['code'] }}<br><span style="font-weight: normal;">Jumlah / %</span></th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['cross_distribution'] as $period)
                <tr>
                    <td class="center"><strong>{{ $period['period'] }}</strong></td>
                    @foreach ($report['type_columns'] as $type)
                        <td class="center nowrap">
                            {{ number_format($period['counts'][$type['code']]['count']) }} /
                            {{ number_format($period['counts'][$type['code']]['percentage'], 2, ',', '.') }}%
                        </td>
                    @endforeach
                    <td class="center"><strong>{{ number_format($period['total']) }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="{{ count($report['type_columns']) + 2 }}" class="center">Belum ada data persebaran silang.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($report['summary']['fallback_date_count'] > 0 || $report['summary']['default_type_count'] > 0)
        <div class="quality-note">
            <strong>Catatan kualitas data:</strong>
            @if ($report['summary']['fallback_date_count'] > 0)
                {{ number_format($report['summary']['fallback_date_count']) }} mahasiswa belum memiliki tanggal kelulusan yang dapat ditelusuri.
            @endif
            @if ($report['summary']['default_type_count'] > 0)
                {{ number_format($report['summary']['default_type_count']) }} mahasiswa belum memiliki jenis tugas akhir dan sementara diklasifikasikan sebagai TA-SM.
            @endif
        </div>
    @endif

    <table class="signature-wrap">
        <tr>
            <td class="signature-note" width="62%">
                Laporan ini disahkan secara elektronik. QR memuat tautan verifikasi metadata,
                jumlah mahasiswa, periode, dan sidik laporan tanpa memublikasikan identitas mahasiswa.
            </td>
            <td class="signature">
                <div class="signature-heading">
                    Makassar, {{ helper::tgl_indo_lengkap($report['generated_at']->format('Y-m-d')) }}<br>
                    Ketua Program Studi {{ $scope['program_studi'] }},
                </div>
                <div class="signature-qr-box">
                    <a href="{{ $verificationUrl }}">
                        <img class="verification-qr" src="{{ \App\Helper::qrCodeDataUri($verificationUrl, 130) }}" alt="QR verifikasi laporan">
                    </a>
                </div>
                <div class="signature-identity">
                    <span class="official-name">{{ $kaprodi->nama ?: '-' }}</span><br>
                    NIDN: {{ $kaprodi->nidn ?: '-' }}
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
