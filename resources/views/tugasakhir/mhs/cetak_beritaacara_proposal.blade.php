<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Surat Pengusulan</title>
    <style>
        body {
            height: 842px;
            width: 595px;
            /* to centre page on screen*/
            margin-left: auto;
            margin-right: auto;
        }

        .header img {
            width: 75px;
            display: inline;
            float: left;
        }

        .header {
            text-align: center;
            margin-top: 10px;
            position: relative;
        }

        .textheader {
            display: inline;
            margin-top: 100px;
            text-align: center;
        }

        .headerAddress {
            display: inline-block;
            margin-bottom: 0px;
            margin-top: 0px;
            text-align: left;
        }

        .headingTitle {
            display: inline;
        }

        .title {
            text-align: center;
        }

        .legalitor {
            float: right;
        }

        .button {
            background-color: #4CAF50;
            /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            position: relative;
        }
    </style>
    <script>
        function prints() {

            document.getElementById('btnPrint').style.display = "none";
            window.print();
            window.onafterprint = show();
        }

        function back() {
            window.location = 'report';
        }

        function show() {
            document.getElementById('btnBack').style.display = "inline";
            document.getElementById('btnPrint').style.display = "inline";
        }
    </script>
</head>
{{-- <button id="btnBack" onclick="back()" class="button">Kembali</button> --}}
<button id="btnPrint" onclick="prints()" class="button">Print</button>

<body>
    @php
        $namaProdi = helper::getProgramStudiByNim($nim);
        $kaprodi = helper::getKaprodiByNimAndTanggal($nim, $trt_penguji->created_at ?? null);
    @endphp
    <div class="header" style="position: relative; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; margin-right: 30px !important;">
            <img src="{{ asset('umi.png') }}" alt="Logo Institusi"
                style="width: 50px; height: auto; margin-right: 10px;" />
            <img src="{{ asset('fikom-logo.png') }}" alt="Logo Institusi" style="width: 150px; height: auto;" />
        </div>
        <div style="text-align: left;">
            <h4 class="textheader" style="margin: 0; font-size: 16px;">YAYASAN WAKAF UMI</h4><br>
            <h4 class="textheader" style="margin: 0; font-size: 16px;">UNIVERSITAS MUSLIM INDONESIA</h4><br>
            <h4 class="textheader" style="margin: 0; font-size: 16px;">FAKULTAS ILMU KOMPUTER</h4><br>
            @php
                $name = substr(Auth::user()->name, 0, 3);
            @endphp
            @if (Auth::user()->name == 'proditi' || $name == '130')
                <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI TEKNIK INFORMATIKA</h4><br>
            @else
                <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI SISTEM INFORMASI</h4><br>
            @endif
        </div>
    </div>
    <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
    <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>
    <div class="headerAddress" style="text-align: center; margin-top: 5px; font-size: 9px;">
        Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
        Fax (0411) - 453009 Makassar 90231
        <br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
    </div>
    <div class="title" style="text-align: center; margin-top: 20px;">
        <i>
            <h4 style="display: inline; font-weight bold;">Bismillahir Rahmanir Rahiim</h4>
        </i>
        <br><br>
    </div>
    <br>
    <div class="title">
        <u>
            <h1 class="headingTitle">TANDA TERIMA</h1>
        </u><br>
    </div>
    <br>
    <p align="justify">
        Dengan Rahmat Allah SWT. saya yang bertanda tangan dibawah ini telah menerima Skripsi mahasiswa untuk ujian
        {{ $tipe_ujian }} atas
    </p>
    <div>
        <table style="text-align: left">
            <tr>
                <th style="width: 150px">Nama</th>
                <td style="padding-right: 10px">:</td>
                <td>{{ ucwords(strtolower(\App\Model\t_mst_mahasiswa::where('C_NPM', $nim)->first()->NAMA_MAHASISWA)) }}
                </td>
            </tr>
            <tr>
                <th>Stambuk</th>
                <td>:</td>
                <td>{{ $nim }}</td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td>:</td>
                <td>{{ helper::getProgramStudiByNim($nim) }}</td>
            </tr>
            <tr>
                <th>Judul Tugas Akhir</th>
                <td>:</td>
                <td>{{ $trt_bimbingan->judul }}</td>
            </tr>
        </table>
    </div>
    <br>
    <div>
        <table border="1" width="550px" cellpadding="4" cellspacing="0">
            <tr>
                <th>No.</th>
                <th colspan="2">Nama Tim Penguji</th>
                <th colspan="2" width="130px">Tanda Tangan</th>
                <th width="60px">Tanggal Terima</th>
            </tr>
            <tr>
                <td>1</td>
                <td width="100px">Ketua Sidang</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->ketua_sidang_id)->first()->NAMA_DOSEN }}</td>
                <td>1</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Pembimbing Utama</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_I_id)->first()->NAMA_DOSEN }}</td>
                <td></td>
                <td>2</td>
                <td></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Pembimbing Pendamping</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_II_id)->first()->NAMA_DOSEN }}</td>
                <td>3</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>4</td>
                <td>Penguji 1</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_I_id)->first()->NAMA_DOSEN }}</td>
                <td></td>
                <td>4</td>
                <td></td>
            </tr>
            <tr>
                <td>5</td>
                <td>Penguji 2</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_II_id)->first()->NAMA_DOSEN }}</td>
                <td>5</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>6</td>
                <td>Penguji 3</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_III_id)->first()->NAMA_DOSEN }}</td>
                <td></td>
                <td>6</td>
                <td></td>
            </tr>
        </table>
    </div>
    <p align="justify">
        Demikian tanda terima ini dibuat untuk digunakan seperlunya
    </p>

    <div class="legalitor">
        Makassar, {{ $tgl_sekarang }}
        <br>
        Ketua Program Studi
    </div>
    <br><br><br><br>
    <div class="legalitor">
        <b><u>{{ $kaprodi->nama }}</u></b><br>
        <b>NIDN : {{ $kaprodi->nidn }}</b><br>

    </div>
    <br><br><br><br>
    {{-- <p align="justify"> --}}
    {{--    *) Coret yang tidak perlu --}}
    {{-- </p> --}}

    <div class="header"
        style="position: relative; display: flex; align-items: center; justify-content: space-between; page-break-before: always !important;">
        <div style="display: flex; align-items: center; margin-right: 30px !important;">
            <img src="{{ asset('umi.png') }}" alt="Logo Institusi"
                style="width: 50px; height: auto; margin-right: 10px;" />
            <img src="{{ asset('fikom-logo.png') }}" alt="Logo Institusi" style="width: 150px; height: auto;" />
        </div>
        <div style="text-align: left;">
            <h4 class="textheader" style="margin: 0; font-size: 16px;">YAYASAN WAKAF UMI</h4><br>
            <h4 class="textheader" style="margin: 0; font-size: 16px;">UNIVERSITAS MUSLIM INDONESIA</h4><br>
            <h4 class="textheader" style="margin: 0; font-size: 16px;">FAKULTAS ILMU KOMPUTER</h4><br>
            @php
                $name = substr(Auth::user()->name, 0, 3);
            @endphp
            @if (Auth::user()->name == 'proditi' || $name == '130')
                <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI TEKNIK INFORMATIKA</h4><br>
            @else
                <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI SISTEM INFORMASI</h4><br>
            @endif
        </div>
    </div>
    <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
    <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>
    <div class="headerAddress" style="text-align: center; margin-top: 5px; font-size: 9px;">
        Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
        Fax (0411) - 453009 Makassar 90231
        <br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
    </div>

    <div class="title">
        <h4><b>BERITA ACARA UJIAN {{ strtoupper($tipe_ujian) }}</b></h4>
    </div>
    <p align="center">
        Pada Hari ini............. Tanggal.......... Bulan......... Tahun.......... Pukul............ WITA <br>
        Bertempat di Ruang Sidang Prodi {{ helper::getProgramStudiByNim($nim) }} Telah Dilaksanakan <br>
        Ujian <b><u>{{ $tipe_ujian }}</u></b>, Mahasiswa (i);
    </p>
    <div>
        <table>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ ucwords(strtolower(\App\Model\t_mst_mahasiswa::where('C_NPM', $nim)->first()->NAMA_MAHASISWA)) }}
                    / {{ $nim }}</td>
            </tr>
            <tr>
                <td>Judul Skripsi</td>
                <td>:</td>
                <td>{{ $trt_bimbingan->judul }}</td>
            </tr>
            <tr>
                <td>Ketua Sidang</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->ketua_sidang_id)->first()->NAMA_DOSEN }}</td>
            </tr>
            <tr>
                <td style="padding-right: 20px">Pembimbing Utama</td>
                <td style="padding-right: 10px">:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_I_id)->first()->NAMA_DOSEN }}</td>
            </tr>
            <tr>
                <td>Pembimbing Kedua</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_II_id)->first()->NAMA_DOSEN }}</td>
            </tr>
            <tr>
                <td style="position: absolute">Tim Penguji</td>
                <td style="vertical-align: top">:</td>
                <td>1. {{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_I_id)->first()->NAMA_DOSEN }} <br>
                    2. {{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_II_id)->first()->NAMA_DOSEN }} <br>
                    3. {{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_III_id)->first()->NAMA_DOSEN }}
                </td>
            </tr>
        </table>
    </div>
    <p align="justify">
        <b>Tanggapan/Pertanyaan/Saran :</b>
    </p>
    <div>
        <table>
            <tr>
                <td style="position: absolute"><b>1</b></td>
                <td style="padding-left: 20px">
                    .................................................................................................................................................
                    .................................................................................................................................................
                    <br>
                    .................................................................................................................................................
                    <br>
                    ...........................................................................................................................................
                    <br>
                </td>
            </tr>
            <tr>
                <td style="position: absolute"><b>2</b></td>
                <td style="padding-left: 20px">
                    .................................................................................................................................................
                    .................................................................................................................................................
                    <br>
                    .................................................................................................................................................
                    <br>
                    ...........................................................................................................................................
                    <br>
                </td>
            </tr>
            <tr>
                <td style="position: absolute"><b>3</b></td>
                <td style="padding-left: 20px">
                    .................................................................................................................................................
                    .................................................................................................................................................
                    <br>
                    .................................................................................................................................................
                    <br>
                    ...........................................................................................................................................
                    <br>
                </td>
            </tr>
        </table>
    </div>
    <div class="legalitor">
        Makassar,........../........./20..
        <br>
    </div>
    <br><br><br><br>

    <br><br><br><br>
    {{-- <p align="justify"> --}}
    {{--    *) Coret yang tidak perlu --}}
    {{-- </p> --}}
    <div class="header"
        style="position: relative; display: flex; align-items: center; justify-content: space-between; page-break-before: always !important;">
        <div style="display: flex; align-items: center; margin-right: 30px !important;">
            <img src="{{ asset('umi.png') }}" alt="Logo Institusi"
                style="width: 50px; height: auto; margin-right: 10px;" />
            <img src="{{ asset('fikom-logo.png') }}" alt="Logo Institusi" style="width: 150px; height: auto;" />
        </div>
        <div style="text-align: left;">
            <h4 class="textheader" style="margin: 0; font-size: 16px;">YAYASAN WAKAF UMI</h4><br>
            <h4 class="textheader" style="margin: 0; font-size: 16px;">UNIVERSITAS MUSLIM INDONESIA</h4><br>
            <h4 class="textheader" style="margin: 0; font-size: 16px;">FAKULTAS ILMU KOMPUTER</h4><br>
            @php
                $name = substr(Auth::user()->name, 0, 3);
            @endphp
            @if (Auth::user()->name == 'proditi' || $name == '130')
                <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI TEKNIK INFORMATIKA</h4><br>
            @else
                <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI SISTEM INFORMASI</h4><br>
            @endif
        </div>
    </div>
    <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
    <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>
    <div class="headerAddress" style="text-align: center; margin-top: 5px; font-size: 9px;">
        Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
        Fax (0411) - 453009 Makassar 90231
        <br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
    </div>

    <div class="title">
        <h4><b>BERITA ACARA TANDA HADIR UJIAN {{ strtoupper($tipe_ujian) }}<br>
                SEMESTER GASAL/GENAP TAHUN AKADEMIK .................... / ....................</b></h4>
    </div>
    <div>
        <table>
            <tr>
                <td>Nama Mahasiswa</td>
                <td style="padding-right: 10px">:</td>
                <td colspan="2">
                    {{ ucwords(strtolower(\App\Model\t_mst_mahasiswa::where('C_NPM', $nim)->first()->NAMA_MAHASISWA)) }}
                </td>
            </tr>
            <tr>
                <td>Stambuk</td>
                <td>:</td>
                <td colspan="2">{{ $nim }}</td>
            </tr>
            <tr>
                <td style="padding-right: 50px">Fakultas/Proram Studi</td>
                <td>:</td>
                <td colspan="2">Ilmu Komputer/{{ helper::getProgramStudiByNim($nim) }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top">Peminatan</td>
                <td style="vertical-align: top">:</td>
                <td colspan="2">Rekayasa Perangkat Lunak / Sistem Jaringan / Informasi Industri</td>
            </tr>
            <tr>
                <td style="position: absolute">Judul Tugas Akhir</td>
                <td style="vertical-align: top">:</td>
                <td style="vertical-align: top" colspan="2">{{ $trt_bimbingan->judul }}
                </td>
            </tr>
            <tr>
                <td>Diseminarkan Pada</td>
                <td>:</td>
                <td colspan="2">Hari ........ Tanggal ........... Bulan ............ Tahun .........</td>
            </tr>
            <tr>
                <td>Lama Seminar</td>
                <td>:</td>
                <td colspan="2">(.........Jam) Mulai jam ........... Sampai jam ..................</td>
            <tr>
                <td><b>Formasi Tim Penguji</b></td>
                <td>:</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"><b>Status</b></td>
                <td><b>Nama Lengkap</b></td>
                <td><b>Tanda Tangan</b></td>
            </tr>
            <tr>
                <td>Pembimbing Utama</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_I_id)->first()->NAMA_DOSEN }}</td>
                <td>.........................</td>
            </tr>
            <tr>
                <td>Pembimbing Pendamping</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_II_id)->first()->NAMA_DOSEN }}</td>
                <td>.........................</td>
            </tr>
            <tr>
                <td>Ketua Sidang</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->ketua_sidang_id)->first()->NAMA_DOSEN }}</td>
                <td>.........................</td>
            </tr>
            <tr>
                <td>Tim Penanggap</td>
                <td>:</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="padding-left: 25px">Penanggap 1</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_I_id)->first()->NAMA_DOSEN }}</td>
                <td>.........................</td>
            </tr>
            <tr>
                <td style="padding-left: 25px">Penanggap 2</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_II_id)->first()->NAMA_DOSEN }}</td>
                <td>.........................</td>
            </tr>
            <tr>
                <td style="padding-left: 25px">Penanggap 3</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_III_id)->first()->NAMA_DOSEN }}</td>
                <td>.........................</td>
            </tr>
            <tr>
                <td colspan="4">Penanggap pengganti direkomendasikan kepada:</td>
            </tr>
            <tr>
                <td colspan="2"><b>Status</b></td>
                <td><b>Nama Lengkap</b></td>
                <td style=""><b>Tanda Tangan</b></td>
            </tr>
            <tr>
                <td colspan="2">..........................</td>
                <td>..................................................</td>
                <td>.........................</td>
            </tr>
            <tr>
                <td colspan="2">..........................</td>
                <td>..................................................</td>
                <td>.........................</td>
            </tr>
            <tr>
                <td colspan="4">Rekomendasi Tim Penanggap terhadap Judul Proposal Mahasiswa:</td>
            </tr>
            <tr>
                <td>Tim Penanggap</td>
                <td>:</td>
                <td colspan="2">Judul diterima / ditolak</td>
            </tr>
            <tr>
                <td>Ditolak dengan alasan</td>
                <td>:</td>
                <td colspan="2">
                    ......................................................................................</td>
            </tr>
            <tr>
                <td>Judul yang disarankan</td>
                <td>:</td>
                <td colspan="2">
                    ......................................................................................</td>
            </tr>
        </table>
    </div>
    <div class="legalitor">
        Makassar,........../........./20..
        <br>
        Ketua Program Studi
    </div>
    <br><br><br><br>
    <div class="legalitor">
        <b><u>{{ $kaprodi->nama }}</u></b><br>
        <b>NIDN : {{ $kaprodi->nidn }}</b><br>
    </div>
    @if ($trt_penguji->tipe_ujian != '0')
        <br><br>
        <br><br>
        <div class="header"
            style="position: relative; display: flex; align-items: center; justify-content: space-between; page-break-before: always !important;">
            <div style="display: flex; align-items: center; margin-right: 30px !important;">
                <img src="{{ asset('umi.png') }}" alt="Logo Institusi"
                    style="width: 50px; height: auto; margin-right: 10px;" />
                <img src="{{ asset('fikom-logo.png') }}" alt="Logo Institusi" style="width: 150px; height: auto;" />
            </div>
            <div style="text-align: left;">
                <h4 class="textheader" style="margin: 0; font-size: 16px;">YAYASAN WAKAF UMI</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">UNIVERSITAS MUSLIM INDONESIA</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">FAKULTAS ILMU KOMPUTER</h4><br>
                @php
                    $name = substr(Auth::user()->name, 0, 3);
                @endphp
                @if (Auth::user()->name == 'proditi' || $name == '130')
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI TEKNIK INFORMATIKA</h4>
                    <br>
                @else
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI SISTEM INFORMASI</h4><br>
                @endif
            </div>
        </div>
        <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
        <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>
        <div class="headerAddress" style="text-align: center; margin-top: 5px; font-size: 9px;">
            Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
            Fax (0411) - 453009 Makassar 90231
            <br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
        </div>

        <span style="border: solid 0.5px; width: 100%; display: flex"></span>
        <span style="border: solid 1.5px; width: 99.8%; display: flex; margin-top:2px"></span>
        <div class="title">
            <h4><b>LEMBAR PENILAIAN UJIAN {{ strtoupper($tipe_ujian) }}</b></h4>
        </div>
        <div>
            <table>
                <tr>
                    <td>STAMBUK</td>
                    <td>:</td>
                    <td colspan="2">{{ $nim }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 70px">NAMA MAHASISWA</td>
                    <td style="padding-right: 10px">:</td>
                    <td colspan="2">
                        {{ strtoupper(\App\Model\t_mst_mahasiswa::where('C_NPM', $nim)->first()->NAMA_MAHASISWA) }}
                    </td>
                </tr>
                <tr>
                    <td>HARI, TANGGAL UJIAN</td>
                    <td>:</td>
                    <td colspan="2">{{ strtoupper($tgl_ujian) }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top">JUDUL/SKRIPSI</td>
                    <td style="vertical-align: top">:</td>
                    <td style="vertical-align: top" colspan="2">{{ $trt_bimbingan->judul }}</td>
                </tr>
                <tr>
                    <td>RUANG</td>
                    <td>:</td>
                    <td colspan="2">{{ $ruangan }}</td>
                </tr>
            </table>
        </div>
        <div style="position: relative">
            <table style=" margin: 0 auto" border="1" width="550px" cellpadding="4" cellspacing="0">
                <tr>
                    <th width="10px">NO.</th>
                    <th>MATERI PENILAIAN</th>
                    <th>BOBOT</th>
                    <th width="125px">RANGE NILAI</th>
                    <th>NILAI</th>
                </tr>
                <tr>
                    <td align="center">1</td>
                    <td><b>Presentasi</b><br> (Media dan Penguasaan ruang lingkup permasalahan)</td>
                    <td align="center">10%</td>
                    <td align="center">0 - 10</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td><b>Penulisan</b><br> (Kelengkapasan/ Kesesuaian Penulisan dan Format Penulisan)</td>
                    <td align="center">20%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td><b>Penguasaan Materi:</b><br> <b>2.1 Dasar Teori</b> (Konsep Teori Riset, dan materi yang
                        relevan)<br><br><b>2.2 Aplikasi yang dibuat mahasiswa</b> (Pengoperasian OS dan Running Program)
                    </td>
                    <td align="center">40%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">4</td>
                    <td><b>Penguasaan Program/Aplikasi</b><br> (Penggunaan Data, Fungsi/Procedure, dll)</td>
                    <td align="center">30%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center" colspan="2">TOTAL NILAI</td>
                    <td align="center">100%</td>
                    <td align="center">0 - 100</td>
                    <td align="center">.......</td>
                </tr>
            </table>
        </div>
        <div class="legalitor">
            Makassar, .......... - ......... - 20...
            <br>
            <b>PEMBIMBING</b>
        </div>
        <br><br><br><br>
        <div class="legalitor">
            <b><u>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_I_id)->first()->NAMA_DOSEN }}</u></b><br>
            <b>NIDN : </b><br>
        </div>
        <br><br>
        <div>

            <table border="1" width="320px" cellpadding="1" cellspacing="0">
                <tr align="center">
                    <th width="100px">Nilai Angka</th>
                    <th width="100px">Nilai Mutu</th>
                    <th width="120px">Nilai Konversi</th>
                </tr>
                <tr align="center">
                    <td> > 85</td>
                    <td>A</td>
                    <td>4.00</td>
                </tr>
                <tr align="center">
                    <td>81 - 85</td>
                    <td>A-</td>
                    <td>3.75</td>
                </tr>
                <tr align="center">
                    <td>76 - 80</td>
                    <td>B+</td>
                    <td>3.50</td>
                </tr>
                <tr align="center">
                    <td>71 - 75</td>
                    <td>B</td>
                    <td>3.00</td>
                </tr>
            </table>
        </div>
        <br><br>
        <br><br>
        <div class="header"
            style="position: relative; display: flex; align-items: center; justify-content: space-between; page-break-before: always !important;">
            <div style="display: flex; align-items: center; margin-right: 30px !important;">
                <img src="{{ asset('umi.png') }}" alt="Logo Institusi"
                    style="width: 50px; height: auto; margin-right: 10px;" />
                <img src="{{ asset('fikom-logo.png') }}" alt="Logo Institusi" style="width: 150px; height: auto;" />
            </div>
            <div style="text-align: left;">
                <h4 class="textheader" style="margin: 0; font-size: 16px;">YAYASAN WAKAF UMI</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">UNIVERSITAS MUSLIM INDONESIA</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">FAKULTAS ILMU KOMPUTER</h4><br>
                @php
                    $name = substr(Auth::user()->name, 0, 3);
                @endphp
                @if (Auth::user()->name == 'proditi' || $name == '130')
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI TEKNIK INFORMATIKA</h4>
                    <br>
                @else
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI SISTEM INFORMASI</h4><br>
                @endif
            </div>
        </div>
        <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
        <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>
        <div class="headerAddress" style="text-align: center; margin-top: 5px; font-size: 9px;">
            Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
            Fax (0411) - 453009 Makassar 90231
            <br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
        </div>

        <span style="border: solid 0.5px; width: 100%; display: flex"></span>
        <span style="border: solid 1.5px; width: 99.8%; display: flex; margin-top:2px"></span>
        <div class="title">
            <h4><b>LEMBAR PENILAIAN UJIAN {{ strtoupper($tipe_ujian) }}</b></h4>
        </div>
        <div>
            <table>
                <tr>
                    <td>STAMBUK</td>
                    <td>:</td>
                    <td colspan="2">{{ $nim }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 70px">NAMA MAHASISWA</td>
                    <td style="padding-right: 10px">:</td>
                    <td colspan="2">
                        {{ strtoupper(\App\Model\t_mst_mahasiswa::where('C_NPM', $nim)->first()->NAMA_MAHASISWA) }}
                    </td>
                </tr>
                <tr>
                    <td>HARI, TANGGAL UJIAN</td>
                    <td>:</td>
                    <td colspan="2">{{ strtoupper($tgl_ujian) }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top">JUDUL/SKRIPSI</td>
                    <td style="vertical-align: top">:</td>
                    <td style="vertical-align: top" colspan="2">{{ $trt_bimbingan->judul }}</td>
                </tr>
                <tr>
                    <td>RUANG</td>
                    <td>:</td>
                    <td colspan="2">{{ $ruangan }}</td>
                </tr>
            </table>
        </div>
        <div style="position: relative">
            <table style=" margin: 0 auto" border="1" width="550px" cellpadding="4" cellspacing="0">
                <tr>
                    <th width="10px">NO.</th>
                    <th>MATERI PENILAIAN</th>
                    <th>BOBOT</th>
                    <th width="125px">RANGE NILAI</th>
                    <th>NILAI</th>
                </tr>
                <tr>
                    <td align="center">1</td>
                    <td><b>Presentasi</b><br> (Media dan Penguasaan ruang lingkup permasalahan)</td>
                    <td align="center">10%</td>
                    <td align="center">0 - 10</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td><b>Penulisan</b><br> (Kelengkapasan/ Kesesuaian Penulisan dan Format Penulisan)</td>
                    <td align="center">20%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td><b>Penguasaan Materi:</b><br> <b>2.1 Dasar Teori</b> (Konsep Teori Riset, dan materi yang
                        relevan)<br><br><b>2.2 Aplikasi yang dibuat mahasiswa</b> (Pengoperasian OS dan Running Program)
                    </td>
                    <td align="center">40%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">4</td>
                    <td><b>Penguasaan Program/Aplikasi</b><br> (Penggunaan Data, Fungsi/Procedure, dll)</td>
                    <td align="center">30%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center" colspan="2">TOTAL NILAI</td>
                    <td align="center">100%</td>
                    <td align="center">0 - 100</td>
                    <td align="center">.......</td>
                </tr>
            </table>
        </div>
        <div class="legalitor">
            Makassar, .......... - ......... - 20...
            <br>
            <b>PEMBIMBING</b>
        </div>
        <br><br><br><br>
        <div class="legalitor">
            <b><u>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_II_id)->first()->NAMA_DOSEN }}</u></b><br>
            <b>NIDN : </b><br>
        </div>
        <br><br>
        <div>

            <table border="1" width="320px" cellpadding="1" cellspacing="0">
                <tr align="center">
                    <th width="100px">Nilai Angka</th>
                    <th width="100px">Nilai Mutu</th>
                    <th width="120px">Nilai Konversi</th>
                </tr>
                <tr align="center">
                    <td> > 85</td>
                    <td>A</td>
                    <td>4.00</td>
                </tr>
                <tr align="center">
                    <td>81 - 85</td>
                    <td>A-</td>
                    <td>3.75</td>
                </tr>
                <tr align="center">
                    <td>76 - 80</td>
                    <td>B+</td>
                    <td>3.50</td>
                </tr>
                <tr align="center">
                    <td>71 - 75</td>
                    <td>B</td>
                    <td>3.00</td>
                </tr>
            </table>
        </div>
        <br><br>
        <br><br>
        <div class="header"
            style="position: relative; display: flex; align-items: center; justify-content: space-between; page-break-before: always !important;">
            <div style="display: flex; align-items: center; margin-right: 30px !important;">
                <img src="{{ asset('umi.png') }}" alt="Logo Institusi"
                    style="width: 50px; height: auto; margin-right: 10px;" />
                <img src="{{ asset('fikom-logo.png') }}" alt="Logo Institusi" style="width: 150px; height: auto;" />
            </div>
            <div style="text-align: left;">
                <h4 class="textheader" style="margin: 0; font-size: 16px;">YAYASAN WAKAF UMI</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">UNIVERSITAS MUSLIM INDONESIA</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">FAKULTAS ILMU KOMPUTER</h4><br>
                @php
                    $name = substr(Auth::user()->name, 0, 3);
                @endphp
                @if (Auth::user()->name == 'proditi' || $name == '130')
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI TEKNIK INFORMATIKA</h4>
                    <br>
                @else
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI SISTEM INFORMASI</h4><br>
                @endif
            </div>
        </div>
        <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
        <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>
        <div class="headerAddress" style="text-align: center; margin-top: 5px; font-size: 9px;">
            Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
            Fax (0411) - 453009 Makassar 90231
            <br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
        </div>
        <div>
            <table>
                <tr>
                    <td>STAMBUK</td>
                    <td>:</td>
                    <td colspan="2">{{ $nim }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 70px">NAMA MAHASISWA</td>
                    <td style="padding-right: 10px">:</td>
                    <td colspan="2">
                        {{ strtoupper(\App\Model\t_mst_mahasiswa::where('C_NPM', $nim)->first()->NAMA_MAHASISWA) }}
                    </td>
                </tr>
                <tr>
                    <td>HARI, TANGGAL UJIAN</td>
                    <td>:</td>
                    <td colspan="2">{{ strtoupper($tgl_ujian) }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top">JUDUL/SKRIPSI</td>
                    <td style="vertical-align: top">:</td>
                    <td style="vertical-align: top" colspan="2">{{ $trt_bimbingan->judul }}</td>
                </tr>
                <tr>
                    <td>RUANG</td>
                    <td>:</td>
                    <td colspan="2">{{ $ruangan }}</td>
                </tr>
            </table>
        </div>
        <div style="position: relative">
            <table style=" margin: 0 auto" border="1" width="550px" cellpadding="4" cellspacing="0">
                <tr>
                    <th width="10px">NO.</th>
                    <th>MATERI PENILAIAN</th>
                    <th>BOBOT</th>
                    <th width="125px">RANGE NILAI</th>
                    <th>NILAI</th>
                </tr>
                <tr>
                    <td align="center">1</td>
                    <td><b>Presentasi</b><br> (Media dan Penguasaan ruang lingkup permasalahan)</td>
                    <td align="center">10%</td>
                    <td align="center">0 - 10</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td><b>Penulisan</b><br> (Kelengkapasan/ Kesesuaian Penulisan dan Format Penulisan)</td>
                    <td align="center">20%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td><b>Penguasaan Materi:</b><br> <b>2.1 Dasar Teori</b> (Konsep Teori Riset, dan materi yang
                        relevan)<br><br><b>2.2 Aplikasi yang dibuat mahasiswa</b> (Pengoperasian OS dan Running Program)
                    </td>
                    <td align="center">40%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">4</td>
                    <td><b>Penguasaan Program/Aplikasi</b><br> (Penggunaan Data, Fungsi/Procedure, dll)</td>
                    <td align="center">30%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center" colspan="2">TOTAL NILAI</td>
                    <td align="center">100%</td>
                    <td align="center">0 - 100</td>
                    <td align="center">.......</td>
                </tr>
            </table>
        </div>
        <div class="legalitor">
            Makassar, .......... - ......... - 20...
            <br>
            <b>PENGUJI</b>
        </div>
        <br><br><br><br>
        <div class="legalitor">
            <b><u>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_I_id)->first()->NAMA_DOSEN }}</u></b><br>
            <b>NIDN : </b><br>
        </div>
        <br><br>
        <div>
            <table border="1" width="320px" cellpadding="1" cellspacing="0">
                <tr align="center">
                    <th width="100px">Nilai Angka</th>
                    <th width="100px">Nilai Mutu</th>
                    <th width="120px">Nilai Konversi</th>
                </tr>
                <tr align="center">
                    <td> > 85</td>
                    <td>A</td>
                    <td>4.00</td>
                </tr>
                <tr align="center">
                    <td>81 - 85</td>
                    <td>A-</td>
                    <td>3.75</td>
                </tr>
                <tr align="center">
                    <td>76 - 80</td>
                    <td>B+</td>
                    <td>3.50</td>
                </tr>
                <tr align="center">
                    <td>71 - 75</td>
                    <td>B</td>
                    <td>3.00</td>
                </tr>
            </table>
        </div>
        <br><br>
        <br><br>
        <div class="header"
            style="position: relative; display: flex; align-items: center; justify-content: space-between; page-break-before: always !important;">
            <div style="display: flex; align-items: center; margin-right: 30px !important;">
                <img src="{{ asset('umi.png') }}" alt="Logo Institusi"
                    style="width: 50px; height: auto; margin-right: 10px;" />
                <img src="{{ asset('fikom-logo.png') }}" alt="Logo Institusi" style="width: 150px; height: auto;" />
            </div>
            <div style="text-align: left;">
                <h4 class="textheader" style="margin: 0; font-size: 16px;">YAYASAN WAKAF UMI</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">UNIVERSITAS MUSLIM INDONESIA</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">FAKULTAS ILMU KOMPUTER</h4><br>
                @php
                    $name = substr(Auth::user()->name, 0, 3);
                @endphp
                @if (Auth::user()->name == 'proditi' || $name == '130')
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI TEKNIK INFORMATIKA</h4>
                    <br>
                @else
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI SISTEM INFORMASI</h4><br>
                @endif
            </div>
        </div>
        <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
        <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>
        <div class="headerAddress" style="text-align: center; margin-top: 5px; font-size: 9px;">
            Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
            Fax (0411) - 453009 Makassar 90231
            <br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
        </div>
        <div>
            <table>
                <tr>
                    <td>STAMBUK</td>
                    <td>:</td>
                    <td colspan="2">{{ $nim }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 70px">NAMA MAHASISWA</td>
                    <td style="padding-right: 10px">:</td>
                    <td colspan="2">
                        {{ strtoupper(\App\Model\t_mst_mahasiswa::where('C_NPM', $nim)->first()->NAMA_MAHASISWA) }}
                    </td>
                </tr>
                <tr>
                    <td>HARI, TANGGAL UJIAN</td>
                    <td>:</td>
                    <td colspan="2">{{ strtoupper($tgl_ujian) }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top">JUDUL/SKRIPSI</td>
                    <td style="vertical-align: top">:</td>
                    <td style="vertical-align: top" colspan="2">{{ $trt_bimbingan->judul }}</td>
                </tr>
                <tr>
                    <td>RUANG</td>
                    <td>:</td>
                    <td colspan="2">{{ $ruangan }}</td>
                </tr>
            </table>
        </div>
        <div style="position: relative">
            <table style=" margin: 0 auto" border="1" width="550px" cellpadding="4" cellspacing="0">
                <tr>
                    <th width="10px">NO.</th>
                    <th>MATERI PENILAIAN</th>
                    <th>BOBOT</th>
                    <th width="125px">RANGE NILAI</th>
                    <th>NILAI</th>
                </tr>
                <tr>
                    <td align="center">1</td>
                    <td><b>Presentasi</b><br> (Media dan Penguasaan ruang lingkup permasalahan)</td>
                    <td align="center">10%</td>
                    <td align="center">0 - 10</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td><b>Penulisan</b><br> (Kelengkapasan/ Kesesuaian Penulisan dan Format Penulisan)</td>
                    <td align="center">20%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td><b>Penguasaan Materi:</b><br> <b>2.1 Dasar Teori</b> (Konsep Teori Riset, dan materi yang
                        relevan)<br><br><b>2.2 Aplikasi yang dibuat mahasiswa</b> (Pengoperasian OS dan Running Program)
                    </td>
                    <td align="center">40%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">4</td>
                    <td><b>Penguasaan Program/Aplikasi</b><br> (Penggunaan Data, Fungsi/Procedure, dll)</td>
                    <td align="center">30%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center" colspan="2">TOTAL NILAI</td>
                    <td align="center">100%</td>
                    <td align="center">0 - 100</td>
                    <td align="center">.......</td>
                </tr>
            </table>
        </div>
        <div class="legalitor">
            Makassar, .......... - ......... - 20...
            <br>
            <b>PENGUJI</b>
        </div>
        <br><br><br><br>
        <div class="legalitor">
            <b><u>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_II_id)->first()->NAMA_DOSEN }}</u></b><br>
            <b>NIDN : </b><br>
        </div>
        <br><br>
        <div>
            <table border="1" width="320px" cellpadding="1" cellspacing="0">
                <tr align="center">
                    <th width="100px">Nilai Angka</th>
                    <th width="100px">Nilai Mutu</th>
                    <th width="120px">Nilai Konversi</th>
                </tr>
                <tr align="center">
                    <td> > 85</td>
                    <td>A</td>
                    <td>4.00</td>
                </tr>
                <tr align="center">
                    <td>81 - 85</td>
                    <td>A-</td>
                    <td>3.75</td>
                </tr>
                <tr align="center">
                    <td>76 - 80</td>
                    <td>B+</td>
                    <td>3.50</td>
                </tr>
                <tr align="center">
                    <td>71 - 75</td>
                    <td>B</td>
                    <td>3.00</td>
                </tr>
            </table>
        </div>
        <br><br>
        <br><br>
        <div class="header"
            style="position: relative; display: flex; align-items: center; justify-content: space-between; page-break-before: always !important;">
            <div style="display: flex; align-items: center; margin-right: 30px !important;">
                <img src="{{ asset('umi.png') }}" alt="Logo Institusi"
                    style="width: 50px; height: auto; margin-right: 10px;" />
                <img src="{{ asset('fikom-logo.png') }}" alt="Logo Institusi" style="width: 150px; height: auto;" />
            </div>
            <div style="text-align: left;">
                <h4 class="textheader" style="margin: 0; font-size: 16px;">YAYASAN WAKAF UMI</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">UNIVERSITAS MUSLIM INDONESIA</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">FAKULTAS ILMU KOMPUTER</h4><br>
                @php
                    $name = substr(Auth::user()->name, 0, 3);
                @endphp
                @if (Auth::user()->name == 'proditi' || $name == '130')
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI TEKNIK INFORMATIKA</h4>
                    <br>
                @else
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI SISTEM INFORMASI</h4><br>
                @endif
            </div>
        </div>
        <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
        <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>
        <div class="headerAddress" style="text-align: center; margin-top: 5px; font-size: 9px;">
            Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
            Fax (0411) - 453009 Makassar 90231
            <br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
        </div>
        <span style="border: solid 0.5px; width: 100%; display: flex"></span>
        <span style="border: solid 1.5px; width: 99.8%; display: flex; margin-top:2px"></span>
        <div class="title">
            <h4><b>LEMBAR PENILAIAN UJIAN {{ strtoupper($tipe_ujian) }}</b></h4>
        </div>
        <div>
            <table>
                <tr>
                    <td>STAMBUK</td>
                    <td>:</td>
                    <td colspan="2">{{ $nim }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 70px">NAMA MAHASISWA</td>
                    <td style="padding-right: 10px">:</td>
                    <td colspan="2">
                        {{ strtoupper(\App\Model\t_mst_mahasiswa::where('C_NPM', $nim)->first()->NAMA_MAHASISWA) }}
                    </td>
                </tr>
                <tr>
                    <td>HARI, TANGGAL UJIAN</td>
                    <td>:</td>
                    <td colspan="2">{{ strtoupper($tgl_ujian) }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top">JUDUL/SKRIPSI</td>
                    <td style="vertical-align: top">:</td>
                    <td style="vertical-align: top" colspan="2">{{ $trt_bimbingan->judul }}</td>
                </tr>
                <tr>
                    <td>RUANG</td>
                    <td>:</td>
                    <td colspan="2">{{ $ruangan }}</td>
                </tr>
            </table>
        </div>
        <div style="position: relative">
            <table style=" margin: 0 auto" border="1" width="550px" cellpadding="4" cellspacing="0">
                <tr>
                    <th width="10px">NO.</th>
                    <th>MATERI PENILAIAN</th>
                    <th>BOBOT</th>
                    <th width="125px">RANGE NILAI</th>
                    <th>NILAI</th>
                </tr>
                <tr>
                    <td align="center">1</td>
                    <td><b>Presentasi</b><br> (Media dan Penguasaan ruang lingkup permasalahan)</td>
                    <td align="center">10%</td>
                    <td align="center">0 - 10</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">2</td>
                    <td><b>Penulisan</b><br> (Kelengkapasan/ Kesesuaian Penulisan dan Format Penulisan)</td>
                    <td align="center">20%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">3</td>
                    <td><b>Penguasaan Materi:</b><br> <b>2.1 Dasar Teori</b> (Konsep Teori Riset, dan materi yang
                        relevan)<br><br><b>2.2 Aplikasi yang dibuat mahasiswa</b> (Pengoperasian OS dan Running Program)
                    </td>
                    <td align="center">40%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center">4</td>
                    <td><b>Penguasaan Program/Aplikasi</b><br> (Penggunaan Data, Fungsi/Procedure, dll)</td>
                    <td align="center">30%</td>
                    <td align="center">0 - 20</td>
                    <td align="center">.......</td>
                </tr>
                <tr>
                    <td align="center" colspan="2">TOTAL NILAI</td>
                    <td align="center">100%</td>
                    <td align="center">0 - 100</td>
                    <td align="center">.......</td>
                </tr>
            </table>
        </div>
        <div class="legalitor">
            Makassar, .......... - ......... - 20...
            <br>
            <b>PENGUJI</b>
        </div>
        <br><br><br><br>
        <div class="legalitor">
            <b><u>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_III_id)->first()->NAMA_DOSEN }}</u></b><br>
            <b>NIDN : </b><br>
        </div>
        <br><br>
        <div>
            <table border="1" width="320px" cellpadding="1" cellspacing="0">
                <tr align="center">
                    <th width="100px">Nilai Angka</th>
                    <th width="100px">Nilai Mutu</th>
                    <th width="120px">Nilai Konversi</th>
                </tr>
                <tr align="center">
                    <td> > 85</td>
                    <td>A</td>
                    <td>4.00</td>
                </tr>
                <tr align="center">
                    <td>81 - 85</td>
                    <td>A-</td>
                    <td>3.75</td>
                </tr>
                <tr align="center">
                    <td>76 - 80</td>
                    <td>B+</td>
                    <td>3.50</td>
                </tr>
                <tr align="center">
                    <td>71 - 75</td>
                    <td>B</td>
                    <td>3.00</td>
                </tr>
            </table>
        </div>
        <br><br>
        <br><br>
        <div class="header"
            style="position: relative; display: flex; align-items: center; justify-content: space-between; page-break-before: always !important;">
            <div style="display: flex; align-items: center; margin-right: 30px !important;">
                <img src="{{ asset('umi.png') }}" alt="Logo Institusi"
                    style="width: 50px; height: auto; margin-right: 10px;" />
                <img src="{{ asset('fikom-logo.png') }}" alt="Logo Institusi" style="width: 150px; height: auto;" />
            </div>
            <div style="text-align: left;">
                <h4 class="textheader" style="margin: 0; font-size: 16px;">YAYASAN WAKAF UMI</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">UNIVERSITAS MUSLIM INDONESIA</h4><br>
                <h4 class="textheader" style="margin: 0; font-size: 16px;">FAKULTAS ILMU KOMPUTER</h4><br>
            @php
                $name = substr(Auth::user()->name, 0, 3);
            @endphp
            @if (Auth::user()->name == 'proditi' || $name == '130')
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI TEKNIK INFORMATIKA</h4>
                    <br>
                @else
                    <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI SISTEM INFORMASI</h4><br>
                @endif
            </div>
        </div>
        <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
        <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>
        <div class="headerAddress" style="text-align: center; margin-top: 5px; font-size: 9px;">
            Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
            Fax (0411) - 453009 Makassar 90231
            <br>website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
        </div>
        <span style="border: solid 0.5px; width: 100%; display: flex"></span>
        <span style="border: solid 1.5px; width: 99.8%; display: flex; margin-top:2px"></span>
        <div class="title">
            <h4><b>REKAPITULASI NILAI UJIAN {{ strtoupper($tipe_ujian) }}</b></h4>
        </div>
        <div>
            <table>
                <tr>
                    <td>STAMBUK</td>
                    <td>:</td>
                    <td colspan="2">{{ $nim }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 70px">NAMA MAHASISWA</td>
                    <td style="padding-right: 10px">:</td>
                    <td colspan="2">
                        {{ strtoupper(\App\Model\t_mst_mahasiswa::where('C_NPM', $nim)->first()->NAMA_MAHASISWA) }}
                    </td>
                </tr>
                <tr>
                    <td>HARI, TANGGAL UJIAN</td>
                    <td>:</td>
                    <td colspan="2">{{ strtoupper($tgl_ujian) }}</td>
                </tr>
                <tr>
                    <td style="vertical-align: top">JUDUL/SKRIPSI</td>
                    <td style="vertical-align: top">:</td>
                    <td style="vertical-align: top" colspan="2">{{ $trt_bimbingan->judul }}</td>
                </tr>
                <tr>
                    <td>RUANG</td>
                    <td>:</td>
                    <td colspan="2">{{ $ruangan }}</td>
                </tr>
            </table>
        </div>
        <div style="position: relative">
            <table border="1" width="550px" cellpadding="4" cellspacing="0" style="margin: 0 auto">
                <tr>
                    <th>No.</th>
                    <th colspan="2">Nama Tim Penguji</th>
                    <th width="40px">Nilai</th>
                    <th width="130px">Paraf</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td width="100px">Ketua Sidang</td>
                    <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->ketua_sidang_id)->first()->NAMA_DOSEN }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Pembimbing Utama</td>
                    <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_I_id)->first()->NAMA_DOSEN }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Pembimbing Pendamping</td>
                    <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_bimbingan->pembimbing_II_id)->first()->NAMA_DOSEN }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Penguji 1</td>
                    <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_I_id)->first()->NAMA_DOSEN }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Penguji 2</td>
                    <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_II_id)->first()->NAMA_DOSEN }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>6</td>
                    <td>Penguji 3</td>
                    <td>{{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->penguji_III_id)->first()->NAMA_DOSEN }}
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right">Total:</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right">Nilai Ujian Rata-rata (Total/6):</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right">Nilai Huruf:</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
        <div style="position: relative">
            <div class="legalitor">
                Makassar, .......... - ......... - 20...
            </div>
            <br>
            <table>
                <tr>
                    <td style="text-align: center; white-space: nowrap">KETUA SIDANG</td>
                    <td width="280px"></td>
                </tr>
                <tr style="height: 40px"></tr>
                <tr>
                    <td style="text-align: center; white-space: nowrap; border-bottom: solid 1px">
                        {{ \App\Dosen::where('C_KODE_DOSEN', $trt_penguji->ketua_sidang_id)->first()->NAMA_DOSEN }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; white-space: nowrap"><b>NIP/NIDN:
                            {{ $trt_penguji->ketua_sidang_id }}</b></td>
                </tr>
            </table>
        </div>
        <br><br>
        <div>
            <table border="1" width="320px" cellpadding="1" cellspacing="0">
                <tr align="center">
                    <th width="100px">Nilai Angka</th>
                    <th width="100px">Nilai Mutu</th>
                    <th width="120px">Nilai Konversi</th>
                </tr>
                <tr align="center">
                    <td> > 85</td>
                    <td>A</td>
                    <td>4.00</td>
                </tr>
                <tr align="center">
                    <td>81 - 85</td>
                    <td>A-</td>
                    <td>3.75</td>
                </tr>
                <tr align="center">
                    <td>76 - 80</td>
                    <td>B+</td>
                    <td>3.50</td>
                </tr>
                <tr align="center">
                    <td>71 - 75</td>
                    <td>B</td>
                    <td>3.00</td>
                </tr>
            </table>
        </div>
    @endif
</body>

</html>
