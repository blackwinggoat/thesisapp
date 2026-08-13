<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK Ujian Proposal</title>
    <style>
        @page { margin: 13mm 14mm 12mm; }
        * { box-sizing: border-box; }
        body { color: #000; font-family: "Times New Roman", serif; font-size: 10.5pt; line-height: 1.17; margin: 0; }
        .document { margin: 0 auto; width: 555px; }
        .letterhead { padding: 2px 0 0; }
        .letterhead table, .details, .signature { border-collapse: collapse; width: 100%; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 50px; width: auto; }
        .logo-fikom { height: 40px; width: auto; }
        .letterhead-title { font-size: 11.5pt; font-weight: bold; line-height: 1.05; text-align: right; }
        .letterhead-divider { border-top: 3px double #000; margin-top: 7px; }
        .address { font-size: 7.3pt; line-height: 1.12; padding-top: 7px; text-align: center; }
        .invocation { font-size: 12pt; font-style: italic; font-weight: bold; margin: 18px 0 13px; text-align: center; }
        .document-title { font-size: 12pt; font-weight: bold; margin: 0 0 2px; text-align: center; text-decoration: underline; }
        .document-number { font-size: 11pt; font-weight: bold; margin-bottom: 27px; text-align: center; }
        p { margin: 0 0 13px; text-align: justify; }
        .centered { text-align: center; }
        .details { margin: 0 0 17px; }
        .details td { padding: 2px 0; vertical-align: top; }
        .details .label { width: 29%; }
        .details .separator { text-align: center; width: 3%; }
        .details .value { padding-left: 4px; width: 68%; }
        .examiner { padding-left: 4px; }
        .panel-gap td { padding-bottom: 14px; }
        .student-table { border-collapse: collapse; margin: 0 0 15px; width: 100%; }
        .student-table th, .student-table td { border: 1px solid #000; padding: 4px; }
        .student-table th { text-align: center; }
        .signature { margin-top: 29px; }
        .signature-left { font-size: 10.5pt; padding-top: 104px; width: 50%; }
        .signature-right { text-align: center; width: 50%; }
        .signature-space { height: 94px; position: relative; }
        .stamp { bottom: 0; height: 88px; position: absolute; right: 83px; width: auto; }
        .sign { bottom: 13px; height: 64px; position: absolute; right: 5px; width: auto; }
        .official-name { font-weight: bold; text-decoration: underline; }
        .tembusan { font-size: 10.5pt; font-style: italic; line-height: 1.32; margin-top: 6px; }
    </style>
</head>
<body>
    @php
        $namaProdi = helper::getProgramStudiByStambuk($surat->C_NPM);
        $kaprodi = helper::getKaprodiByNimAndTanggal($surat->C_NPM, $surat->penguji_created_at ?: $surat->tgl_ujian);
        $stempelKaprodi = $namaProdi === 'Teknik Informatika' ? 'stempelprodi.png' : 'stempelprodi_si.png';
        $mahasiswa = \App\Model\t_mst_mahasiswa::where('C_NPM', $surat->C_NPM)->first();
        $namaMahasiswa = optional($mahasiswa)->NAMA_MAHASISWA ?: '-';
        $judulTugasAkhir = helper::judulDenganKodeJenisTugasAkhir($surat->jenis_tugas_akhir_id, $surat->judul) ?: '-';
        try {
            $tanggalUjian = Illuminate\Support\Carbon::parse($surat->tgl_ujian ?: date('Y-m-d'));
            $tanggalSurat = Illuminate\Support\Carbon::parse($surat->penguji_created_at ?: $surat->tgl_ujian ?: date('Y-m-d'));
        } catch (\Exception $exception) {
            $tanggalUjian = Illuminate\Support\Carbon::today();
            $tanggalSurat = Illuminate\Support\Carbon::today();
        }
        $jamUjian = trim((string) $surat->jam_ujian);
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
        <div class="document-title">SURAT PENUNJUKAN</div>
        <div class="document-number">Nomor : {{ $surat->nomor_sk }}</div>

        <p>Sesuai Peraturan Akademik pada Program Studi {{ $namaProdi }} Fakultas Ilmu Komputer Universitas Muslim Indonesia, dengan ini menetapkan tim penguji pada ujian proposal.</p>
        <p class="centered"><strong>KETUA PROGRAM STUDI {{ strtoupper($namaProdi) }}</strong></p>
        <p>Menetapkan Tim Penguji Ujian Proposal sebagai berikut:</p>
        <table class="details">
            <tr><td class="label">Pembimbing Utama</td><td class="separator">:</td><td class="value">{{ helper::getNamaDosenByKode($surat->pembimbing_I_id) }}</td></tr>
            <tr class="panel-gap"><td class="label">Pembimbing<br>Pendamping</td><td class="separator">:</td><td class="value">{{ helper::getNamaDosenByKode($surat->pembimbing_II_id) }}</td></tr>
            <tr class="panel-gap"><td class="label">Ketua Sidang</td><td class="separator">:</td><td class="value">{{ helper::getNamaDosenByKode($surat->ketua_sidang_id) }}</td></tr>
            <tr><td class="label">Penguji</td><td class="separator">:</td><td class="value examiner">1. {{ helper::getNamaDosenByKode($surat->penguji_I_id) }}</td></tr>
            <tr><td class="label"></td><td class="separator"></td><td class="value examiner">2. {{ helper::getNamaDosenByKode($surat->penguji_II_id) }}</td></tr>
            <tr><td class="label"></td><td class="separator"></td><td class="value examiner">3. {{ helper::getNamaDosenByKode($surat->penguji_III_id) }}</td></tr>
        </table>
        <p>Bertugas melaksanakan ujian proposal bagi mahasiswa:</p>
        <table class="student-table">
            <tr><th width="8%">No</th><th>Nama</th><th width="24%">Stambuk</th></tr>
            <tr><td style="text-align:center">1</td><td>{{ $namaMahasiswa }}</td><td style="text-align:center">{{ $surat->C_NPM }}</td></tr>
        </table>
        <table class="details">
            <tr><td class="label">Judul</td><td class="separator">:</td><td class="value"><strong>{{ $judulTugasAkhir }}</strong></td></tr>
            <tr><td class="label">Hari / Tanggal</td><td class="separator">:</td><td class="value">{{ helper::getHari($tanggalUjian->format('Y-m-d')) }}, {{ $tanggalUjian->format('d') }} {{ helper::getBulan((int) $tanggalUjian->format('m')) }} {{ $tanggalUjian->format('Y') }}</td></tr>
            <tr><td class="label">Waktu</td><td class="separator">:</td><td class="value">{{ $waktuUjian }}</td></tr>
            <tr><td class="label">Tempat</td><td class="separator">:</td><td class="value">{{ $surat->nama_ruangan ?: '-' }}</td></tr>
        </table>
        <p>Demikian surat penunjukan ini diberikan untuk dilaksanakan dengan penuh tanggung jawab dan amanah.</p>
        <p>Waalahu Waliyyut Taufiq wal-Hidayah.</p>

        <table class="signature">
            <tr>
                <td class="signature-left"><span class="tembusan"><u>Tembusan:</u><br>1. Dekan Fakultas Ilmu Komputer<br>2. Arsip</span></td>
                <td class="signature-right">Makassar, {{ helper::tgl_indo_lengkap($tanggalSurat->format('Y-m-d')) }}<br>Ketua Program Studi
                    <div class="signature-space">
                        <img class="stamp" src="{{ \App\Helper::pdfOfficialImageDataUri($stempelKaprodi) }}" alt="">
                        @if (!empty($kaprodi->ttd))
                            <img class="sign" src="{{ \App\Helper::pdfOfficialImageDataUri($kaprodi->ttd) }}" alt="">
                        @endif
                    </div>
                    <span class="official-name">{{ $kaprodi->nama ?: '-' }}</span><br>NIDN : {{ $kaprodi->nidn ?: '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
