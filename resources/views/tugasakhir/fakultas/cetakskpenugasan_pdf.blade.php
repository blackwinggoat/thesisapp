<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK Penugasan Ujian Tugas Akhir</title>
    <style>
        @page { margin: 13mm 14mm 12mm; }
        * { box-sizing: border-box; }
        body { color: #000; font-family: "Times New Roman", serif; font-size: 10.5pt; line-height: 1.17; margin: 0; }
        .document { margin: 0 auto; width: 555px; }
        .letterhead { padding: 2px 0 0; }
        .letterhead table { border-collapse: collapse; width: 100%; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 46px; width: auto; }
        .logo-fikom { height: 36px; width: auto; }
        .letterhead-title { font-size: 9.5pt; font-weight: bold; line-height: 1.1; padding-left: 8px; text-align: left; }
        .letterhead-divider { border-top: 3px double #000; margin-top: 7px; }
        .address { font-size: 7.3pt; line-height: 1.12; margin-top: 8px; text-align: center; }
        .invocation { font-size: 12pt; font-style: italic; font-weight: bold; margin: 18px 0 13px; text-align: center; }
        .document-title { font-size: 12pt; font-weight: bold; margin: 0 0 2px; text-align: center; text-decoration: underline; }
        .document-number { font-size: 11pt; font-weight: bold; margin-bottom: 30px; text-align: center; }
        p { margin: 0 0 14px; text-align: justify; }
        .opening { line-height: 1.2; }
        .section-label { margin: 28px 0 16px; }
        .details { border-collapse: collapse; margin: 0 0 20px; width: 100%; }
        .details td { padding: 2px 0; vertical-align: top; }
        .details .label { width: 29%; }
        .details .separator { text-align: center; width: 3%; }
        .details .value { padding-left: 4px; width: 68%; }
        .examiner { padding-left: 4px; }
        .panel-gap td { padding-bottom: 22px; }
        .signature { margin-top: 45px; width: 100%; }
        .signature td { vertical-align: top; }
        .signature-left { font-size: 10.5pt; padding-top: 104px; width: 50%; }
        .signature-right { text-align: center; width: 50%; }
        .signature-space { height: 94px; position: relative; }
        .stamp { bottom: 0; height: 88px; position: absolute; right: 83px; width: auto; }
        .sign { bottom: 13px; height: 64px; position: absolute; right: 5px; width: auto; }
        .dekan-name { font-weight: bold; text-decoration: underline; }
        .tembusan { font-size: 10.5pt; font-style: italic; line-height: 1.32; margin-top: 6px; }
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

    <div class="document">
    <div class="letterhead">
        <table>
            <tr>
                <td width="51%"><img class="logo-umi" src="{{ \App\Helper::publicImageDataUri('images/branding/umi-pdf.jpg') }}" alt="Logo UMI"><img class="logo-fikom" src="{{ \App\Helper::publicImageDataUri('images/branding/fikom-pdf.jpg') }}" alt="Logo FIKOM" style="margin-left:12px; vertical-align:middle"></td>
                <td class="letterhead-title" width="49%">YAYASAN WAKAF UMI<br>UNIVERSITAS MUSLIM INDONESIA<br>FAKULTAS ILMU KOMPUTER</td>
            </tr>
        </table>
        <div class="letterhead-divider"></div>
        <div class="address">Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818, Fax (0411) - 453009 Makassar 90231<br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id</div>
    </div>

    <div class="invocation">Bismillahir Rahmanir Rahiim</div>
    <div class="document-title">SURAT PENUGASAN</div>
    <div class="document-number">Nomor : {{ $data_sk[0]->nomor_sk }}</div>

    <p class="opening">Dengan rahmat Allah SWT, sesuai peraturan Akademik Universitas Muslim Indonesia dan Surat Ketua Program Studi {{ helper::getProgramStudiByStambuk($data_sk[0]->C_NPM) }} nomor : {{ helper::getNomorSKPenugasanWithBimbinganId($data_sk[0]->pendaftaran_id) }}, tertanggal {{ helper::tgl_indo_lengkap($tanggalUjian->format('Y-m-d')) }}, maka dengan ini menetapkan Panitia Ujian Tugas Akhir sebagai berikut</p>

    <table class="details">
        <tr><td class="label">Pembimbing Utama</td><td class="separator">:</td><td class="value">{{ helper::getNamaDosenByKode($data_sk[0]->pembimbing_I_id) }}</td></tr>
        <tr class="panel-gap"><td class="label">Pembimbing<br>Pendamping</td><td class="separator">:</td><td class="value">{{ helper::getNamaDosenByKode($data_sk[0]->pembimbing_II_id) }}</td></tr>
        <tr class="panel-gap"><td class="label">Ketua Sidang</td><td class="separator">:</td><td class="value">{{ helper::getNamaDosenByKode($data_sk[0]->ketua_sidang_id) }}</td></tr>
        <tr><td class="label">Penguji</td><td class="separator">:</td><td class="value examiner">1. {{ helper::getNamaDosenByKode($data_sk[0]->penguji_I_id) }}</td></tr>
        <tr><td class="label"></td><td class="separator"></td><td class="value examiner">2. {{ helper::getNamaDosenByKode($data_sk[0]->penguji_II_id) }}</td></tr>
        <tr class="panel-gap"><td class="label"></td><td class="separator"></td><td class="value examiner">3. {{ helper::getNamaDosenByKode($data_sk[0]->penguji_III_id) }}</td></tr>
    </table>

    <p class="section-label">Untuk melaksanakan Ujian Tugas Akhir bagi mahasiswa :</p>
    <table class="details">
        <tr><td class="label">Nama / Stambuk</td><td class="separator">:</td><td class="value">{{ $namaMahasiswa }} / {{ $data_sk[0]->C_NPM }}</td></tr>
        <tr><td class="label">Judul Tugas Akhir</td><td class="separator">:</td><td class="value">{{ $judulTugasAkhir }}</td></tr>
        <tr><td class="label">Hari/Tanggal</td><td class="separator">:</td><td class="value">{{ helper::getHari($tanggalUjian->format('Y-m-d')) }}, {{ $tanggalUjian->format('d') }} {{ helper::getBulan((int) $tanggalUjian->format('m')) }} {{ $tanggalUjian->format('Y') }}</td></tr>
        <tr><td class="label">Waktu</td><td class="separator">:</td><td class="value">{{ $waktuUjian }}</td></tr>
        <tr><td class="label">Tempat</td><td class="separator">:</td><td class="value">{{ $data_sk[0]->nama_ruangan ?: '-' }}</td></tr>
    </table>

    <p>Demikian surat penugasan ini disampaikan, atas perhatian dan kehadiran Bapak diucapkan terima kasih</p>
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
    </div>
</body>
</html>
