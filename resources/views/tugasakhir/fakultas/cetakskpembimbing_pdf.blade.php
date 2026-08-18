<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK Pembimbing</title>
    <style>
        @page { margin: 13mm 14mm 12mm; }
        * { box-sizing: border-box; }
        body { color: #000; font-family: "Times New Roman", serif; font-size: 10.5pt; line-height: 1.17; margin: 0; }
        .document { margin: 0 auto; page-break-after: always; width: 555px; }
        .document:last-child { page-break-after: auto; }
        .letterhead { padding: 2px 0 0; }
        .letterhead table, .details, .signature { border-collapse: collapse; width: 100%; }
        .letterhead td, .details td, .signature td { vertical-align: top; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 50px; width: auto; }
        .logo-fikom { height: 40px; width: auto; }
        .letterhead-title { font-size: 11.5pt; font-weight: bold; line-height: 1.05; text-align: right; }
        .letterhead-divider { border-top: 3px double #000; margin-top: 7px; }
        .address { font-size: 7.3pt; line-height: 1.12; padding-top: 7px; text-align: center; }
        .invocation { font-size: 12pt; font-style: italic; font-weight: bold; margin: 18px 0 13px; text-align: center; }
        .document-title { font-size: 12pt; font-weight: bold; margin: 0 0 2px; text-align: center; text-decoration: underline; }
        .document-number { font-size: 11pt; font-weight: bold; margin-bottom: 28px; text-align: center; }
        p { margin: 0 0 14px; text-align: justify; }
        .centered { text-align: center; }
        .details { margin: 0 0 17px; }
        .details td { padding: 2px 0; }
        .details .label { width: 29%; }
        .details .separator { text-align: center; width: 3%; }
        .details .value { padding-left: 4px; width: 68%; }
        .signature { margin-top: 44px; }
        .signature-left { font-size: 10.5pt; padding-top: 104px; width: 50%; }
        .signature-right { text-align: center; width: 50%; }
        .signature-space { height: 94px; position: relative; }
        .verification-qr { height: 82px; margin-top: 7px; width: 82px; }
        .official-name { font-weight: bold; text-decoration: underline; }
        .tembusan { font-size: 10.5pt; font-style: italic; line-height: 1.32; margin-top: 6px; }
    </style>
</head>
<body>
    @php
        $tanggalAcuan = $data_sk->sk_created_at ?? null;
        $dekan = helper::getDekanByTanggal($tanggalAcuan);
        $mahasiswa = \App\Model\t_mst_mahasiswa::where('C_NPM', $data_sk->C_NPM)->first();
        $namaMahasiswa = optional($mahasiswa)->NAMA_MAHASISWA ?: '-';
        $judulTugasAkhir = helper::judulDenganKodeJenisTugasAkhir($data_sk->jenis_tugas_akhir_id, $data_sk->judul) ?: '-';
        try {
            $tanggalSurat = Illuminate\Support\Carbon::parse($tanggalAcuan ?: date('Y-m-d'));
        } catch (\Exception $exception) {
            $tanggalSurat = Illuminate\Support\Carbon::today();
        }
        $pembimbing = [
            ['jabatan' => 'Pembimbing Utama', 'kode' => $data_sk->pembimbing_I_id],
            ['jabatan' => 'Pembimbing Pendamping', 'kode' => $data_sk->pembimbing_II_id],
        ];
        $verificationUrl = url('sk_pembimbing/' . str_replace('/', '', $data_sk->nomor_sk));
    @endphp

    @foreach ($pembimbing as $penugasan)
        @if (!empty($penugasan['kode']))
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
                <div class="document-number">Nomor : {{ $data_sk->nomor_sk }}</div>

                <p>Dengan rahmat Allah SWT, sesuai dengan surat Ketua Program Studi {{ helper::getProgramStudiByStambuk($data_sk->C_NPM) }} nomor : {{ helper::getNomorSKWithBimbinganId($data_sk->bimbingan_id) }} tentang tugas akhir mahasiswa, maka:</p>
                <p class="centered">DEKAN FAKULTAS ILMU KOMPUTER</p>
                <p>Menunjuk Saudara:</p>
                <table class="details">
                    <tr><td class="label">Nama</td><td class="separator">:</td><td class="value"><strong>{{ helper::getDeskripsi($penugasan['kode']) }}</strong></td></tr>
                    <tr><td class="label">Pangkat/Gol.</td><td class="separator">:</td><td class="value"><strong>{{ helper::getJabatanFungsionalByNIDN($penugasan['kode']) }}</strong></td></tr>
                </table>
                <p>Sebagai {{ $penugasan['jabatan'] }} untuk Tugas Akhir Mahasiswa:</p>
                <table class="details">
                    <tr><td class="label">Nama / Stambuk</td><td class="separator">:</td><td class="value"><strong>{{ $namaMahasiswa }} / {{ $data_sk->C_NPM }}</strong></td></tr>
                    <tr><td class="label">Topik Penelitian</td><td class="separator">:</td><td class="value"><strong>{{ $judulTugasAkhir }}</strong></td></tr>
                </table>
                <p>Demikian surat penunjukan ini disampaikan kepada yang bersangkutan untuk dilaksanakan dengan penuh tanggung jawab dan amanah.</p>
                <p>Waalahu Waliyyut Taufiq wal-Hidayah.</p>

                <table class="signature">
                    <tr>
                        <td class="signature-left"><span class="tembusan"><u>Tembusan:</u><br>1. Yayasan Wakaf UMI<br>2. Rektor UMI<br>3. Ketua Program Studi FIKOM UMI</span></td>
                        <td class="signature-right">Makassar, {{ helper::tgl_indo_lengkap($tanggalSurat->format('Y-m-d')) }}<br>Dekan
                            <div class="signature-space">
                                @if ((int) $data_sk->status_sk === 2)
                                    <a href="{{ $verificationUrl }}"><img class="verification-qr" src="{{ \App\Helper::qrCodeDataUri($verificationUrl, 130) }}" alt="QR verifikasi SK Pembimbing"></a>
                                @endif
                            </div>
                            <span class="official-name">{{ $dekan->nama ?: '-' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    @endforeach
</body>
</html>
