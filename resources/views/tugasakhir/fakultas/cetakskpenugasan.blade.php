<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Surat Pengusulan</title>
    <style>
        body {
            height: 842px;
            width: 555px;
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
        $tanggalAcuanDekan = $data_sk[0]->created_at ?? null;
        $dekan = helper::getDekanByTanggal($tanggalAcuanDekan);
    @endphp
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

    <div class="title">
        <u>
            <h4 class="headingTitle">SURAT PENUGASAN</h4>
        </u><br>
        <h4 class="headingTitle">Nomor : {{ $data_sk[0]->nomor_sk }} </h4></u>
    </div>
    <br>
    <p align="justify">
        Dengan rahmat Allah SWT, sesuai peraturan Akademik Universitas Muslim Indonesia dan Surat Ketua Program Studi
        {{ helper::getProgramStudiByNim($data_sk[0]->C_NPM) }} nomor :
        {{ helper::getNomorSKPenugasanWithBimbinganId($data_sk[0]->pendaftaran_id) }}, tertanggal
        {{ helper::tgl_indo_lengkap(date('Y-m-d')) }}, maka dengan ini menetapkan Panitia Ujian Tugas Akhir sebagai
        berikut
    </p>
    <div>
        <table>
            <tr>
                <td width="150px">Pembimbing Utama</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $data_sk[0]->pembimbing_I_id)->first()->NAMA_DOSEN }}</td>
            </tr>
            <tr>
                <td>Pembimbing Pendamping</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $data_sk[0]->pembimbing_II_id)->first()->NAMA_DOSEN }}</td>
            </tr>
        </table>
    </div>
    <br>
    <div>
        <table>
            <tr>
                <td width="150px">Ketua Sidang</td>
                <td>:</td>
                <td>{{ \App\Dosen::where('C_KODE_DOSEN', $data_sk[0]->ketua_sidang_id)->first()->NAMA_DOSEN }}</td>
            </tr>
        </table>
    </div>
    <br>
    <div>
        <table>
            <tr>
                <td width="150px">Penguji</td>
                <td>:</td>
                <td>1. {{ \App\Dosen::where('C_KODE_DOSEN', $data_sk[0]->penguji_I_id)->first()->NAMA_DOSEN }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>2. {{ \App\Dosen::where('C_KODE_DOSEN', $data_sk[0]->penguji_II_id)->first()->NAMA_DOSEN }}</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>3. {{ \App\Dosen::where('C_KODE_DOSEN', $data_sk[0]->penguji_III_id)->first()->NAMA_DOSEN }}</td>
            </tr>
        </table>
    </div>
    <br>
    <p align="justify">
        Untuk melaksanakan Ujian Tugas Akhir bagi mahasiswa :
    </p>
    <div>
        <table>
            <tr>
                <td width="150px">Nama / Stambuk</td>
                <td>:</td>
                <td>{{ \App\Model\t_mst_mahasiswa::where('C_NPM', $data_sk[0]->C_NPM)->first()->NAMA_MAHASISWA }} /
                    {{ $data_sk[0]->C_NPM }}</td>
            </tr>
        </table>
    </div>
    <div>
        <table>
            <tr>
                <td width="150px">Judul Tugas Akhir</td>
                <td>:</td>
                <td>{{ helper::getJudulTugasAkhirByNim($data_sk[0]->C_NPM) }}</td>
            </tr>
        </table>
    </div>
    <div>
        @php
            $tanggal = Illuminate\Support\Carbon::parse($data_sk[0]->tgl_ujian)->formatLocalized('%d');
            $bulan = Illuminate\Support\Carbon::parse($data_sk[0]->tgl_ujian)->formatLocalized('%m');
            $tahun = Illuminate\Support\Carbon::parse($data_sk[0]->tgl_ujian)->formatLocalized('%Y');
            $tgl_ujian = Illuminate\Support\Carbon::parse($data_sk[0]->tgl_ujian)->formatLocalized('%A, %d %B %Y');
        @endphp
        <table>
            <tr>
                <td width="150px">Hari/Tanggal</td>
                <td>:</td>
                <td>{{ helper::getHari($tgl_ujian) }}, {{ $tanggal }} {{ helper::getBulan($bulan) }}
                    {{ $tahun }}</td>
            </tr>
        </table>
    </div>
    <div>
        <table>
            <tr>
                @php
                    # check count string jam ujian
                    $count_jam_ujian = strlen($data_sk[0]->jam_ujian);
                    if ($count_jam_ujian == 5) {
                        $waktu =
                            $data_sk[0]->jam_ujian .
                            '-' .
                            sprintf('%02d', substr($data_sk[0]->jam_ujian, 0, 2) + 2) .
                            ':30';
                    } else {
                        $waktu = $data_sk[0]->jam_ujian;
                    }
                @endphp
                <td width="150px">Waktu</td>
                <td>:</td>
                <td>{{ $waktu }}</td>
            </tr>
        </table>
    </div>
    <div>
        <table>
            <tr>
                <td width="150px">Tempat</td>
                <td>:</td>
                <td>{{ $data_sk[0]->nama_ruangan }}</td>
            </tr>
        </table>
    </div>
    <p align="justify">
        Demikian surat penugasan ini disampaikan, atas perhatian dan kehadiran Bapak diucapkan terima kasih
        <br><br>
        Waalahu Waliyyut Taufiq wal-Hidayah
    </p>

    <div class="legalitor">
        Makassar,
        {{ helper::tgl_indo_lengkap(Illuminate\Support\Carbon::parse(substr($data_sk[0]->created_at, 0, 10))->formatLocalized('%Y-%m-%d')) }}
        <br>
        Dekan
    </div>
    <br>
    <br>
    <div style="text-align: center; position: relative">
        @if (helper::getStatusFromSkPenugasan($data_sk[0]->sk_penugasan_id) == 0)
        @elseif(helper::getStatusFromSkPenugasan($data_sk[0]->sk_penugasan_id) == 1)

        @elseif(helper::getStatusFromSkPenugasan($data_sk[0]->sk_penugasan_id) == 2)
            <img src="{{ asset('gambar/stempelfakultas.png') }}" alt="" height="100px"
                style="position: absolute; right: 140px">
            @if (!empty($dekan->ttd))
                <img src="{{ asset('gambar/' . $dekan->ttd) }}" alt="" height="70px"
                    style="position: absolute; right: -20px">
            @endif
        @endif
    </div>
    <br><br><br><br>
    <div class="legalitor">
        <b><u>{{ $dekan->nama }}</u></b>
    </div>
    <div style="text-align: center; position: relative">
        @if (helper::getStatusFromSkPenugasan($data_sk[0]->sk_penugasan_id) == 0)
        @elseif(helper::getStatusFromSkPenugasan($data_sk[0]->sk_penugasan_id) == 1 ||
                helper::getStatusFromSkPenugasan($data_sk[0]->sk_penugasan_id) == 2)
            <img src="{{ asset('gambar/paraf_wd.png') }}" alt="" height="50px"
                style="position: absolute; right: -20px">
        @endif
    </div>
    <p align="justify">
        <i><u>Tembusan : </u>
            <br>
            1. Yayasan Wakaf UMI <br>
            2. Rektor UMI <br>
            3. Ketua Program Studi TI FIK UMI</i>
    </p>
    </table>
