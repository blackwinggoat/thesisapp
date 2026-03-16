@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading">Sistem Informasi Program Studi <small>Tugas Akhir</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Hasil Ujian</li>
        </ol>


        <h3 class="page-heading">Hasil Ujian</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="row">

            <div class="col-md-8">
                <div class="the-box">
                    <fieldset>
                        <form action="{{url('dsn/detailhasil_proposalpost/')}}" method="post"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="reg_id" value="{{$data[0]->reg_id}}">
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Nama</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control bold-border" name="nama"
                                        value="{{$data[0]->NAMA_MAHASISWA}}" disabled />
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Sikap/Presentasi</label>
                                <div class="col-lg-8">
                                    <select id="nilai_1" class="form-control bold-border" name="nilai_1">
                                        @if ($nilai["nilai_1"] == 0)
                                            <option value="0" selected>--</option>
                                        @else
                                            <option value="{{str_replace(",",".",$nilai['nilai_1'])}}" selected>{{str_replace(",",".",$nilai['nilai_1'])}}</option>
                                        @endif
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                        @for ($i = 10; $i <= 15; $i++)
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                            @if ($i == 15)
                                                @for ($j = 1; $j < 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @else
                                                @for ($j = 1; $j <= 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"></label>
                                <div class="col-lg-8">
                                    <span>
                                        1.1 Penguasaan isi proposal
                                        <br>
                                        1.2 Kemampuan berargumentasi
                                        <br>
                                        1.3 Percaya diri dalam menyampaikan
                                        <br>
                                        1.4 Sistematis dan logis saat menjawab
                                        <br>
                                    </span>
                                </div>
                            </div>
                            <br><br>
                            <br><br>
                            <br><br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Motivasi Penelitian</label>
                                <div class="col-lg-8">
                                <select id="nilai_2" class="form-control bold-border" name="nilai_2">
                                    @if ($nilai["nilai_2"] == 0)
                                        <option value="0" selected>--</option>
                                    @else
                                        <option value="{{str_replace(",",".",$nilai['nilai_2'])}}" selected>{{str_replace(",",".",$nilai['nilai_2'])}}</option>
                                    @endif
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                        @for ($i = 16; $i <= 25; $i++)
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                            @if ($i == 25)
                                                @for ($j = 1; $j < 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @else
                                                @for ($j = 1; $j <= 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"></label>
                                <div class="col-lg-8">
                                    2.1 Pengembangan IPTEK
                                    <br>
                                    2.2 Relevansi Judul /Kesesuaian dengan Judul dan Isi
                                    <br>
                                    2.3 Ketajaman Perumusan Masalah
                                    <br>
                                    2.4 Tujuan Penelitian
                                    <br>
                                    2.5 Kebaharuan dan Originalitas
                                    <br>
                                </div>
                            </div>
                            <br><br>
                            <br><br>
                            <br><br>
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Tinjauan Pustaka</label>
                                <div class="col-lg-8">
                                <select id="nilai_3" class="form-control bold-border" name="nilai_3">
                                    @if ($nilai["nilai_3"] == 0)
                                        <option value="0" selected>--</option>
                                    @else
                                        <option value="{{str_replace(",",".",$nilai['nilai_3'])}}" selected>{{str_replace(",",".",$nilai['nilai_3'])}}</option>
                                    @endif
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                        @for ($i = 15; $i <= 20; $i++)
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                            @if ($i == 20)
                                                @for ($j = 1; $j < 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @else
                                                @for ($j = 1; $j <= 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"></label>
                                <div class="col-lg-8">
                                    <span>
                                        3.1 Relevansi Pustaka
                                        <br>
                                        3.2 Kemutakhiran Pustaka
                                        <br>
                                        3.3 Kedalaman Tinjauan Pustaka
                                        <br>
                                        3.4 Referensi Jurnal
                                        <br>
                                    </span>
                                </div>
                            </div>
                            <br><br>
                            <br><br>
                            <br><br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Metodologi</label>
                                <div class="col-lg-8">
                                <select id="nilai_4" class="form-control bold-border" name="nilai_4">
                                    @if ($nilai["nilai_4"] == 0)
                                        <option value="0" selected>--</option>
                                    @else
                                        <option value="{{str_replace(",",".",$nilai['nilai_4'])}}" selected>{{str_replace(",",".",$nilai['nilai_4'])}}</option>
                                    @endif
                                    
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                        @for ($i = 15; $i <= 20; $i++)
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                            @if ($i == 20)
                                                @for ($j = 1; $j < 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @else
                                                @for ($j = 1; $j <= 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"></label>
                                <div class="col-lg-8">
                                    <span>
                                        4.1 Ketepatan Desain dan Instrumen
                                        <br>
                                        4.2 Ketepatan Metode yang digunakan
                                        <br>
                                    </span>
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Sistematika Penulisan</label>
                                <div class="col-lg-8">
                                <select id="nilai_5" class="form-control bold-border" name="nilai_5">
                                    @if ($nilai["nilai_5"] == 0)
                                        <option value="0" selected>--</option>
                                    @else
                                        <option value="{{str_replace(",",".",$nilai['nilai_5'])}}" selected>{{str_replace(",",".",$nilai['nilai_5'])}}</option>
                                    @endif
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                        @for ($i = 15; $i <= 20; $i++)
                                        @php
                                            $setelah_koma = 0;
                                        @endphp
                                            @if ($i == 20)
                                                @for ($j = 1; $j < 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @else
                                                @for ($j = 1; $j <= 2; $j++)
                                                <option value="{{$i}}.{{$setelah_koma}}">{{$i}}.{{$setelah_koma}}</option>
                                                @php
                                                    $setelah_koma = 5;
                                                @endphp
                                            @endfor
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"></label>
                                <div class="col-lg-8">
                                    <span>
                                        5.1 Kelengkapan/Kesesuaian Penulisan
                                        <br>
                                        5.2 Format Penulisan
                                        <br>
                                    </span>
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Saran</label>
                                <div class="col-lg-8">
                                    <textarea name="saran" class="form-control bold-border" cols="77"
                                        rows="10">{{$nilai['saran']}}</textarea>
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-xs-12" align="right">
                                    <button id="tombol_satu" class="btn btn-info btn-perspective" type="button"
                                        onclick="showPostModal(this)"
                                        data-formaction="{{url('dsn/detailhasil_proposalpost/')}}"
                                        data-target="#modalPrimary" data-toggle="modal">Simpan</button>
                                </div>
                            </div>
                        </form>

                    </fieldset>
                </div>
            </div>
            <div class="col-md-4">
                <div class="the-box">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 style="font-weight: bold">Data Nilai Per Mahasiswa</h4>
                        </div>
                        <div class="col-md-6">
                            <h4>Total Nilai</h4>
                        </div>
                        <div class="col-md-6">
                            <h4 id="total_nilai_final" class="badge-info badge">{{$nilai['nilai_1'] + $nilai['nilai_2'] + $nilai['nilai_3'] + $nilai['nilai_4'] + $nilai['nilai_5'] }}</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Index Nilai</h4>
                        </div>
                        <div id="index_nilai_final" class="col-md-6">
                            @php
                                $nilai = $nilai['nilai_1'] + $nilai['nilai_2'] + $nilai['nilai_3'] + $nilai['nilai_4'] + $nilai['nilai_5']
                            @endphp
                            @if ($nilai > 85)
                            <h4 class="badge badge-primary">A</h4>
                            @elseif($nilai >= 81 && $nilai <= 85)
                            <h4 class="badge badge-primary">A-</h4>
                            @elseif($nilai >= 76 && $nilai <= 80)
                            <h4 class="badge badge-primary">B+</h4>
                            @elseif($nilai >= 71 && $nilai <= 75)
                            <h4 class="badge badge-primary">B</h4>
                            @endif
                        </div>
                    </div>
                        <div class="row">

<div class="col-md-12">
            <table border="1" width="100%" cellpadding="1" cellspacing="0">
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
            <span style="font-size: 12px"><i>Sumber: Peraturan No. 1 Tahun 2014 UMI Tentang Ketentuan Pokok Akademik Pasal 43 Predikat Kelulusan</i></span>
</div>

    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    {{--ModalTerima--}}
    @section("modalPrimaryTitle")
    Penilaian & Saran
    @endsection
    @section("modalPrimaryBody")
    Apakah Anda yakin ingin memberi nilai dan saran ini ?
    <br>
    <span id="status" class="badge badge-danger"></span>
    @endsection
    @section("modalPrimaryFooter")
    <button onclick="submit(this)" id="tombol_dua" class="btn btn-default">Kirim</button>
    @endsection

    @section("script")
    <script>

        $('#nilai_1').on('click', function () {
            var nilai_1 = $('select[name="nilai_1"]').val();
            var nilai_2 = $('select[name="nilai_2"]').val();
            var nilai_3 = $('select[name="nilai_3"]').val();
            var nilai_4 = $('select[name="nilai_4"]').val();
            var nilai_5 = $('select[name="nilai_5"]').val();
            var nilai_final = parseFloat(nilai_1)+parseFloat(nilai_2)+parseFloat(nilai_3)+parseFloat(nilai_4)+parseFloat(nilai_5);
            $('#total_nilai_final').html(nilai_final);
                if (nilai_final > 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A</h4>`);
                }else if(nilai_final >= 81 && nilai_final <= 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A-</h4>`);
                }else if(nilai_final >= 76 && nilai_final <= 80){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B+</h4>`);
                }else if(nilai_final >= 71 && nilai_final <= 75){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B</h4>`);
                }else{
                    
                }
        });

                $('#nilai_2').on('click', function () {
            var nilai_1 = $('select[name="nilai_1"]').val();
            var nilai_2 = $('select[name="nilai_2"]').val();
            var nilai_3 = $('select[name="nilai_3"]').val();
            var nilai_4 = $('select[name="nilai_4"]').val();
            var nilai_5 = $('select[name="nilai_5"]').val();
            var nilai_final = parseFloat(nilai_1)+parseFloat(nilai_2)+parseFloat(nilai_3)+parseFloat(nilai_4)+parseFloat(nilai_5);
            $('#total_nilai_final').html(nilai_final);
                if (nilai_final > 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A</h4>`);
                }else if(nilai_final >= 81 && nilai_final <= 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A-</h4>`);
                }else if(nilai_final >= 76 && nilai_final <= 80){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B+</h4>`);
                }else if(nilai_final >= 71 && nilai_final <= 75){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B</h4>`);
                }else{
                    
                }
        });

                $('#nilai_3').on('click', function () {
            var nilai_1 = $('select[name="nilai_1"]').val();
            var nilai_2 = $('select[name="nilai_2"]').val();
            var nilai_3 = $('select[name="nilai_3"]').val();
            var nilai_4 = $('select[name="nilai_4"]').val();
            var nilai_5 = $('select[name="nilai_5"]').val();
            var nilai_final = parseFloat(nilai_1)+parseFloat(nilai_2)+parseFloat(nilai_3)+parseFloat(nilai_4)+parseFloat(nilai_5);
            $('#total_nilai_final').html(nilai_final);
                if (nilai_final > 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A</h4>`);
                }else if(nilai_final >= 81 && nilai_final <= 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A-</h4>`);
                }else if(nilai_final >= 76 && nilai_final <= 80){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B+</h4>`);
                }else if(nilai_final >= 71 && nilai_final <= 75){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B</h4>`);
                }else{
                    
                }
        });

                $('#nilai_4').on('click', function () {
            var nilai_1 = $('select[name="nilai_1"]').val();
            var nilai_2 = $('select[name="nilai_2"]').val();
            var nilai_3 = $('select[name="nilai_3"]').val();
            var nilai_4 = $('select[name="nilai_4"]').val();
            var nilai_5 = $('select[name="nilai_5"]').val();
            var nilai_final = parseFloat(nilai_1)+parseFloat(nilai_2)+parseFloat(nilai_3)+parseFloat(nilai_4)+parseFloat(nilai_5);
            $('#total_nilai_final').html(nilai_final);
                if (nilai_final > 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A</h4>`);
                }else if(nilai_final >= 81 && nilai_final <= 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A-</h4>`);
                }else if(nilai_final >= 76 && nilai_final <= 80){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B+</h4>`);
                }else if(nilai_final >= 71 && nilai_final <= 75){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B</h4>`);
                }else{
                    
                }
        });

                $('#nilai_5').on('click', function () {
            var nilai_1 = $('select[name="nilai_1"]').val();
            var nilai_2 = $('select[name="nilai_2"]').val();
            var nilai_3 = $('select[name="nilai_3"]').val();
            var nilai_4 = $('select[name="nilai_4"]').val();
            var nilai_5 = $('select[name="nilai_5"]').val();
            var nilai_final = parseFloat(nilai_1)+parseFloat(nilai_2)+parseFloat(nilai_3)+parseFloat(nilai_4)+parseFloat(nilai_5);
            $('#total_nilai_final').html(nilai_final);
                if (nilai_final > 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A</h4>`);
                }else if(nilai_final >= 81 && nilai_final <= 85){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">A-</h4>`);
                }else if(nilai_final >= 76 && nilai_final <= 80){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B+</h4>`);
                }else if(nilai_final >= 71 && nilai_final <= 75){
                    $('#index_nilai_final').html(`<h4 class="badge badge-primary">B</h4>`);
                }else{
                    
                }
        });
        
        $('#tombol_satu').click(function (e) {
            e.preventDefault();
            $('#total_nilai_final').html("");
            console.log("Selamat Datang");
            var nilai_1 = $('select[name="nilai_1"]').val();
            var nilai_2 = $('select[name="nilai_2"]').val();
            var nilai_3 = $('select[name="nilai_3"]').val();
            var nilai_4 = $('select[name="nilai_4"]').val();
            var nilai_5 = $('select[name="nilai_5"]').val();
            var saran = $('textarea[name="saran"]').val();
            console.log(nilai_1);
            console.log(nilai_2);
            console.log(nilai_3);
            console.log(nilai_4);
            console.log(nilai_5);
            if (nilai_5 != "0" || nilai_4 != "0" || nilai_3 != "0" || nilai_2 != "0" || nilai_1 != "0") {
                $('#status').html("");
                $("#tombol_dua").removeAttr("disabled");
            } else {
                console.log("Ini Bagian Satu");
                $('#tombol_dua').attr("disabled", "disabled");
                $('#status').html("Belum Ada Nilai Yang Diisi");
            }

            // if (nilai_5 == '' || nilai_4 == '' || nilai_3 == '' || nilai_2 == '' || nilai_1 == '') {
            //     console.log("Ini Bagian Satu");
            //     $('#tombol_dua').attr("disabled", "disabled");
            //     $('#status').html("Data Pada Form Belum Lengkap Atau Nilai Kurang atau Lebih Dari Range Yang Telah Ditentukan");
            // }

        });
        //Modal
        // $('#tombol_satu').on('click', function () {
        //     console.log("Selamat Datang di Bagian Satu");
        // var nilai_1 = $('input[name="nilai_1"]').val();
        // var nilai_2 = $('input[name="nilai_2"]').val();
        // var nilai_3 = $('input[name="nilai_3"]').val();
        // var nilai_4 = $('input[name="nilai_4"]').val();
        // var nilai_5 = $('input[name="nilai_5"]').val();
        // var saran = $('textarea[name="saran"]').val();
        // console.log(nilai_1);
        // console.log(nilai_2);
        // console.log(nilai_3);
        // console.log(nilai_4);
        // console.log(nilai_5);

        // if (($nilai_1 >= 10 $nilai_1 <= 15) || $nilai_2 >= 16 $nilai_2 <= 25) || $nilai_3 >= 15 $nilai_3 <= 20) || $nilai_4 >= 15 $nilai_4 <= 20) || $nilai_5 >= 15 $nilai_5 <= 20)) {
        //     $('#status').html("");
        //     $("#tombol_dua").removeAttr("disabled");
        // } else {
        //     console.log("Ini Bagian Satu");
        //     $('#tombol_dua').attr("disabled", "disabled");
        //     $('#status').html("Data Pada Form Belum Lengkap");
        // }
        // }

        let modal, modalId, modalFooter, link, form, formaction;

        const showPostModal = e => {
            formaction = e.getAttribute("data-formaction");
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
        };

        const showModal = e => {
            link = e.getAttribute("data-href");
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
        };

        const goOn = () => {
            modal.querySelector(".modal-backdrop").click();
            window.location.href = link;
        };

        const submit = () => {
            form = document.querySelector(`form[action="${formaction}"]`);
            form.submit();
        };

        const goOnNewTab = () => {
            modal.querySelector(".modal-backdrop").click();
            window.open(link);
        };
    </script>
    @endsection