<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tanda Terima Honorarium</title>
    <style>
        @page { margin: 10mm 13mm 10mm; }
        * { box-sizing: border-box; }
        body { color: #000; font-family: "Times New Roman", serif; font-size: 10pt; line-height: 1.2; margin: 0; }
        .document { page-break-after: always; width: 100%; }
        .document:last-child { page-break-after: auto; }
        .letterhead { padding-top: 2px; }
        .letterhead table, .identity, .receipt-table, .signature { border-collapse: collapse; width: 100%; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 50px; width: auto; }
        .logo-fikom { height: 40px; margin-left: 10px; vertical-align: middle; width: auto; }
        .letterhead-title { font-size: 11.7pt; font-weight: bold; line-height: 1.05; text-align: right; }
        .letterhead-divider { border-top: 3px double #000; margin-top: 7px; }
        .address { font-size: 7.6pt; line-height: 1.18; padding-top: 8px; text-align: center; }
        .contact-line { padding-top: 2px; white-space: nowrap; }
        .document-title { font-size: 13pt; font-weight: bold; margin: 18px 0 2px; text-align: center; text-decoration: underline; }
        .document-subtitle { font-size: 10pt; margin-bottom: 15px; text-align: center; }
        .identity { margin-bottom: 12px; }
        .identity td { padding: 2px 0; vertical-align: top; }
        .identity .label { width: 21%; }
        .identity .separator { text-align: center; width: 3%; }
        .identity .value { font-weight: bold; width: 76%; }
        .date-section { margin-top: 13px; }
        .date-heading { background: #374151; color: #fff; font-size: 9.5pt; font-weight: bold; padding: 5px 7px; }
        .receipt-table { font-size: 8.8pt; margin-top: 0; table-layout: fixed; }
        .receipt-table thead { display: table-header-group; }
        .receipt-table tr { page-break-inside: avoid; }
        .receipt-table th, .receipt-table td { border: 1px solid #000; padding: 5px 4px; vertical-align: middle; }
        .receipt-table th { background: #e5e7eb; font-weight: bold; text-align: center; }
        .receipt-table .number { text-align: center; width: 6%; }
        .receipt-table .student { width: 35%; }
        .receipt-table .exam { width: 20%; }
        .receipt-table .role { width: 21%; }
        .receipt-table .amount { text-align: right; width: 18%; }
        .receipt-table .total-label { font-weight: bold; text-align: right; }
        .receipt-table .total-amount { font-weight: bold; text-align: right; }
        .grand-total { border: 2px solid #000; font-size: 10.5pt; font-weight: bold; margin-top: 15px; padding: 7px 8px; text-align: right; }
        .statement { margin: 15px 0 0; text-align: justify; }
        .signature { margin-top: 18px; }
        .signature td { vertical-align: top; }
        .signature-note { font-size: 8.8pt; padding-right: 20px; width: 54%; }
        .signature-receiver { text-align: center; width: 46%; }
        .signature-space { height: 66px; }
        .receiver-name { font-weight: bold; text-decoration: underline; }
        .page-note { color: #555; font-size: 7.5pt; margin-top: 12px; text-align: right; }
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
                                <th class="amount">Honor</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($laporanTanggal->items as $item)
                            <tr>
                                <td class="number">{{ $loop->iteration }}</td>
                                <td class="student"><strong>{{ $item->nama_mahasiswa }}</strong><br>{{ $item->nim }}</td>
                                <td class="exam">{{ $item->tipe_ujian }}</td>
                                <td class="role">{{ $item->peran }}</td>
                                <td class="amount">{{ helper::formatRupiah($item->honor) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="total-label">SUBTOTAL {{ helper::tgl_indo_lengkap($laporanTanggal->tanggal) }}</td>
                            <td class="total-amount">{{ helper::formatRupiah($laporanTanggal->subtotal_honor) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach

            <div class="grand-total">TOTAL HONORARIUM SELURUH TANGGAL: {{ helper::formatRupiah($report->total_honor) }}</div>

            <p class="statement">Dengan ini saya menyatakan telah menerima honorarium pelaksanaan ujian untuk seluruh tanggal yang dirinci di atas dengan jumlah sebesar <strong>{{ helper::formatRupiah($report->total_honor) }}</strong>.</p>

            <table class="signature">
                <tr>
                    <td class="signature-note">Dokumen ini menjadi bukti penerimaan honorarium dosen untuk seluruh tanggal ujian yang tercantum di atas.</td>
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
