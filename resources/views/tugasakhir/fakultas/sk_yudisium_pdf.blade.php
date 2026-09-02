<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK Yudisium {{ $programStudi }}</title>
    <style>
        @page { margin: 11mm 13mm 12mm; }
        * { box-sizing: border-box; }
        body { color: #000; font-family: "Times New Roman", serif; font-size: 10.2pt; line-height: 1.11; margin: 0; }
        .letter-page { page-break-after: always; width: 100%; }
        .letterhead table, .signature-table { border-collapse: collapse; width: 100%; }
        .letterhead td { vertical-align: middle; }
        .logo-umi { height: 50px; width: auto; }
        .logo-fikom { height: 40px; margin-left: 10px; vertical-align: middle; width: auto; }
        .letterhead-title { font-size: 11.7pt; font-weight: bold; line-height: 1.05; text-align: right; }
        .letterhead-divider { border-top: 3px double #000; margin-top: 7px; }
        .letterhead-address { font-size: 7.6pt; line-height: 1.2; padding-top: 8px; text-align: center; }
        .invocation { font-size: 11.5pt; font-style: italic; font-weight: bold; margin: 12px 0 10px; text-align: center; }
        .document-title { font-size: 13pt; font-weight: bold; margin: 0 0 2px; text-align: center; text-decoration: underline; }
        .document-number { font-size: 10.8pt; margin-bottom: 16px; text-align: center; }
        .about { font-size: 11.2pt; font-weight: bold; line-height: 1.13; margin: 0 0 12px; text-align: center; }
        .legal-intro { font-size: 10.8pt; font-weight: bold; margin-bottom: 7px; text-align: center; }
        .legal-table { border-collapse: collapse; margin: 0 0 10px; width: 100%; }
        .legal-table td { padding: 2px 0; vertical-align: top; }
        .legal-label { width: 22%; }
        .legal-separator { text-align: center; width: 4%; }
        .legal-value { text-align: justify; width: 74%; }
        .decision-heading { font-size: 11.2pt; font-weight: bold; margin: 8px 0; text-align: center; }
        .closing { margin: 0 0 8px; text-align: justify; }
        .blessing { font-style: italic; margin: 0; }
        .signature-table { margin-top: 12px; }
        .signature-table td { vertical-align: top; }
        .signature-right { text-align: center; width: 46%; }
        .signature-date { border-collapse: collapse; margin: 0 auto; text-align: left; }
        .signature-date td { padding: 0; vertical-align: top; }
        .signature-date-label { padding-right: 4px !important; white-space: nowrap; }
        .signature-date-separator { text-align: center; width: 12px; }
        .signature-date-value { padding-left: 5px !important; }
        .qr-space { line-height: 0; text-align: center; }
        .verification-qr { height: 68px; margin: 5px 0 3px; width: 68px; }
        .official-name { font-weight: bold; text-decoration: underline; }
        .attachment { width: 100%; }
        .attachment-meta { font-size: 10.5pt; margin: 0 0 16px; }
        .attachment-title { font-size: 12pt; font-weight: bold; line-height: 1.12; margin: 0 0 17px; text-align: center; }
        .yudisium-table { border-collapse: collapse; font-size: 7.6pt; line-height: 1.1; table-layout: fixed; width: 100%; }
        .yudisium-table th, .yudisium-table td { border: 1px solid #000; padding: 4px 3px; vertical-align: middle; }
        .yudisium-table th { font-size: 8.4pt; font-weight: bold; text-align: center; }
        .yudisium-table thead { display: table-header-group; }
        .yudisium-table tr { page-break-inside: avoid; }
        .center { text-align: center; }
        .name-cell { font-size: 8.3pt; font-weight: bold; }
        .date-cell { font-size: 7.3pt; white-space: nowrap; }
        .attachment-summary { margin-top: 14px; page-break-inside: avoid; width: 100%; }
        .criteria { font-size: 9.4pt; line-height: 1.35; width: 54%; }
        .criteria-title { font-size: 10.5pt; margin-bottom: 5px; }
        .attachment-signature { text-align: center; vertical-align: top; width: 46%; }
        .attachment-signature .verification-qr { height: 68px; margin: 7px 0 4px; width: 68px; }
    </style>
</head>
<body>
    <section class="letter-page">
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

        <div class="invocation">Bismillahir Rahmanir Rahiim</div>
        <div class="document-title">SURAT KEPUTUSAN</div>
        <div class="document-number">Nomor: {{ $dokumen->nomor_surat }}</div>

        <div class="about">
            TENTANG<br>
            YUDISIUM MAHASISWA PROGRAM STUDI {{ strtoupper($programStudi) }}<br>
            FAKULTAS ILMU KOMPUTER UMI<br>
            DENGAN RAHMAT ALLAH SWT
        </div>

        <div class="legal-intro">DEKAN FAKULTAS ILMU KOMPUTER</div>
        <table class="legal-table">
            <tr>
                <td class="legal-label">Menimbang</td>
                <td class="legal-separator">:</td>
                <td class="legal-value">Hasil Ujian Tugas Akhir mahasiswa Program Studi {{ $programStudi }} Fakultas Ilmu Komputer Universitas Muslim Indonesia.</td>
            </tr>
            <tr>
                <td class="legal-label">Mengingat</td>
                <td class="legal-separator">:</td>
                <td class="legal-value">Ketentuan akademik Universitas Muslim Indonesia yang berlaku.</td>
            </tr>
            <tr>
                <td class="legal-label">Memperhatikan</td>
                <td class="legal-separator">:</td>
                <td class="legal-value">Keputusan rapat Tim Penguji Fakultas Ilmu Komputer Universitas Muslim Indonesia tanggal {{ helper::tgl_indo_lengkap($tanggalSurat->format('Y-m-d')) }}.</td>
            </tr>
        </table>

        <div class="decision-heading">MEMUTUSKAN</div>
        <table class="legal-table">
            <tr>
                <td class="legal-label">Menetapkan</td>
                <td class="legal-separator">:</td>
                <td class="legal-value">Peserta Yudisium Ujian Tugas Akhir Mahasiswa Program Studi <strong>{{ $programStudi }} Fakultas Ilmu Komputer</strong> Universitas Muslim Indonesia dengan nama terlampir.</td>
            </tr>
        </table>

        <p class="closing">Demikian Surat Keputusan Yudisium ini dibuat untuk digunakan sebagaimana mestinya.</p>
        <p class="blessing">Wallahu Waliyyut Taufiq Walhidayah</p>

        <table class="signature-table">
            <tr>
                <td width="54%"></td>
                <td class="signature-right">
                    <table class="signature-date">
                        <tr>
                            <td class="signature-date-label">Ditetapkan di</td>
                            <td class="signature-date-separator">:</td>
                            <td class="signature-date-value">Makassar</td>
                        </tr>
                        <tr>
                            <td class="signature-date-label">Pada Tanggal</td>
                            <td class="signature-date-separator">:</td>
                            <td class="signature-date-value">{{ $tanggalHijriah }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="signature-date-value">{{ helper::tgl_indo_lengkap($tanggalSurat->format('Y-m-d')) }} M</td>
                        </tr>
                    </table>
                    <br>
                    Ketua Yudisium,<br>
                    Dekan,
                    <div class="qr-space">
                        <img class="verification-qr" src="{{ \App\Helper::qrCodeDataUri($verificationUrl, 130) }}" alt="QR verifikasi SK Yudisium">
                    </div>
                    <span class="official-name">{{ $namaDekan }}</span>
                </td>
            </tr>
        </table>
    </section>

    <section class="attachment">
        <p class="attachment-meta"><strong>Lampiran Surat</strong> : No: {{ $dokumen->nomor_surat }}</p>
        <div class="attachment-title">
            DAFTAR ALUMNI FAKULTAS ILMU KOMPUTER<br>
            UNIVERSITAS MUSLIM INDONESIA<br>
            PROGRAM STUDI {{ strtoupper($programStudi) }}
        </div>

        <table class="yudisium-table">
            <thead>
                <tr>
                    <th colspan="3" style="width: 24%;">Nomor</th>
                    <th rowspan="2" style="width: 23%;">Nama</th>
                    <th colspan="2" style="width: 12%;">Nilai T.A</th>
                    <th rowspan="2" style="width: 7%;">IPK</th>
                    <th colspan="4" style="width: 20%;">Yudisium Lulus</th>
                    <th rowspan="2" style="width: 14%;">Tgl/Tahun Kelulusan</th>
                </tr>
                <tr>
                    <th style="width: 4%;">Urt</th>
                    <th style="width: 14%;">Stambuk</th>
                    <th style="width: 6%;">Alumni</th>
                    <th style="width: 6%;">Angka</th>
                    <th style="width: 6%;">Huruf</th>
                    <th style="width: 5%;">I</th>
                    <th style="width: 5%;">II</th>
                    <th style="width: 5%;">III</th>
                    <th style="width: 5%;">IV</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($peserta as $mahasiswa)
                    <tr>
                        <td class="center">{{ sprintf('%02d', $loop->iteration) }}</td>
                        <td class="center">{{ $mahasiswa->nim }}</td>
                        <td class="center">{{ $mahasiswa->nomor_alumni }}</td>
                        <td class="name-cell">{{ $mahasiswa->nama }}</td>
                        <td class="center">{{ number_format($mahasiswa->nilai_ujian_ta, 2, ',', '.') }}</td>
                        <td class="center"><strong>{{ $mahasiswa->nilai_huruf }}</strong></td>
                        <td class="center"><strong>{{ number_format($mahasiswa->ipk, 2, ',', '.') }}</strong></td>
                        <td class="center">{{ $mahasiswa->kategori_yudisium === 'I' ? 'X' : '' }}</td>
                        <td class="center">{{ $mahasiswa->kategori_yudisium === 'II' ? 'X' : '' }}</td>
                        <td class="center">{{ $mahasiswa->kategori_yudisium === 'III' ? 'X' : '' }}</td>
                        <td class="center">{{ $mahasiswa->kategori_yudisium === 'IV' ? 'X' : '' }}</td>
                        <td class="center date-cell"><strong>{{ helper::tgl_indo_lengkap($tanggalSurat->format('Y-m-d')) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="attachment-summary">
            <tr>
                <td class="criteria">
                    <div class="criteria-title">Ketentuan Yudisium:</div>
                    I. Lulus Terpuji (Cum Laude) : &gt; 3,51<br>
                    II. Lulus Sangat Memuaskan : 3,01 - 3,50<br>
                    III. Lulus Memuaskan : 2,76 - 3,00<br>
                    IV. Cukup : 2,00 - 2,75
                </td>
                <td class="attachment-signature">
                    Makassar, {{ helper::tgl_indo_lengkap($tanggalSurat->format('Y-m-d')) }}<br><br>
                    Ketua Panitia Yudisium,<br>
                    Dekan,
                    <div class="qr-space">
                        <img class="verification-qr" src="{{ \App\Helper::qrCodeDataUri($verificationUrl, 120) }}" alt="QR verifikasi SK Yudisium">
                    </div>
                    <span class="official-name">{{ $namaDekan }}</span>
                </td>
            </tr>
        </table>
    </section>
</body>
</html>
