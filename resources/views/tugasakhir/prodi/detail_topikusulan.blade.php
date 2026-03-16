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
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/prodi/topik') }}">Topik Usulan</a></li>
                <li class="active">Detail Topik Usulan</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Data Pengusul</h3>
            <div class="the-box">
                <br>
                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">NIM</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="nim"
                                value="{{ $data->C_NPM }}" disabled />
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Nama</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="nama"
                                value="{{ $data->NAMA_MAHASISWA }}" disabled />
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Bidang Ilmu Peminatan</label>
                        <div class="col-lg-5">
                            <input value="{{ $data_usulan[0]->bidang_ilmu_peminatan }}" type="text"
                                class="form-control bold-border" name="jml_sks" disabled />
                        </div>
                    </div>
                    <br><br>
                    {{-- <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Jumlah SKS</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="jml_sks" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Semester</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="semester" disabled/>
                        </div>
                    </div>
                    <br><br> --}}
                </fieldset>
            </div><!-- /.the-box -->
            <!-- END DATA TABLE -->


            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Usulan Topik</h3>
            <div class="the-box">
                <br>
                <div class="table-responsive">
                    <form method="post" action="{{ url('prodi/topik') }}">
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control bold-border" name="C_NPM" value="{{ $data->C_NPM }}" />
                        <table class="table table-striped table-hover">
                            <thead class="the-box dark full">
                                <tr>
                                    <th>No</th>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Topik Usulan</th>
                                    <th>Bidang Ilmu</th>
                                    <th>Kerangka Pikir</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                    <th>Konfirmasi</th>
                                    <th>Tolak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_usulan as $key => $value)
                                    <tr class="odd gradeX">

                                        <td width="1%" align="center">{{ ++$key }}</td>
                                        <td>{{ $value->C_NPM }}</td>
                                        <td>{{ $value->NAMA_MAHASISWA }}</td>
                                        <th>{{ $value->topik }}</th>
                                        <th>
                                            @php
                                                $request_pembimbing = \App\RequestPembimbing::where('topik', $value->topik_id)->get();
                                            @endphp
                                            @foreach ($request_pembimbing as $key => $val)
                                                @php
                                                    $mst_bidang_ilmu = \App\Model\mst_bidangilmu::find($val->bidang_ilmu);
                                                @endphp
                                                {{ $key++ + 1 }}. {{ $mst_bidang_ilmu->bidang_ilmu }}<br>
                                            @endforeach
                                        </th>
                                        <td>
                                            <button type="button" class="btn btn-primary" onclick="showModal(this)"
                                                data-href="{{ asset('dokumen/' . $value->kerangka) }}"
                                                data-target="#modalInfo" data-toggle="modal"><i class="fa fa-paperclip"></i>
                                            </button>
                                        </td>
                                        <td>
                                            @if ($value->status == 0)
                                                <span class="badge badge-danger">Belum Dikonfirmasi</span>
                                            @elseif($value->status == 1)
                                                <span class="badge badge-success">Diterima</span>
                                            @else
                                                <span class="badge badge-danger">Ditolak</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-info"
                                                href="{{ url('prodi/detail_note') }}/{{ $value->topik_id }}"><i
                                                    class="fa fa-newspaper-o"></i></a>
                                        </td>
                                        <th class="text-center">
                                            <label>
                                                <input type="radio" value="{{ $value->topik_id }}" class="i-grey-flat"
                                                    name="topik_id">
                                            </label>
                                        </th>
                                        {{-- tolak and showModal --}}
                                        <td>
                                            <button type="button" class="btn btn-danger" onclick="showModal(this)"
                                                data-href="{{ url('prodi/tolak_topik_penelitian') }}/{{ $value->topik_id }}"
                                                data-target="#modalDanger" data-toggle="modal"><i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p align="right">
                            <button id="tombol_satu" class="btn btn-danger btn-perspective" type="button"
                                onclick="showPostModal(this)" data-formaction="{{ url('prodi/topik') }}"
                                data-target="#modalPrimary" data-toggle="modal">Terima Topik
                            </button>
                        </p>
                    </form>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div>
@endsection


{{-- Modal Tolak --}}
@section('modalDangerTitle')
    Tolak Topik
@endsection
@section('modalDangerBody')
    Apakah Anda yakin ingin menolak topik?
@endsection
@section('modalDangerFooter')
    <button onclick="goOn(this)" class="btn btn-default">Tolak</button>
@endsection

{{-- ModalDownload --}}
@section('modalInfoTitle')
    Download
@endsection
@section('modalInfoBody')
    Download kerangka pikir?
@endsection
@section('modalInfoFooter')
    <button onclick="goOn(this)" class="btn btn-default">Download</button>
@endsection

{{-- ModalTerima --}}
@section('modalPrimaryTitle')
    Terima Topik
@endsection
@section('modalPrimaryBody')
    Apakah Anda yakin ingin menerima topik?
    <br>
    <span id="status" class="badge badge-danger"></span>
@endsection
@section('modalPrimaryFooter')
    <button id="tombol_dua" onclick="submit(this)" class="btn btn-default">Terima</button>
@endsection

@section('script')
    <script>
        $('#tombol_satu').on('click', function() {
            console.log("Selamat Datang di Bagian Satu");


            if (!$("input[name='topik_id']").is(':checked')) {
                console.log("Ini Bagian Satu");
                $('#tombol_dua').attr("disabled", "disabled");
                $('#status').html("Belum Ada Topik yang Ingin dikonfirmasi");
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

        const goOn = e => {
            modal.querySelector(".modal-backdrop").click();
            window.location.href = link;
        };

        const submit = e => {
            form = document.querySelector(`form[action="${formaction}"]`);
            form.submit();
        }
    </script>
@endsection
