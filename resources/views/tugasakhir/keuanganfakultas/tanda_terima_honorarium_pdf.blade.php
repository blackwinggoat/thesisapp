<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tanda Terima Honorarium</title>
    <style>
        @page { margin: 7mm 10mm 19mm; }
        * { box-sizing: border-box; }
        body { color: #000; font-family: "Times New Roman", serif; font-size: 9.2pt; line-height: 1.1; margin: 0; }
        .document { page-break-after: always; width: 100%; }
        .document:last-child { page-break-after: auto; }
        .letterhead { padding-top: 0; }
        .letterhead table, .identity, .receipt-table, .signature { border-collapse: collapse; width: 100%; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 41px; width: auto; }
        .logo-fikom { height: 33px; margin-left: 7px; vertical-align: middle; width: auto; }
        .letterhead-title { font-size: 10.6pt; font-weight: bold; line-height: 1.01; text-align: right; }
        .letterhead-divider { border-top: 3px double #000; margin-top: 4px; }
        .address { font-size: 7pt; line-height: 1.08; padding-top: 4px; text-align: center; }
        .contact-line { padding-top: 1px; white-space: nowrap; }
        .document-title { font-size: 12pt; font-weight: bold; margin: 9px 0 1px; text-align: center; text-decoration: underline; }
        .document-subtitle { font-size: 9.2pt; margin-bottom: 6px; text-align: center; }
        .identity { margin-bottom: 5px; }
        .identity td { padding: 1px 0; vertical-align: top; }
        .identity .label { width: 21%; }
        .identity .separator { text-align: center; width: 3%; }
        .identity .value { font-weight: bold; width: 76%; }
        .date-section { margin-top: 6px; page-break-inside: avoid; }
        .date-heading { background: #374151; color: #fff; font-size: 8.6pt; font-weight: bold; padding: 3px 5px; page-break-after: avoid; }
        .receipt-table { font-size: 7.2pt; margin-top: 0; page-break-before: avoid; table-layout: fixed; }
        .receipt-table thead { display: table-header-group; }
        .receipt-table tr { page-break-inside: avoid; }
        .receipt-table th, .receipt-table td { border: 1px solid #000; padding: 2px 3px; vertical-align: middle; }
        .receipt-table th { background: #e5e7eb; font-weight: bold; text-align: center; }
        .receipt-table .number { text-align: center; width: 5%; }
        .receipt-table .student { width: 30%; }
        .receipt-table .exam { width: 16%; }
        .receipt-table .role { width: 19%; }
        .receipt-table .amount { text-align: right; width: 30%; }
        .receipt-table .total-label { font-weight: bold; text-align: right; }
        .receipt-table .total-amount { font-weight: bold; text-align: right; }
        .adjustment-section { page-break-inside: avoid; }
        .adjustment-title { font-size: 8.2pt; font-weight: bold; margin: 5px 0 2px; page-break-after: avoid; }
        .adjustment-table { border-collapse: collapse; font-size: 7.1pt; page-break-before: avoid; table-layout: fixed; width: 100%; }
        .adjustment-table th, .adjustment-table td { border: 1px solid #000; padding: 2px; vertical-align: top; }
        .adjustment-table th { background: #f3f4f6; text-align: center; }
        .adjustment-table .number { text-align: center; width: 5%; }
        .adjustment-table .student { width: 27%; }
        .adjustment-table .role { width: 17%; }
        .adjustment-table .kind { text-align: center; width: 14%; }
        .adjustment-table .amount { text-align: right; width: 13%; }
        .adjustment-table .basis { width: 24%; }
        .overall-summary { border-collapse: collapse; display: table; font-size: 8pt; margin: 6px 0 0; page-break-inside: avoid; width: 100%; }
        .overall-summary td { border: 1px solid #000; padding: 2px 4px; }
        .overall-summary .label { font-weight: bold; text-align: right; width: 78%; }
        .overall-summary .value { font-weight: bold; text-align: right; width: 22%; }
        .grand-total { border: 2px solid #000; font-size: 10.5pt; font-weight: bold; margin-top: 10px; padding: 6px 8px; text-align: right; }
        .statement { margin: 7px 0 0; page-break-inside: avoid; text-align: justify; }
        .signature { margin-top: 8px; page-break-inside: avoid; }
        .signature td { vertical-align: top; }
        .signature-note { font-size: 8.2pt; padding-right: 16px; width: 54%; }
        .signature-receiver { text-align: center; width: 46%; }
        .signature-space { height: 38px; }
        .receiver-name { font-weight: bold; text-decoration: underline; }
        .page-note { color: #555; font-size: 6.6pt; margin-top: 4px; text-align: right; }
    </style>
</head>
<body>
    @php
        $formatPenyesuaianHonor = function ($nilai) {
            $nilai = (float) $nilai;
            if ($nilai > 0) {
                return '+' . helper::formatRupiah($nilai);
            }
            if ($nilai < 0) {
                return '-' . helper::formatRupiah(abs($nilai));
            }
            return helper::formatRupiah(0);
        };
    @endphp
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

            <div class="document-title">TANDA TERIMA HONORARIUM</div>
            <div class="document-subtitle">Pelaksanaan Ujian Proposal dan Tugas Akhir</div>

            <table class="identity">
                <tr><td class="label">Tanggal Ujian</td><td class="separator">:</td><td class="value">{{ $report->tanggal->pluck('tanggal')->map(function ($tanggal) { return helper::tgl_indo_lengkap($tanggal); })->implode(', ') }}</td></tr>
                <tr><td class="label">Nama Dosen</td><td class="separator">:</td><td class="value">{{ $report->nama_dosen }}</td></tr>
                <tr><td class="label">NIDN / Kode Dosen</td><td class="separator">:</td><td class="value">{{ $report->kode_dosen }}</td></tr>
            </table>

            @foreach ($report->tanggal as $laporanTanggal)
                <div class="date-section">
                    <div class="date-heading">Tanggal Ujian: {{ helper::tgl_indo_lengkap($laporanTanggal->tanggal) }}</div>
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th class="number">No.</th>
                                <th class="student">Mahasiswa / NIM</th>
                                <th class="exam">Jenis Ujian</th>
                                <th class="role">Peran</th>
                                <th class="amount">Honor Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($laporanTanggal->items as $item)
                            <tr>
                                <td class="number">{{ $loop->iteration }}</td>
                                <td class="student"><strong>{{ $item->nama_mahasiswa }}</strong><br>{{ $item->nim }}</td>
                                <td class="exam">{{ $item->tipe_ujian }}</td>
                                <td class="role">{{ $item->peran }}</td>
                                <td class="amount">{{ helper::formatRupiah($item->honor_awal) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="total-label">SUBTOTAL PENERIMAAN {{ helper::tgl_indo_lengkap($laporanTanggal->tanggal) }}</td>
                            <td class="total-amount">{{ helper::formatRupiah($laporanTanggal->subtotal_penerimaan) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach

            @if ($report->adjustments->isNotEmpty())
                <div class="adjustment-section">
                    <div class="adjustment-title">Tabel Penyesuaian Honorarium</div>
                    <table class="adjustment-table">
                        <thead>
                            <tr>
                                <th class="number">No.</th>
                                <th class="student">Mahasiswa / NIM</th>
                                <th class="role">Peran</th>
                                <th class="kind">Jenis</th>
                                <th class="amount">Nilai</th>
                                <th class="basis">Dasar Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report->adjustments as $adjustment)
                                <tr>
                                    <td class="number">{{ $loop->iteration }}</td>
                                    <td class="student"><strong>{{ $adjustment->nama_mahasiswa }}</strong><br>{{ $adjustment->nim }}<br><span style="font-size: 7pt;">Ujian: {{ helper::tgl_indo_lengkap($adjustment->tanggal) }}</span></td>
                                    <td class="role">{{ $adjustment->peran }}</td>
                                    <td class="kind">{{ $adjustment->jenis }}</td>
                                    <td class="amount">{{ helper::formatRupiah($adjustment->nilai) }}</td>
                                    <td class="basis">{{ $adjustment->dasar }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <table class="overall-summary">
                <tr>
                    <td class="label">TOTAL SUBTOTAL PENERIMAAN</td>
                    <td class="value">{{ helper::formatRupiah($report->total_penerimaan) }}</td>
                </tr>
                <tr>
                    <td class="label">TOTAL PENYESUAIAN HONORARIUM</td>
                    <td class="value">{{ $formatPenyesuaianHonor($report->total_penyesuaian) }}</td>
                </tr>
                <tr>
                    <td class="label">TOTAL HONORARIUM SELURUH JADWAL</td>
                    <td class="value">{{ helper::formatRupiah($report->total_honor) }}</td>
                </tr>
            </table>

            <p class="statement">Dengan ini saya menyatakan telah menerima honorarium pelaksanaan ujian untuk seluruh jadwal yang dirinci di atas dengan jumlah sebesar <strong>{{ helper::formatRupiah($report->total_honor) }}</strong>.</p>

            <table class="signature">
                <tr>
                    <td class="signature-note">Dokumen ini menjadi bukti penerimaan honorarium dosen untuk seluruh jadwal ujian yang tercantum di atas.</td>
                    <td class="signature-receiver">Makassar, {{ helper::tgl_indo_lengkap(date('Y-m-d')) }}<br>Penerima,
                        <div class="signature-space"></div>
                        <span class="receiver-name">{{ $report->nama_dosen }}</span><br>
                        {{ $report->kode_dosen }}
                    </td>
                </tr>
            </table>

            <div class="page-note">Lembar dosen {{ $loop->iteration }} dari {{ $reports->count() }}</div>
        </div>
    @endforeach
</body>
</html>
