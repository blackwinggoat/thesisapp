<?php

$mahasiswa = helper::getInformationCetakHonorarium($data[0]['C_NPM']);
$dekan = helper::getDekanByTanggal($data[0]['TANGGAL_UJIAN'] ?? null);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Honorarium Ujian Mahasiswa</title>
    <style>
        body {
            width: 842px;
            /* Landscape width */
            height: 595px;
            /* Landscape height */
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
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
            document.getElementById('btnPrint').style.display = "inline";
        }
    </script>
</head>
<button id="btnPrint" onclick="prints()" class="button">Print</button>

<body>
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
        </div>
    </div>

    <span style="border: solid 0.5px; width: 100%; display: block; margin-top: 10px;"></span>
    <span style="border: solid 1.5px; width: 100%; display: block; margin-top: 2px;"></span>

    <div class="headerAddress" style="text-align: center !important; margin-top: 5px; font-size: 9px;">
        Jln. Urip Sumohardjo Km.05 Gedung Fakultas Ilmu Komputer Lt.I Kampus II UMI Tlp.(0411) 449775-453308-453818,
        Fax (0411) - 453009 Makassar 90231
        website: fikom.umi.ac.id, email: S1.teknik.informatika@umi.ac.id
    </div>

    <div class="title" style="text-align: center; margin-top: 20px;">
        <h4 style="display: inline; font-weight bold; font-size: 12px">Jenis Kegiatan: {{ $data[0]['TIPE_UJIAN'] }}</h4>
        <br>
    </div>

    <table style="width: 100%; border-collapse: collapse; border: none; font-size: 12px;">
        <tr>
            <td style="width: 30%; text-align: left; border: none; padding: 0;">NAMA MAHASISWA</td>
            <td style="width: 70%; text-align: left; border: none; padding: 0;"><strong>{{$mahasiswa->NAMA_MAHASISWA}}</strong></td>
        </tr>
        <tr>
            <td style="width: 30%; text-align: left; border: none; padding: 0;">STAMBUK</td>
            <td style="width: 70%; text-align: left; border: none; padding: 0;"><strong>{{$data[0]['C_NPM']}}</strong></td>
        </tr>
        <tr>
            <td style="width: 30%; text-align: left; border: none; padding: 0;">FAKULTAS</td>
            <td style="width: 70%; text-align: left; border: none; padding: 0;"><strong>Fakultas Ilmu Komputer</strong>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; text-align: left; border: none; padding: 0;">PROGRAM STUDI</td>
            <td style="width: 70%; text-align: left; border: none; padding: 0;"><strong>{{$mahasiswa->nama_prodi}}</strong></td>
        </tr>
        <tr>
            <td style="width: 30%; text-align: left; border: none; padding: 0;">JUDUL TUGAS AKHIR</td>
            <td style="width: 70%; text-align: left; border: none; padding: 0;"><strong>{{$mahasiswa->topik}}</strong></td>
        </tr>

        <tr>
            <td colspan="2" style="border: none; padding: 10px 0;"></td>
        </tr>

        <tr>
            <td style="width: 30%; text-align: left; border: none; padding: 0;">HARI / TANGGAL</td>
            <td style="width: 70%; text-align: left; border: none; padding: 0;"><strong>{{helper::getDateNowWithParam($data[0]['TANGGAL_UJIAN'])}}</strong></td>
            </td>
        </tr>
        <tr>
            <td style="width: 30%; text-align: left; border: none; padding: 0;">WAKTU</td>
            <td style="width: 70%; text-align: left; border: none; padding: 0;"><strong>{{$mahasiswa->jam_ujian}} WITA</strong></td>
        </tr>
        <tr>
            <td style="width: 30%; text-align: left; border: none; padding: 0;">TEMPAT</td>
            <td style="width: 70%; text-align: left; border: none; padding: 0;"><strong>{{$mahasiswa->nama_ruangan}}</strong></td>
        </tr>
    </table>



    <table>
        <thead>
            <tr style="font-size: 8px;">
                <th>No</th>
                <th>Nama</th>
                <th>Posisi</th>
                <th>Bantuan Pustaka</th>
                <th>Transport</th>
                <th>Bantuan Internet</th>
                <th>Jumlah</th>
                <th>PPN 5%</th>
                <th>Total</th>
                <th>Potongan</th>
                <th>Tambahan</th>
                <th>Terima</th>
                <th>Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < count($data); $i++)
                <tr style="font-size: 8px;">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $data[$i]['NAMA'] }}</td>
                    <td>{{ $data[$i]['POSISI'] }}</td>
                    <td>{{ $data[$i]['PUSTAKA'] }}</td>
                    <td>{{ $data[$i]['TRANSPORT'] }}</td>
                    <td>{{ $data[$i]['BANTUAN INTERNET'] }}</td>
                    <td>{{ $data[$i]['JUMLAH'] }}</td>
                    <td>{{ $data[$i]['PPN'] }}</td>
                    <td>{{ $data[$i]['TOTAL'] }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="width: 5% !important;">
                        @if ($data[$i]['TANDA_TANGAN'] != null)
                            <img src="{{ 'data:image/png;base64,' . base64_encode($data[$i]['TANDA_TANGAN'])}}" alt="Tanda Tangan Dosen" style="height: 20px; object-fit: cover; object-position: center;">
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer" style="font-size: 12px;">
        <div style="display: inline-block; text-align: left;">
            <br>
            <p style="margin: 0;">Mengetahui,</p>
            <p style="margin: 0;">Dekan Fakultas Ilmu Komputer</p>
            <br>
            <br>
            <br>
            <p style="margin: 0;">{{ $dekan->nama }}</p>
        </div>
        <div style="display: inline-block; text-align: right; float: right;">
            <p style="margin: 0;">Makassar, {{helper::getDateNow()}}</p>
            <p style="margin: 0;">Wakil Dekan II</p>
            <p style="margin: 0;">Bagian Keuangan & Kepegawaian</p>
            <br>
            <br>
            <br>
            <p style="margin: 0;">Dr. Hj. Harlinda, S.Kom., MM., M.Kom</p>
        </div>
    </div>


</body>

</html>
