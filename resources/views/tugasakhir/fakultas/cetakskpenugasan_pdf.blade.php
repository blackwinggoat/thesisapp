<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK Penugasan Ujian Tugas Akhir</title>
    <style>
        @page { margin: 12mm 14mm 11mm; }
        * { box-sizing: border-box; }
        body { color: #111; font-family: "Times New Roman", serif; font-size: 10pt; line-height: 1.2; margin: 0; }
        .letterhead { border-bottom: 3px double #111; padding-bottom: 5px; }
        .letterhead table { border-collapse: collapse; width: 100%; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 52px; width: auto; }
        .logo-fikom { height: 37px; width: auto; }
        .letterhead-title { font-family: Arial, sans-serif; font-size: 10pt; font-weight: bold; line-height: 1.35; text-align: center; }
        .address { font-family: Arial, sans-serif; font-size: 7.5pt; line-height: 1.25; margin-top: 4px; text-align: center; }
        .document-title { font-size: 12pt; font-weight: bold; margin: 15px 0 2px; text-align: center; text-decoration: underline; }
        .document-number { font-size: 10pt; margin-bottom: 13px; text-align: center; }
        p { margin: 0 0 8px; text-align: justify; }
        .section-label { font-weight: bold; margin: 9px 0 4px; }
        .details { border-collapse: collapse; margin: 0 0 8px; width: 100%; }
        .details td { padding: 1.5px 0; vertical-align: top; }
        .details .label { width: 31%; }
        .details .separator { text-align: center; width: 3%; }
        .details .value { width: 66%; }
        .examiner { padding-left: 4px; }
        .signature { margin-top: 14px; width: 100%; }
        .signature td { vertical-align: top; }
        .signature-left { font-size: 9pt; padding-top: 57px; width: 50%; }
        .signature-right { text-align: center; width: 50%; }
        .signature-space { height: 65px; position: relative; }
        .stamp { bottom: 0; height: 62px; position: absolute; right: 58px; width: auto; }
        .sign { bottom: 8px; height: 45px; position: absolute; right: 13px; width: auto; }
        .dekan-name { font-weight: bold; text-decoration: underline; }
        .tembusan { font-size: 8.7pt; line-height: 1.3; margin-top: 6px; }
    </style>
</head>
<body>
    @php
        $tanggalAcuanDekan = $data_sk[0]->created_at ?? null;
        $dekan = helper::getDekanByTanggal($tanggalAcuanDekan);
        $mahasiswa = \App\Model\t_mst_mahasiswa::where('C_NPM', $data_sk[0]->C_NPM)->first();
        $namaMahasiswa = optional($mahasiswa)->NAMA_MAHASISWA ?: '-';
        $judulTugasAkhir = helper::getJudulTugasAkhirByNim($data_sk[0]->C_NPM) ?: '-';
        $statusSk = helper::getStatusFromSkPenugasan($data_sk[0]->sk_penugasan_id);
        try {
            $tanggalUjian = Illuminate\Support\Carbon::parse($data_sk[0]->tgl_ujian ?: date('Y-m-d'));
        } catch (\Exception $exception) {
            $tanggalUjian = Illuminate\Support\Carbon::today();
        }
        try {
            $tanggalSurat = $data_sk[0]->created_at
                ? Illuminate\Support\Carbon::parse(substr($data_sk[0]->created_at, 0, 10))
                : Illuminate\Support\Carbon::today();
        } catch (\Exception $exception) {
            $tanggalSurat = Illuminate\Support\Carbon::today();
        }
        $jamUjian = trim((string) $data_sk[0]->jam_ujian);
        $waktuUjian = strlen($jamUjian) === 5
            ? $jamUjian . ' - ' . sprintf('%02d', substr($jamUjian, 0, 2) + 2) . ':30'
            : ($jamUjian ?: '-');
    @endphp

    <div class="letterhead">
        <table>
            <tr>
                <td width="16%"><img class="logo-umi" src="{{ \App\Helper::publicImageDataUri('images/branding/umi-pdf.jpg') }}" alt="Logo UMI"></td>
                <td class="letterhead-title" width="52%">YAYASAN WAKAF UMI<br>UNIVERSITAS MUSLIM INDONESIA<br>FAKULTAS ILMU KOMPUTER</td>
                <td width="32%" style="text-align:right"><img class="logo-fikom" src="{{ \App\Helper::publicImageDataUri('images/branding/fikom-pdf.jpg') }}" alt="Logo FIKOM"></td>
            </tr>
        </table>
        <div class="address">Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI, Makassar 90231<br>fikom.umi.ac.id</div>
    </div>

    <div class="document-title">SURAT PENUGASAN</div>
    <div class="document-number">Nomor: {{ $data_sk[0]->nomor_sk }}</div>

    <p>Dengan rahmat Allah SWT, sesuai Peraturan Akademik Universitas Muslim Indonesia dan Surat Ketua Program Studi {{ helper::getProgramStudiByStambuk($data_sk[0]->C_NPM) }} nomor {{ helper::getNomorSKPenugasanWithBimbinganId($data_sk[0]->pendaftaran_id) }}, maka dengan ini menetapkan Panitia Ujian Tugas Akhir sebagai berikut.</p>

    <table class="details">
        <tr><td class="label">Pembimbing Utama</td><td class="separator">:</td><td class="value">{{ helper::getNamaDosenByKode($data_sk[0]->pembimbing_I_id) }}</td></tr>
        <tr><td class="label">Pembimbing Pendamping</td><td class="separator">:</td><td class="value">{{ helper::getNamaDosenByKode($data_sk[0]->pembimbing_II_id) }}</td></tr>
        <tr><td class="label">Ketua Sidang</td><td class="separator">:</td><td class="value">{{ helper::getNamaDosenByKode($data_sk[0]->ketua_sidang_id) }}</td></tr>
        <tr><td class="label">Penguji I</td><td class="separator">:</td><td class="value examiner">{{ helper::getNamaDosenByKode($data_sk[0]->penguji_I_id) }}</td></tr>
        <tr><td class="label">Penguji II</td><td class="separator">:</td><td class="value examiner">{{ helper::getNamaDosenByKode($data_sk[0]->penguji_II_id) }}</td></tr>
        <tr><td class="label">Penguji III</td><td class="separator">:</td><td class="value examiner">{{ helper::getNamaDosenByKode($data_sk[0]->penguji_III_id) }}</td></tr>
    </table>

    <p class="section-label">Untuk melaksanakan Ujian Tugas Akhir bagi mahasiswa:</p>
    <table class="details">
        <tr><td class="label">Nama / Stambuk</td><td class="separator">:</td><td class="value">{{ $namaMahasiswa }} / {{ $data_sk[0]->C_NPM }}</td></tr>
        <tr><td class="label">Judul Tugas Akhir</td><td class="separator">:</td><td class="value">{{ $judulTugasAkhir }}</td></tr>
        <tr><td class="label">Hari / Tanggal</td><td class="separator">:</td><td class="value">{{ helper::getHari($tanggalUjian->format('Y-m-d')) }}, {{ $tanggalUjian->format('d') }} {{ helper::getBulan((int) $tanggalUjian->format('m')) }} {{ $tanggalUjian->format('Y') }}</td></tr>
        <tr><td class="label">Waktu</td><td class="separator">:</td><td class="value">{{ $waktuUjian }}</td></tr>
        <tr><td class="label">Tempat</td><td class="separator">:</td><td class="value">{{ $data_sk[0]->nama_ruangan ?: '-' }}</td></tr>
    </table>

    <p>Demikian surat penugasan ini disampaikan. Atas perhatian dan kehadiran Bapak/Ibu diucapkan terima kasih.</p>
    <p>Waalahu Waliyyut Taufiq wal-Hidayah.</p>

    <table class="signature">
        <tr>
            <td class="signature-left"><span class="tembusan"><u>Tembusan:</u><br>1. Yayasan Wakaf UMI<br>2. Rektor UMI<br>3. Ketua Program Studi FIKOM UMI</span></td>
            <td class="signature-right">Makassar, {{ helper::tgl_indo_lengkap($tanggalSurat->format('Y-m-d')) }}<br>Dekan
                <div class="signature-space">
                    @if ($statusSk == 2)
                        <img class="stamp" src="{{ \App\Helper::pdfOfficialImageDataUri('stempelfakultas.png') }}" alt="">
                        @if (!empty($dekan->ttd))
                            <img class="sign" src="{{ \App\Helper::pdfOfficialImageDataUri($dekan->ttd) }}" alt="">
                        @endif
                    @endif
                </div>
                <span class="dekan-name">{{ $dekan->nama ?: '-' }}</span>
            </td>
        </tr>
    </table>
</body>
</html>
