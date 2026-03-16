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
            <li><a href="{{ url('/')}}">Home</a></li>
            <li class="active">Dosen Pembimbing</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Daftar Dosen Pembimbing</h3>
        <div class="the-box">
            <div class="row">
                <div class="col-sm-2">
                    <span>PSH: Persiapan Seminar Hasil</span><br>
                    <span>PUM: Persiapan Ujian Meja</span><br>
                    <span>L: Lulusan</span>
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>NIDN</th>
                            <th>Nama</th>
                            <th>Jabatan Fungsional</th>
                            <th>Jumlah Bimbingan</th>
                            <th>Jumlah Bimbingan Semester Aktif</th>
                            <th>Level Pembimbing</th>
                            <th>Detail</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $value)
                        @php
                        $dosenpembimbingi = App\Model\trt_bimbingan::where("pembimbing_I_id",$value->C_KODE_DOSEN);
                        @endphp
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$key}}</td>
                            <td>{{$value->C_KODE_DOSEN}}</td>
                            <td>{{$value->NAMA_DOSEN}}</td>
                            <td>{{$value->jabatan_fungsional}}</td>
                            <td>
                                <span>PP: {{$dosenpembimbingi->where("status_bimbingan",0)->count()}}</span><br>
                                <span>PSH: {{$dosenpembimbingi->where("status_bimbingan",1)->count()}}</span><br>
                                <span>PUM: {{$dosenpembimbingi->where("status_bimbingan",2)->count()}}</span><br>
                                <span>L: {{$dosenpembimbingi->where("status_bimbingan",3)->count()}}</span>
                            </td>
                            <td>
                                @if(date("Y-m-01") >= date("Y-")."07-01")
                                <span>PP: {{$dosenpembimbingi->where("status_bimbingan",0)->whereDate("created_at",">=",date("Y")."-07-01")->count()}}</span><br>
                                <span>PSH: {{$dosenpembimbingi->where("status_bimbingan",1)->whereDate("created_at",">=",date("Y")."-07-01")->count()}}</span><br>
                                <span>PUM: {{$dosenpembimbingi->where("status_bimbingan",2)->whereDate("created_at",">=",date("Y")."-07-01")->count()}}</span><br>
                                <span>L: {{$dosenpembimbingi->where("status_bimbingan",3)->whereDate("created_at",">=",date("Y")."-07-01")->count()}}</span>
                                @elseif(date("Y-m-01") <= date("Y-")."07-01") <span>PP: {{$dosenpembimbingi->where("status_bimbingan",0)->whereDate("created_at","<=",date("Y")."-07-01")->count()}}</span><br>
                                    <span>PSH: {{$dosenpembimbingi->where("status_bimbingan",1)->whereDate("created_at","<=",date("Y")."-07-01")->count()}}</span><br>
                                    <span>PUM: {{$dosenpembimbingi->where("status_bimbingan",2)->whereDate("created_at","<=",date("Y")."-07-01")->count()}}</span><br>
                                    <span>L: {{$dosenpembimbingi->where("status_bimbingan",3)->whereDate("created_at","<=",date("Y")."-07-01")->count()}}</span>
                                    @endif
                            </td>
                            <td style="white-space: nowrap">
                                <button onclick="showModal(this)" data-target="#modalWarning" data-toggle="modal" data-href="{{ url("prodi/setlevelpembimbing/$value->C_KODE_DOSEN/3")}}" class="btn {{isset($value->level) ? $value->level == "3" ? "btn-success" : "btn-default text-info" : "btn-success"}}">P1 & P2
                                </button>
                                <button onclick="showModal(this)" data-target="#modalWarning" data-toggle="modal" data-href="{{ url("prodi/setlevelpembimbing/$value->C_KODE_DOSEN/1")}}" class="btn {{isset($value->level) ? $value->level == "1" ? "btn-info" : "btn-default text-info" : "btn-default text-info"}}">P1
                                </button>
                                <button onclick="showModal(this)" data-target="#modalWarning" data-toggle="modal" data-href="{{ url("prodi/setlevelpembimbing/$value->C_KODE_DOSEN/2")}}" class="btn {{isset($value->level) ? $value->level == "2" ? "btn-primary" : "btn-default text-info" : "btn-default text-info"}}">P2
                                </button>
                                <button onclick="showModal(this)" data-target="#modalWarning" data-toggle="modal" data-href="{{ url("prodi/setlevelpembimbing/$value->C_KODE_DOSEN/0")}}" class="btn {{isset($value->level) ? $value->level == "0" ? "btn-danger" : "btn-default text-info" : "btn-default text-info"}}">
                                    <i class="fa fa-ban"></i>
                                </button>
                            </td>
                            <td><a href="{{ url('prodi/detail_pembimbing/'.$value->C_KODE_DOSEN)}}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a></td>
                            <td style="white-space: nowrap">
                                <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal" data-href="{{ url('prodi/make_userx/'
                                    .$value->C_KODE_DOSEN)
                                    }}"><i class="fa
                                    fa-sign-in"></i></button>
                                <button class="btn btn-default" onclick="showModal(this)" data-target="#modalInfo" data-toggle="modal" data-href="{{ url('prodi/reset_userx/'.$value->C_KODE_DOSEN)
                                    }}"><i class="fa
                                    fa-recycle"></i></button>
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
{{--ModalSetUser--}}
@section("modalPrimaryTitle")
Daftarkan User
@endsection
@section("modalPrimaryBody")
Apakah Anda yakin ingin mendaftarkan user?
@endsection
@section("modalPrimaryFooter")
<button onclick="goOn(this)" class="btn btn-default">Daftarkan</button>
@endsection

{{--ModalResetUser--}}
@section("modalInfoTitle")
Reset User
@endsection
@section("modalInfoBody")
Apakah Anda yakin ingin me-reset user?
@endsection
@section("modalInfoFooter")
<button onclick="goOn(this)" class="btn btn-default">Reset</button>
@endsection

{{--ModalSetLevel--}}
@section("modalWarningTitle")
Ubah Level Pembimbing
@endsection
@section("modalWarningBody")
Apakah Anda yakin ingin mengubah level pembimbing?
@endsection
@section("modalWarningFooter")
<button onclick="goOn(this)" class="btn btn-default">Ubah</button>
@endsection

@section("script")
<script>
    let modal, modalId, modalFooter, link;
    const showModal = e => {
        link = e.getAttribute("data-href");
        modalId = e.getAttribute("data-target");
        modal = document.querySelector(modalId);
        modalFooter = modal.querySelector(".modal-footer");
    };

    const goOn = e => {
        window.location.href = link;
    };
</script>
@endsection