@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Periode Ujian</li>
        </ol>
        @php
            $showProdiColumn = !in_array(Auth::user()->name, ['proditi', 'prodisi', 'akademikproditi', 'akademikprodisi']);
        @endphp

        <h3 class="page-heading">Form Periode Pendaftaran</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <form method="post" action="{{url('prodi/jadwal')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Nama Periode</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="nama_periode" />
                        </div>
                    </div>
                    <br><br>
                    @if (Auth::user()->name == 'proditi' || Auth::user()->name == 'akademikproditi')
                        <input type="hidden" name="status_prodi" value="1">
                    @elseif (Auth::user()->name == 'prodisi' || Auth::user()->name == 'akademikprodisi')
                        <input type="hidden" name="status_prodi" value="2">
                    @else
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Program Studi</label>
                        <div class="col-xs-5">
                            <select class="form-control bold-border" name="status_prodi" required>
                                <option selected disabled>Pilih Program Studi</option>
                                <option value="1">Teknik Informatika</option>
                                <option value="2">Sistem Informasi</option>
                            </select>
                        </div>
                    </div>
                    <br><br>
                    @endif
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Tipe Ujian</label>
                        <div class="col-xs-5">
                            <select class="form-control bold-border" name="tipe_ujian">
                                <option value='0'>Proposal</option>
                                <option value='2'>Ujian Meja</option>
                                {{-- <option value='3'>Umum</option> --}}
                            </select>
                        </div><!-- /.col-xs-5 -->
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Tanggal Mulai</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control datepicker bold-border" data-date-format="yyyy-mm-dd"
                                placeholder="yyyy-mm-dd" name="tgl_start">
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Tanggal Tutup</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control datepicker bold-border" data-date-format="yyyy-mm-dd"
                                placeholder="yyyy-mm-dd" name="tgl_end">
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Kuota Peserta</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="kuota" />
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <div class="col-xs-7" align="right">
                            <button id="tombol_satu" class="btn btn-primary btn-perspective" type="button" onclick="showPostModal(this)"
                                data-formaction="{{url('prodi/jadwal')}}" data-target="#modalPrimary"
                                data-toggle="modal">Simpan</button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div><!-- /.the-box -->
        <!-- End breadcrumb -->
        <h3 class="page-heading">Daftar Periode Pendaftaran</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            @if($showProdiColumn)
                            <th>Program Studi</th>
                            @endif
                            <th>Periode</th>
                            <th>Tipe Ujian</th>
                            <th>Tanggal periode</th>
                            <th>Kuota</th>
                            <th>Pendaftar</th>
                            <th>Detail</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendaftaran as $key => $value)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$key}}</td>
                            @if($showProdiColumn)
                            <td>{{$value->prodi_label}}</td>
                            @endif
                            <td>{{$value->nama_periode}}</td>
                            <td>
                                @if($value->jumlah_tipe_ujian < 3) @if($value->tipe_ujian == 0)
                                    Proposal
                                    @elseif($value->tipe_ujian == 2)
                                    Ujian Meja
                                    @endif
                                @elseif($value->jumlah_tipe_ujian == 3)
                                    Umum
                                    @endif
                            </td>
                            <td>{{$value->tgl_start}} - {{$value->tgl_end}}</td>
                            <td>{{$value->kuota}}</td>
                            <td>{{$value->jml_peserta}}</td>
                            <td>
                                <a href="{{url('prodi/temp_daftar_peserta')}}/{{$value->pendaftaran_id}}"><i
                                        class="fa fa-copy icon-square icon-xs icon-primary"></i></a>
                            </td>
                            <td>
                                <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger"
                                    data-toggle="modal"
                                    data-href="{{ url('prodi/pendaftarandel/'.$value->pendaftaran_id)}}"><i
                                        class="fa fa-trash-o"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
        <h3 class="page-heading">Form Jadwal Ujian</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <form action="{{url("/prodi/jadwalujian")}}" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <fieldset>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Nama Periode</label>
                        <div class="col-xs-5">
                            <select class="form-control bold-border" name="pendaftaran_id"
                                onchange="namaPeriodeChange(this)" required>
                                <option selected disabled>Pilih Periode</option>
                                @foreach ($mstpendaftaran as $value)
                                <option value="{{$value->pendaftaran_id}}">
                                    {{$value->nama_periode}}@if($showProdiColumn) - {{$value->prodi_label}}@endif
                                </option>
                                @endforeach
                            </select>
                        </div><!-- /.col-xs-5 -->
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Jumlah Peserta</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="jml_peserta" disabled />
                        </div>
                    </div>
                    <br><br>
                    <input type="hidden" class="form-control bold-border" name="tipe_ujian" />
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Tanggal Ujian</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control datepicker bold-border" data-date-format="yyyy-mm-dd"
                                placeholder="yyyy-mm-dd" name="tgl_ujian" required>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <div class="col-xs-7" align="right">
                            <button id="tombol_tiga" type="button" onclick="showPostModal(this)" data-target="#modalPrimary"
                                data-toggle="modal" data-formaction="{{url("/prodi/jadwalujian")}}"
                                class="btn btn-primary btn-perspective">Simpan</button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div><!-- /.the-box -->
        <!-- End breadcrumb -->
        <h3 class="page-heading">Daftar Jadwal Ujian</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            @if($showProdiColumn)
                            <th>Program Studi</th>
                            @endif
                            <th>Tanggal Ujian</th>
                            <th>Nama Periode</th>
                            <th>Tipe Ujian</th>
                            <th>Jumlah Peserta</th>
                            {{-- {{-- <th>Status Ujian</th> --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalujian as $i => $d)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$i}}</td>
                            @if($showProdiColumn)
                            <td>{{$d->prodi_label}}</td>
                            @endif
                            <td>{{$d->tgl_ujian}}</td>
                            <td>{{$d->nama_periode}}</td>
                            @php
                            if($d->tipe_ujian == 0):
                            $tipe = "Proposal";
                            elseif($d->tipe_ujian == 2):
                            $tipe = "Ujian Meja";
                            endif;

                            @endphp
                            <td>{{$tipe}}</td>
                            <td>{{$d->jml_peserta}}</td>
                            {{-- <td>{{$d->status == 0 ? "<td>{{$value->status == 0 ? "<td>{{$d->status == 0 ? "Belum terlaksana" : "Terlaksana"}}
                            </td>" : "Terlaksana"}}</td>" : "Terlaksana"}}</td> --}}
                            <td>
                                <a class="btn btn-primary" href="{{ url('prodi/daftar_peserta/'.$d->pendaftaran_id)}}" title="Detail peserta">
                                    <i class="fa fa-copy"></i>
                                </a>
                                <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger"
                                    data-toggle="modal" data-href="{{ url("prodi/jadwalujiandel/$d->pendaftaran_id")}}"><i
                                        class="fa fa-trash-o"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->
</div>

@endsection

{{--ModalTambah--}}
@section("modalPrimaryTitle")
Simpan
@endsection
@section("modalPrimaryBody")
Apakah Anda yakin ingin menyimpan data?
<br>
<span id="status" class="badge badge-danger"></span>
@endsection
@section("modalPrimaryFooter")
<button onclick="submit(this)" id="tombol_dua" class="btn btn-default">Simpan</button>
@endsection

{{--ModalHapus--}}
@section("modalDangerTitle")
Hapus
@endsection
@section("modalDangerBody")
Apakah Anda yakin ingin menghapus data?
@endsection
@section("modalDangerFooter")
<button onclick="goOn(this)" class="btn btn-default">Hapus</button>
@endsection

@section("script")
<script>
    $('#tombol_satu').on('click', function () {
        console.log("Selamat Datang di Bagian Satu");
        var nama_periode = $('input[name="nama_periode"]').val();
        var tipe_ujian = $('select[name="tipe_ujian"]').val();
        var status_prodi = $('select[name="status_prodi"]').length ? $('select[name="status_prodi"]').val() : 'fixed';
        var tgl_start = $('input[name="tgl_start"]').val();
        var tgl_end = $('input[name="tgl_end"]').val();
        var kuota = $('input[name="kuota"]').val();
        console.log(nama_periode);
        console.log(tipe_ujian);
        console.log(tgl_start);
        console.log(tgl_end);
        console.log(kuota);
        

       if (nama_periode == "" || tipe_ujian == "" || status_prodi == "" || status_prodi == null || tgl_start == "" || tgl_end == "" || kuota == "") {
            console.log("Ini Bagian Satu");
            $('#tombol_dua').attr("disabled", "disabled");
            $('#status').html("Data Pada Form Belum Lengkap");
        } else {
            $('#status').html("");
            $("#tombol_dua").removeAttr("disabled");
        }      
    });

    $('#tombol_tiga').on('click', function () {
        console.log("Selamat Datang di Bagian Satu");
        var pendaftaran_id = $('select[name="pendaftaran_id"]').val();
        var jml_peserta = $('select[name="tipe_ujian"]').val();
        var tgl_ujian = $('input[name="tgl_ujian"]').val();
        console.log(pendaftaran_id);
        console.log(jml_peserta);
        console.log(tgl_ujian);
        

       if (pendaftaran_id == "" || jml_peserta == "" || tgl_ujian == "") {
            console.log("Ini Bagian Satu");
            $('#tombol_dua').attr("disabled", "disabled");
            $('#status').html("Data Pada Form Belum Lengkap");
        } else {
            $('#status').html("");
            $("#tombol_dua").removeAttr("disabled");
        }      
    });
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
        window.location.href = link;
    };

    const submit = () => {
        form = document.querySelector(`form[action="${formaction}"]`);
        form.submit();
    };

    const namaPeriodeChange = e => {
        axios.get(`https://thesis.fikom.app/api/getjumlahpeserta/${e.value}`).then(res => {
            document.querySelector("input[name=jml_peserta]").value = res.data
        })
        axios.get(`https://thesis.fikom.app/api/gettipeujian/${e.value}`).then(res => {
            document.querySelector("input[name=tipe_ujian]").value = res.data
        })
    }
</script>
@endsection
