<!DOCTYPE html>
<html>

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
            window.location = 'http://localhost:8000';
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
        $namaProdi = helper::getProgramStudiByAuthUser(Auth::user()->name);
        $kaprodi = helper::getKaprodiByProdiAndTanggal($namaProdi, $data[0]->tgl_ujian ?? null);
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
            <h4 class="textheader" style="margin: 0; font-size: 16px;">PROGRAM STUDI {{ strtoupper($namaProdi) }}</h4><br>
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
    <div>

        <table>
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td></td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>1 Lembar</td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td>Usulan Tim Ujian Tugas Akhir</td>
            </tr>
        </table>
    </div>
    <br>
    <p align="justify">
        Kpd. Yth.,<br>
        <b>Bapak Dekan Fakultas Ilmu Komputer</b><br>
        Di, - <br>
        Makassar <br><br>
        Assalamualaikum Wr. Wb.<br>
        Dengan Rahmat Allah S.W.T, Sehubungan dengan penyelesaian studi Mahasiswa PRogram Studi {{ $namaProdi }}
        Fakultas Ilmu Komputer UMI Semester Akhir 2018/2019, maka dengan ini kami mengusulkan nama-nama tim Ujian Tugas
        Akhir untuk dibuatkan SK penunjukan dengan susunan sebagai berikut:
    </p>
    <center>
        <table border="1" style="border-collapse: collapse;">
            <tr>
                <th>No</th>
                <th width="50px">Stambuk/ Nama Mahasiswa</th>
                <th>Judul Tugas Akhir</th>
                <th>Tim Penguji</th>
                <th>Waktu & Tempat</th>
            </tr>

            @foreach ($data as $key => $value)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $value->NAMA_MAHASISWA }} / {{ $value->C_NPM }}</td>
                    <td>{{ $value->judul }}</td>
                    @php
                        $pembimbing1 = \App\Dosen::where('C_KODE_DOSEN', $value->pembimbing_I_id)->first();
                        $pembimbing2 = \App\Dosen::where('C_KODE_DOSEN', $value->pembimbing_II_id)->first();
                        $penguji1 = \App\Dosen::where('C_KODE_DOSEN', $value->penguji_I_id)->first();
                        $penguji2 = \App\Dosen::where('C_KODE_DOSEN', $value->penguji_II_id)->first();
                        $penguji3 = \App\Dosen::where('C_KODE_DOSEN', $value->penguji_III_id)->first();
                        $ketua_sidang = \App\Dosen::where('C_KODE_DOSEN', $value->ketua_sidang_id)->first();
                    @endphp
                    <td>
                        Ketua Sidang : {{ $ketua_sidang->NAMA_DOSEN }}<br>
                        <hr color="black">
                        Pembimbing Utama : {{ $pembimbing1->NAMA_DOSEN }}<br>
                        <hr color="black">
                        Pembimbing Pendamping : {{ $pembimbing2->NAMA_DOSEN }}<br>
                        <hr color="black">
                        Penguji I : {{ $penguji1->NAMA_DOSEN }}<br>
                        <hr color="black">
                        Penguji II : {{ $penguji2->NAMA_DOSEN }}<br>
                        <hr color="black">
                        Penguji III : {{ $penguji3->NAMA_DOSEN }}<br>
                    </td>
                    <td>{{ $value->tgl_ujian }}</td>
                </tr>
            @endforeach
        </table>
    </center>
    <br><br>
    <div class="legalitor">
        Makassar,
    </div>
    <br><br><br><br><br>
    <div class="legalitor">
        <b><u>{{ $kaprodi->nama }}</u></b><br>
        <b>NIDN : {{ $kaprodi->nidn }}</b>
    </div>
</body>

</html>
