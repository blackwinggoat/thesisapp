@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
            <!-- End page heading -->

            <!-- Begin breadcrumb -->
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="index.html"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/')}}">Home</a></li>
                <li class="active">Usulan Pembimbing</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Daftar Calon Mahasiswa Bimbingan</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Topik Usulan</th>
                            <th>Bidang Ilmu</th>
                            <th>Kerangka Pikir</th>
                            <th>Note</th>
                            <th>Pembimbing Utama</th>
                            <th>Pembimbing Pendamping</th>
                            <th>Pilihan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $value)
                            @php
                                $request_pembimbing = \App\RequestPembimbing::where("topik", $value->topik_id)->get();
                                $pembimbingI = Illuminate\Support\Facades\DB::table("t_mst_dosen")->where("C_KODE_DOSEN",$value->pembimbing_I_id)->first();
                                $pembimbingII = Illuminate\Support\Facades\DB::table("t_mst_dosen")->where("C_KODE_DOSEN",$value->pembimbing_II_id)->first();
                            @endphp
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->C_NPM}}</td>
                                <td>{{$value->NAMA_MAHASISWA}}</td>
                                <td>{{$value->topik}}</td>
                                <td>
                                    @foreach ($request_pembimbing as $key => $val)
                                        @php
                                            $mst_bidang_ilmu = \App\Model\mst_bidangilmu::find($val->bidang_ilmu);
                                        @endphp
                                        {{++$key}}. {{$mst_bidang_ilmu->bidang_ilmu}}<br>
                                    @endforeach
                                </td>
                                <td><button class="btn btn-primary" onclick="showModal(this)" data-target="#modalInfo" data-toggle="modal" data-href="{{asset('dokumen/'.$value->kerangka)}}"><i class="fa fa-paperclip"></i></button></td>
                                <td>
                                    <a class="btn btn-info" href="{{url('dsn/detail_note')}}/{{$value->topik_id}}"><i class="fa fa-newspaper-o"></i></a>    
                                </td>
                                <td>
                                    @if ($pembimbingI != null)
                                        {{$pembimbingI->NAMA_DOSEN}}<br>
                                        @if($value->pembimbing_I_status == "0")
                                            <span class="label label-danger">
                                                Ditolak
                                            </span>
                                        @elseif($value->pembimbing_I_status == "1")
                                            <span class="label label-success">
                                                Diterima
                                            </span>
                                        @else
                                            <span class="label label-warning">
                                                Menunggu...
                                            </span>
                                        @endif
                                    @else
                                            Pembimbing Utama Belum dipilih
                                    @endif
                                </td>
                                <td>
                                    @if ($pembimbingII != null)
                                        {{$pembimbingII->NAMA_DOSEN}}<br>
                                        @if($value->pembimbing_II_status == "0")
                                            <span class="label label-danger">
                                                Ditolak
                                            </span>
                                        @elseif($value->pembimbing_II_status == "1")
                                            <span class="label label-success">
                                                Diterima
                                            </span>
                                        @else
                                            <span class="label label-warning">
                                                Menunggu...
                                            </span>
                                        @endif
                                    @else  
                                        Pembimbing Pendamping Belum Dipilih
                                    @endif
                                </td>
                                <td>
                                    @if(auth()->user()->name == $value->pembimbing_I_id)
                                        @if($value->pembimbing_I_status == "2")
                                            <button class="btn btn-default" onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/1/$value->C_NPM")}}">Terima</button>
                                            <button class="btn btn-default" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/0/$value->C_NPM")}}">Tolak</button>
                                        @elseif($value->pembimbing_I_status == "1")
                                            <button class="btn btn-primary" onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/1/$value->C_NPM")}}">Terima</button>
                                            <button class="btn btn-default" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/0/$value->C_NPM")}}">Tolak</button>
                                        @elseif($value->pembimbing_I_status == "0")
                                            <button class="btn btn-default" onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/1/$value->C_NPM")}}">Terima</button>
                                            <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/0/$value->C_NPM")}}">Tolak</button>
                                        @endif
                                    @elseif(auth()->user()->name == $value->pembimbing_II_id)
                                        @if($value->pembimbing_II_status == "2")
                                            <button class="btn btn-default" onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/1/$value->C_NPM")}}">Terima</button>
                                            <button class="btn btn-default" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/0/$value->C_NPM")}}">Tolak</button>
                                        @elseif($value->pembimbing_II_status == "1")
                                            <button class="btn btn-primary" onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/1/$value->C_NPM")}}">Terima</button>
                                            <button class="btn btn-default" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/0/$value->C_NPM")}}">Tolak</button>
                                        @elseif($value->pembimbing_II_status == "0")
                                            <button class="btn btn-default" onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/1/$value->C_NPM")}}">Terima</button>
                                            <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{url("/dsn/request_konfirmasi/0/$value->C_NPM")}}">Tolak</button>
                                        @endif
                                    @endif
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

{{--ModalTerima--}}
@section("modalPrimaryTitle")
    Terima Usulan
@endsection
@section("modalPrimaryBody")
    Apakah Anda yakin ingin menerima usulan?
@endsection
@section("modalPrimaryFooter")
    <button onclick="goOn(this)" class="btn btn-default">Terima</button>
@endsection

{{--ModalTolak--}}
@section("modalDangerTitle")
    Tolak Usulan
@endsection
@section("modalDangerBody")
    Apakah Anda yakin ingin menolak usulan?
@endsection
@section("modalDangerFooter")
    <button onclick="goOn(this)" class="btn btn-default">Tolak</button>
@endsection

{{--ModalDownload--}}
@section("modalInfoTitle")
    Download Kerangka Pikir
@endsection
@section("modalInfoBody")
    Apakah Anda yakin ingin men-download kerangka pikir?
@endsection
@section("modalInfoFooter")
    <button onclick="goOn(this)" class="btn btn-default">Download</button>
@endsection

@section("script")
    <script>
        let modal, modalId, modalFooter, link, form, formaction;

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
    </script>
@endsection