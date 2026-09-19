<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Honorarium Harian</title>
    <style>
        @page { margin: 9mm 10mm 10mm; }
        * { box-sizing: border-box; }
        body { color: #111827; font-family: "Times New Roman", serif; font-size: 8.2pt; line-height: 1.12; margin: 0; }
        .document { width: 100%; }
        .page-break { font-size: 0; height: 0; line-height: 0; page-break-before: always; }
        .letterhead table, .summary, .metric-table, .report-table, .signature, .tax-signature { border-collapse: collapse; width: 100%; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 36px; width: auto; }
        .logo-fikom { height: 28px; margin-left: 7px; vertical-align: middle; width: auto; }
        .letterhead-title { color: #000; font-size: 10.8pt; font-weight: bold; line-height: 1.03; text-align: right; }
        .letterhead-divider { border-top: 3px double #000; margin-top: 3px; }
        .address { color: #000; font-size: 7.3pt; line-height: 1.1; padding-top: 3px; text-align: center; }
        .contact-line { padding-top: 1px; }
        .document-title { color: #111; font-size: 12.5pt; font-weight: bold; margin: 7px 0 1px; text-align: center; text-decoration: underline; }
        .document-subtitle { font-size: 9pt; margin-bottom: 5px; text-align: center; }
        .summary { margin-bottom: 4px; }
        .summary td { padding: 0.5px 0; vertical-align: top; }
        .summary .label { width: 13%; }
        .summary .separator { text-align: center; width: 2%; }
        .summary .value { font-weight: bold; width: 85%; }
        .metric-table { margin-bottom: 4px; table-layout: fixed; }
        .metric-table td { border: 1px solid #9ca3af; padding: 4px 5px; text-align: center; vertical-align: middle; }
        .metric-value { color: #111827; display: block; font-size: 11.5pt; font-weight: bold; }
        .metric-label { color: #4b5563; display: block; font-family: Arial, sans-serif; font-size: 6.6pt; margin-top: 1px; text-transform: uppercase; }
        .section { margin-top: 8px; page-break-inside: auto; }
        .section-title { background: #374151; color: #fff; font-family: Arial, sans-serif; font-size: 7.5pt; font-weight: bold; padding: 4px 5px; page-break-after: avoid; text-transform: uppercase; }
        .report-table { font-size: 7.25pt; line-height: 1.06; page-break-before: avoid; table-layout: fixed; }
        .report-table thead { display: table-header-group; }
        .report-table tr { page-break-inside: avoid; }
        .report-table th, .report-table td { border: 1px solid #6b7280; padding: 3.5px 3px; vertical-align: middle; }
        .report-table th { background: #e5e7eb; color: #111827; font-family: Arial, sans-serif; font-size: 6.9pt; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .right { text-align: right; }
        .number { width: 5%; }
        .exam-name { width: 16%; }
        .type-name { width: 34%; }
        .type-count { width: 12%; }
        .type-assignment { width: 15%; }
        .type-amount { width: 18%; }
        .lecturer-name { width: 31%; }
        .lecturer-student { width: 10%; }
        .lecturer-role { width: 28%; }
        .lecturer-amount { width: 16%; }
        .lecturer-signature { width: 10%; }
        .lecturer-signature img { display: block; margin: 0 auto; max-height: 34px; max-width: 64px; }
        .tax-table { font-size: 6.4pt; line-height: 1; }
        .tax-table th, .tax-table td { padding: 2px; }
        .tax-table .official-id { font-size: 5.6pt; }
        .tax-number { width: 4%; }
        .tax-lecturer { width: 24%; }
        .tax-role { width: 16%; }
        .tax-count { width: 7%; }
        .tax-honor { width: 12%; }
        .tax-amount { width: 10%; }
        .tax-adjustment { width: 13%; }
        .tax-received { width: 14%; }
        .tax-formula { color: #374151; font-family: Arial, sans-serif; font-size: 6.7pt; margin: 4px 0 0; text-align: right; }
        .adjustment-positive { color: #166534; }
        .adjustment-negative { color: #991b1b; }
        .lecturer-table-compact { font-size: 6.5pt; line-height: 1; }
        .lecturer-table-compact th, .lecturer-table-compact td { padding: 1.5px 2px; }
        .lecturer-table-compact .official-id { font-size: 5.6pt; }
        .lecturer-table-compact .lecturer-signature img { max-height: 22px; max-width: 56px; }
        .official-id { color: #4b5563; font-size: 6.5pt; }
        .total-row td { background: #f3f4f6; font-weight: bold; }
        .grand-total { border: 2px solid #111827; font-size: 9.5pt; font-weight: bold; margin-top: 7px; padding: 4px 6px; text-align: right; }
        .statement { margin: 6px 0 0; page-break-inside: avoid; text-align: justify; }
        .signature { margin-top: 6px; page-break-inside: avoid; }
        .signature td { vertical-align: top; }
        .signature-spacer { width: 58%; }
        .signature-official { color: #000; text-align: center; width: 42%; }
        .signature-heading { line-height: 1.15; }
        .signature-qr-box { height: 57px; text-align: center; }
        .signature-qr-box a { display: block; height: 54px; margin: 1px auto; width: 54px; }
        .verification-qr { display: block; height: 54px; margin: 0; width: 54px; }
        .signature-identity { line-height: 1.15; min-height: 23px; }
        .official-name { font-weight: bold; text-decoration: underline; }
        .tax-signature { margin-top: 8px; page-break-inside: avoid; }
        .tax-signature td { color: #000; text-align: center; vertical-align: top; width: 50%; }
        .tax-signature td:first-child { padding-right: 20px; }
        .tax-signature td:last-child { padding-left: 20px; }
        .tax-signature-space { height: 44px; }
        .document-continuation { padding-top: 8mm; }
        .tax-document-needs-initial { min-height: 258mm; padding-bottom: 17mm; position: relative; }
        .tax-signature-bottom { margin-top: 12mm; }
        .main-page-initial, .tax-page-initial {
            border: 1px solid #374151;
            color: #374151;
            font-family: Arial, sans-serif;
            font-size: 6.2pt;
            height: 13mm;
            padding-top: 2mm;
            text-align: center;
            width: 25mm;
        }
        .main-page-initial { margin: 6px 0 0 auto; }
        .tax-page-initial { bottom: 0; position: absolute; right: 0; }
    </style>
</head>
<body>
    @foreach ($reports as $report)
        @php
            $reportIndex = $loop->index;
            $lecturerRows = $report->lecturers->values();
            $lecturerChunks = collect();
            $lecturerSinglePageLimit = 21;
            $lecturerPageSize = 27;

            if ($lecturerRows->isEmpty() || $lecturerRows->count() <= $lecturerSinglePageLimit) {
                $lecturerChunks->push((object) ['offset' => 0, 'items' => $lecturerRows]);
            } else {
                $lecturerOffset = 0;
                $lecturerRemaining = $lecturerRows->count();
                while ($lecturerRemaining > $lecturerSinglePageLimit) {
                    $lecturerTake = min($lecturerPageSize, $lecturerRemaining - 2);
                    $lecturerChunks->push((object) [
                        'offset' => $lecturerOffset,
                        'items' => $lecturerRows->slice($lecturerOffset, $lecturerTake)->values(),
                    ]);
                    $lecturerOffset += $lecturerTake;
                    $lecturerRemaining -= $lecturerTake;
                    if ($lecturerRemaining <= $lecturerPageSize) {
                        break;
                    }
                }
                $lecturerChunks->push((object) [
                    'offset' => $lecturerOffset,
                    'items' => $lecturerRows->slice($lecturerOffset)->values(),
                ]);
            }
        @endphp

        @foreach ($lecturerChunks as $lecturerPage)
            @php
                $lecturerChunkIndex = $loop->index;
            @endphp
            @if ($reportIndex > 0 || !$loop->first)
                <div class="page-break"></div>
            @endif
            <div class="document honorarium-document{{ $loop->first ? '' : ' document-continuation' }}">
                @if ($loop->first)
                    @include('tugasakhir.keuanganfakultas._honorarium_pdf_letterhead')
                @endif

                <div class="document-title">REKAP HONORARIUM HARIAN{{ $lecturerChunkIndex > 0 ? ' - LANJUTAN' : '' }}</div>
                <div class="document-subtitle">Pelaksanaan Ujian Proposal dan Tugas Akhir</div>

                <table class="summary">
                    <tr><td class="label">Tanggal Ujian</td><td class="separator">:</td><td class="value">{{ helper::tgl_indo_lengkap($report->tanggal) }}</td></tr>
                    <tr><td class="label">Unit Kerja</td><td class="separator">:</td><td class="value">Fakultas Ilmu Komputer, Universitas Muslim Indonesia</td></tr>
                </table>

                @if ($loop->first)
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
                                    <th class="exam-name">Jenis Ujian</th>
                                    <th class="type-name">Tipe Ujian</th>
                                    <th class="type-count">Mahasiswa</th>
                                    <th class="type-assignment">Penugasan Dosen</th>
                                    <th class="type-amount">Honorarium</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report->exam_types as $type)
                                    <tr>
                                        <td class="center">{{ $loop->iteration }}</td>
                                        <td>{{ $type->exam_name }}</td>
                                        <td>{{ $type->type_name }}</td>
                                        <td class="center">{{ number_format($type->student_count) }}</td>
                                        <td class="center">{{ number_format($type->assignment_count) }}</td>
                                        <td class="right">{{ helper::formatRupiah($type->total_honor) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="3" class="right">TOTAL</td>
                                    <td class="center">{{ number_format($report->student_count) }}</td>
                                    <td class="center">{{ number_format($report->assignment_count) }}</td>
                                    <td class="right">{{ helper::formatRupiah($report->total_honor) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="section">
                    <div class="section-title">Rekap Penerimaan Dosen{{ $lecturerChunkIndex > 0 ? ' (Lanjutan)' : '' }}</div>
                    @php
                        $compactLecturerTable = $report->lecturer_count > 18;
                    @endphp
                    <table class="report-table lecturer-table{{ $compactLecturerTable ? ' lecturer-table-compact' : '' }}">
                        <thead>
                            <tr>
                                <th class="number">No.</th>
                                <th class="lecturer-name">Dosen / NIDN atau Kode Dosen</th>
                                <th class="lecturer-student">Mahasiswa</th>
                                <th class="lecturer-role">Peran</th>
                                <th class="lecturer-amount">Honorarium Diterima</th>
                                <th class="lecturer-signature">Paraf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lecturerPage->items as $lecturer)
                                <tr>
                                    <td class="center">{{ $lecturerPage->offset + $loop->iteration }}</td>
                                    <td><strong>{{ $lecturer->name }}</strong> <span class="official-id">({{ $lecturer->code }})</span></td>
                                    <td class="center">{{ number_format($lecturer->student_count) }}</td>
                                    <td>{{ $lecturer->roles }}</td>
                                    <td class="right">{{ helper::formatRupiah($lecturer->total_honor) }}</td>
                                    <td class="center lecturer-signature">
                                        @if ($lecturer->signature_data_uri)
                                            <img src="{{ $lecturer->signature_data_uri }}" alt="Paraf {{ $lecturer->name }}">
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="center">Tidak ada penerima honorarium pada tanggal ini.</td>
                                </tr>
                            @endforelse
                            @if ($loop->last)
                                <tr class="total-row">
                                    <td colspan="4" class="right">TOTAL HONORARIUM {{ strtoupper(helper::tgl_indo_lengkap($report->tanggal)) }}</td>
                                    <td class="right">{{ helper::formatRupiah($report->total_honor) }}</td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($loop->last)
                    <div class="grand-total">TOTAL HONORARIUM HARIAN: {{ helper::formatRupiah($report->total_honor) }}</div>
                    <p class="statement">Rekap ini merangkum honorarium pelaksanaan ujian pada tanggal {{ helper::tgl_indo_lengkap($report->tanggal) }} berdasarkan tipe ujian, penugasan dosen, dan penyesuaian kehadiran pembimbing yang tercatat di Thesis App FIKOM UMI.</p>

                    <table class="signature">
                        <tr>
                            <td class="signature-spacer"></td>
                            <td class="signature-official">
                                <div class="signature-heading">Makassar, {{ helper::tgl_indo_lengkap($generatedAt->format('Y-m-d')) }}<br>Wakil Dekan II Bidang Keuangan dan SDM,</div>
                                <div class="signature-qr-box"><a href="{{ $report->verification_url }}"><img class="verification-qr" src="{{ \App\Helper::qrCodeDataUri($report->verification_url, 120) }}" alt="QR verifikasi rekap honorarium"></a></div>
                                <div class="signature-identity"><span class="official-name">{{ $wakilDekanDua->nama }}</span><br>{{ $wakilDekanDua->nip_nidn ? 'NIP/NIDN: ' . $wakilDekanDua->nip_nidn : '' }}</div>
                            </td>
                        </tr>
                    </table>
                @elseif ($lecturerChunks->count() > 1)
                    <div class="main-page-initial">Paraf WD II</div>
                @endif
            </div>
        @endforeach

        @php
            $taxRows = $report->tax_lecturers->values();
            $taxChunks = collect();
            if ($taxRows->isEmpty()) {
                $taxChunks->push((object) ['offset' => 0, 'items' => collect()]);
            } else {
                $taxOffset = 0;
                while ($taxOffset < $taxRows->count()) {
                    $taxChunks->push((object) [
                        'offset' => $taxOffset,
                        'items' => $taxRows->slice($taxOffset, 20)->values(),
                    ]);
                    $taxOffset += 20;
                }
            }
        @endphp
        @foreach ($taxChunks as $taxPage)
            @php($taxChunkIndex = $loop->index)
            @php($taxChunk = $taxPage->items)
            <div class="page-break"></div>
            <div class="document tax-document{{ $loop->first ? '' : ' document-continuation' }}{{ $taxChunks->count() > 1 && !$loop->last ? ' tax-document-needs-initial' : '' }}">
                @if ($loop->first)
                    @include('tugasakhir.keuanganfakultas._honorarium_pdf_letterhead')
                @endif

                <div class="document-title">REKAP PAJAK HONORARIUM{{ $taxChunkIndex > 0 ? ' - LANJUTAN' : '' }}</div>
                <div class="document-subtitle">Pembimbing Utama dan Pembimbing Pendamping</div>

                <table class="summary">
                    <tr><td class="label">Tanggal Ujian</td><td class="separator">:</td><td class="value">{{ helper::tgl_indo_lengkap($report->tanggal) }}</td></tr>
                    <tr><td class="label">Unit Kerja</td><td class="separator">:</td><td class="value">Fakultas Ilmu Komputer, Universitas Muslim Indonesia</td></tr>
                </table>

                <div class="section">
                    <table class="report-table tax-table">
                        <thead>
                            <tr>
                                <th class="tax-number">No.</th>
                                <th class="tax-lecturer">Dosen / NIDN</th>
                                <th class="tax-role">Peran</th>
                                <th class="tax-count">Jumlah</th>
                                <th class="tax-honor">Honor</th>
                                <th class="tax-amount">Pajak (5%)</th>
                                <th class="tax-adjustment">Penyesuaian</th>
                                <th class="tax-received">Honor Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($taxChunk as $item)
                                <tr>
                                    <td class="center">{{ $taxPage->offset + $loop->iteration }}</td>
                                    <td><strong>{{ $item->lecturer_name }}</strong><br><span class="official-id">{{ $item->lecturer_code }}</span></td>
                                    <td>{{ $item->role }}</td>
                                    <td class="center">{{ number_format($item->assignment_count) }}</td>
                                    <td class="right">{{ helper::formatRupiah($item->honor) }}</td>
                                    <td class="right">{{ helper::formatRupiah($item->tax) }}</td>
                                    <td class="right {{ $item->adjustment > 0 ? 'adjustment-positive' : ($item->adjustment < 0 ? 'adjustment-negative' : '') }}">
                                        @if ($item->adjustment > 0)+@elseif ($item->adjustment < 0)-@endif{{ helper::formatRupiah(abs($item->adjustment)) }}
                                    </td>
                                    <td class="right">{{ helper::formatRupiah($item->received) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="center">Tidak ada honorarium Pembimbing Utama atau Pembimbing Pendamping pada tanggal ini.</td>
                                </tr>
                            @endforelse
                            @if ($loop->last)
                                <tr class="total-row">
                                    <td colspan="3" class="right">TOTAL</td>
                                    <td class="center">{{ number_format($report->tax_assignment_count) }}</td>
                                    <td class="right">{{ helper::formatRupiah($report->tax_total_honor) }}</td>
                                    <td class="right">{{ helper::formatRupiah($report->tax_total_amount) }}</td>
                                    <td class="right">
                                        @if ($report->tax_total_adjustment > 0)+@elseif ($report->tax_total_adjustment < 0)-@endif{{ helper::formatRupiah(abs($report->tax_total_adjustment)) }}
                                    </td>
                                    <td class="right">{{ helper::formatRupiah($report->tax_total_received) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if ($loop->last)
                    <p class="tax-formula">Honor Diterima = Honor - Pajak + Penyesuaian</p>
                    <table class="tax-signature{{ $taxChunks->count() > 1 ? ' tax-signature-bottom' : '' }}">
                        <tr>
                            <td>
                                Makassar, {{ helper::tgl_indo_lengkap($generatedAt->format('Y-m-d')) }}<br>Dekan,
                                <div class="tax-signature-space"></div>
                                <span class="official-name">{{ $dekan->nama }}</span><br>
                                {{ $dekan->nip_nidn ? 'NIP/NIDN: ' . $dekan->nip_nidn : '' }}
                            </td>
                            <td>
                                Makassar, {{ helper::tgl_indo_lengkap($generatedAt->format('Y-m-d')) }}<br>Wakil Dekan II Bidang Keuangan dan SDM,
                                <div class="tax-signature-space"></div>
                                <span class="official-name">{{ $wakilDekanDua->nama }}</span><br>
                                {{ $wakilDekanDua->nip_nidn ? 'NIP/NIDN: ' . $wakilDekanDua->nip_nidn : '' }}
                            </td>
                        </tr>
                    </table>
                @elseif ($taxChunks->count() > 1)
                    <div class="tax-page-initial">Paraf WD II</div>
                @endif
            </div>
        @endforeach
    @endforeach
</body>
</html>
