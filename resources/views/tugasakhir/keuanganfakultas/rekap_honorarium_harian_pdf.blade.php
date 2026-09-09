<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Honorarium Harian</title>
    <style>
        @page { margin: 6mm 9mm 8mm; }
        * { box-sizing: border-box; }
        body { color: #111827; font-family: "Times New Roman", serif; font-size: 7.8pt; line-height: 1.08; margin: 0; }
        .document { page-break-after: always; width: 100%; }
        .document:last-child { page-break-after: auto; }
        .letterhead table, .summary, .metric-table, .report-table, .signature { border-collapse: collapse; width: 100%; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 38px; width: auto; }
        .logo-fikom { height: 30px; margin-left: 8px; vertical-align: middle; width: auto; }
        .letterhead-title { color: #000; font-size: 11.2pt; font-weight: bold; line-height: 1.01; text-align: right; }
        .letterhead-divider { border-top: 3px double #000; margin-top: 3px; }
        .address { color: #000; font-size: 7pt; line-height: 1.08; padding-top: 3px; text-align: center; }
        .contact-line { padding-top: 1px; }
        .document-title { color: #111; font-size: 12.5pt; font-weight: bold; margin: 6px 0 1px; text-align: center; text-decoration: underline; }
        .document-subtitle { font-size: 8.6pt; margin-bottom: 4px; text-align: center; }
        .summary { margin-bottom: 4px; }
        .summary td { padding: 0.5px 0; vertical-align: top; }
        .summary .label { width: 13%; }
        .summary .separator { text-align: center; width: 2%; }
        .summary .value { font-weight: bold; width: 85%; }
        .metric-table { margin-bottom: 4px; table-layout: fixed; }
        .metric-table td { border: 1px solid #9ca3af; padding: 3px 5px; text-align: center; vertical-align: middle; }
        .metric-value { color: #111827; display: block; font-size: 11.5pt; font-weight: bold; }
        .metric-label { color: #4b5563; display: block; font-family: Arial, sans-serif; font-size: 6.3pt; margin-top: 1px; text-transform: uppercase; }
        .section { margin-top: 4px; page-break-inside: auto; }
        .section-title { background: #374151; color: #fff; font-family: Arial, sans-serif; font-size: 7.2pt; font-weight: bold; padding: 3px 5px; page-break-after: avoid; text-transform: uppercase; }
        .report-table { font-size: 6.8pt; line-height: 1.02; page-break-before: avoid; table-layout: fixed; }
        .report-table thead { display: table-header-group; }
        .report-table tr { page-break-inside: avoid; }
        .report-table th, .report-table td { border: 1px solid #6b7280; padding: 2px 3px; vertical-align: middle; }
        .report-table th { background: #e5e7eb; color: #111827; font-family: Arial, sans-serif; font-size: 6.5pt; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .right { text-align: right; }
        .number { width: 5%; }
        .type-name { width: 42%; }
        .type-count { width: 14%; }
        .type-assignment { width: 17%; }
        .type-amount { width: 22%; }
        .lecturer-name { width: 29%; }
        .lecturer-student { width: 10%; }
        .lecturer-assignment { width: 11%; }
        .lecturer-role { width: 32%; }
        .lecturer-amount { width: 18%; }
        .official-id { color: #4b5563; font-size: 6.2pt; }
        .total-row td { background: #f3f4f6; font-weight: bold; }
        .grand-total { border: 2px solid #111827; font-size: 9.5pt; font-weight: bold; margin-top: 5px; padding: 4px 6px; text-align: right; }
        .statement { margin: 4px 0 0; page-break-inside: avoid; text-align: justify; }
        .signature { margin-top: 4px; page-break-inside: avoid; }
        .signature td { vertical-align: top; }
        .signature-note { color: #4b5563; font-family: Arial, sans-serif; font-size: 6.4pt; line-height: 1.22; padding-right: 18px; width: 58%; }
        .signature-official { color: #000; text-align: center; width: 42%; }
        .signature-heading { line-height: 1.15; }
        .signature-qr-box { height: 57px; text-align: center; }
        .signature-qr-box a { display: block; height: 54px; margin: 1px auto; width: 54px; }
        .verification-qr { display: block; height: 54px; margin: 0; width: 54px; }
        .signature-identity { line-height: 1.15; min-height: 23px; }
        .official-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    @foreach ($reports as $report)
        <div class="document">
            <div class="letterhead">
                <table>
                    <tr>
                        <td width="43%"><img class="logo-umi" src="{{ \App\Helper::publicImageDataUri('images/branding/umi-pdf.jpg') }}" alt="Logo UMI"><img class="logo-fikom" src="{{ \App\Helper::publicImageDataUri('images/branding/fikom-pdf.jpg') }}" alt="Logo FIKOM"></td>
                        <td class="letterhead-title" width="57%">YAYASAN WAKAF UMI<br>UNIVERSITAS MUSLIM INDONESIA<br>FAKULTAS ILMU KOMPUTER</td>
                    </tr>
                </table>
                <div class="letterhead-divider"></div>
                <div class="address">
                    Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI HP/WA. 0811-4224-449 Makassar 90231
                    <div class="contact-line">Website: fikom.umi.ac.id, Email: fikom@umi.ac.id</div>
                </div>
            </div>

            <div class="document-title">REKAP HONORARIUM HARIAN</div>
            <div class="document-subtitle">Pelaksanaan Ujian Proposal dan Tugas Akhir</div>

            <table class="summary">
                <tr><td class="label">Tanggal Ujian</td><td class="separator">:</td><td class="value">{{ helper::tgl_indo_lengkap($report->tanggal) }}</td></tr>
                <tr><td class="label">Unit Kerja</td><td class="separator">:</td><td class="value">Fakultas Ilmu Komputer, Universitas Muslim Indonesia</td></tr>
            </table>

            <table class="metric-table">
                <tr>
                    <td><span class="metric-value">{{ number_format($report->student_count) }}</span><span class="metric-label">Mahasiswa Ujian</span></td>
                    <td><span class="metric-value">{{ number_format($report->exam_type_count) }}</span><span class="metric-label">Jenis Ujian</span></td>
                    <td><span class="metric-value">{{ number_format($report->lecturer_count) }}</span><span class="metric-label">Dosen Penerima</span></td>
                    <td><span class="metric-value">{{ number_format($report->assignment_count) }}</span><span class="metric-label">Penugasan Dosen</span></td>
                    <td><span class="metric-value">{{ helper::formatRupiah($report->total_honor) }}</span><span class="metric-label">Total Honorarium</span></td>
                </tr>
            </table>

            <div class="section">
                <div class="section-title">Rincian Jenis Ujian</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="number">No.</th>
                            <th class="type-name">Jenis Ujian</th>
                            <th class="type-count">Mahasiswa</th>
                            <th class="type-assignment">Penugasan Dosen</th>
                            <th class="type-amount">Honorarium</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report->exam_types as $type)
                            <tr>
                                <td class="center">{{ $loop->iteration }}</td>
                                <td>{{ $type->name }}</td>
                                <td class="center">{{ number_format($type->student_count) }}</td>
                                <td class="center">{{ number_format($type->assignment_count) }}</td>
                                <td class="right">{{ helper::formatRupiah($type->total_honor) }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="2" class="right">TOTAL</td>
                            <td class="center">{{ number_format($report->student_count) }}</td>
                            <td class="center">{{ number_format($report->assignment_count) }}</td>
                            <td class="right">{{ helper::formatRupiah($report->total_honor) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="section">
                <div class="section-title">Rekap Penerimaan Dosen</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th class="number">No.</th>
                            <th class="lecturer-name">Dosen / NIDN atau Kode Dosen</th>
                            <th class="lecturer-student">Mahasiswa</th>
                            <th class="lecturer-assignment">Penugasan</th>
                            <th class="lecturer-role">Peran</th>
                            <th class="lecturer-amount">Honorarium Diterima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report->lecturers as $lecturer)
                            <tr>
                                <td class="center">{{ $loop->iteration }}</td>
                                <td><strong>{{ $lecturer->name }}</strong> <span class="official-id">({{ $lecturer->code }})</span></td>
                                <td class="center">{{ number_format($lecturer->student_count) }}</td>
                                <td class="center">{{ number_format($lecturer->assignment_count) }}</td>
                                <td>{{ $lecturer->roles }}</td>
                                <td class="right">{{ helper::formatRupiah($lecturer->total_honor) }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="5" class="right">TOTAL HONORARIUM {{ strtoupper(helper::tgl_indo_lengkap($report->tanggal)) }}</td>
                            <td class="right">{{ helper::formatRupiah($report->total_honor) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="grand-total">TOTAL HONORARIUM HARIAN: {{ helper::formatRupiah($report->total_honor) }}</div>
            <p class="statement">Rekap ini merangkum honorarium pelaksanaan ujian pada tanggal {{ helper::tgl_indo_lengkap($report->tanggal) }} berdasarkan tipe ujian, penugasan dosen, dan penyesuaian kehadiran pembimbing yang tercatat di Thesis App FIKOM UMI.</p>

            <table class="signature">
                <tr>
                    <td class="signature-note">QR digunakan untuk memeriksa metadata penerbitan dokumen. Halaman verifikasi tidak menampilkan identitas mahasiswa maupun rincian nilai honorarium.</td>
                    <td class="signature-official">
                        <div class="signature-heading">Makassar, {{ helper::tgl_indo_lengkap($generatedAt->format('Y-m-d')) }}<br>Wakil Dekan II Bidang Keuangan dan SDM,</div>
                        <div class="signature-qr-box"><a href="{{ $report->verification_url }}"><img class="verification-qr" src="{{ \App\Helper::qrCodeDataUri($report->verification_url, 120) }}" alt="QR verifikasi rekap honorarium"></a></div>
                        <div class="signature-identity"><span class="official-name">{{ $wakilDekanDua->nama }}</span><br>{{ $wakilDekanDua->nip_nidn ? 'NIP/NIDN: ' . $wakilDekanDua->nip_nidn : '' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</body>
</html>
