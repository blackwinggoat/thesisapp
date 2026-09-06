@extends('tugasakhir.index')
@section('isi')
@include('tugasakhir.mhs.partials.exam_document_table_styles')
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
            <li class="active">Daftar Ujian</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">
            Daftar Periode Pendaftaran
            @if(!empty($studentProgramLabel))
            <small><span class="label label-info">{{$studentProgramLabel}}</span></small>
            @endif
        </h3>
        @if(empty($studentProgramLabel))
        <div class="alert alert-danger">
            Program studi akun mahasiswa belum dikenali. Silakan hubungi Program Studi.
        </div>
        @elseif(session('registration_status') == 'invalid_period')
        <div class="alert alert-danger">
            Periode pendaftaran tidak tersedia untuk program studi Anda.
        </div>
        @elseif(session('registration_status') == 'program_unmapped')
        <div class="alert alert-danger">
            Program studi akun mahasiswa belum dikenali. Silakan hubungi Program Studi.
        </div>
        @elseif(session('registration_status') == 'registration_error')
        <div class="alert alert-danger">
            Pendaftaran belum berhasil diproses. Silakan coba kembali.
        </div>
        @endif
        @if (session('registration_status') == 'cancel_success')
        <div class="alert alert-success alert-block square fade in alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p><strong>Status!</strong></p>
            <p>Pendaftaran ujian meja berhasil dibatalkan.</p>
        </div>
        @elseif(session('registration_status') == 'cancel_scheduled')
        <div class="alert alert-warning alert-block square fade in alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p><strong>Status!</strong></p>
            <p>Pendaftaran tidak dapat dibatalkan karena jadwal ujian sudah dibuat.</p>
        </div>
        @elseif(session('registration_status') == 'cancel_not_found')
        <div class="alert alert-warning alert-block square fade in alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p><strong>Status!</strong></p>
            <p>Data pendaftaran ujian meja tidak ditemukan.</p>
        </div>
        @elseif(session('registration_status') == 'cancel_error')
        <div class="alert alert-danger alert-block square fade in alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p><strong>Status!</strong></p>
            <p>Pendaftaran gagal dibatalkan. Silakan coba kembali.</p>
        </div>
        @endif
        <form method="post" action="{{url('mhs/registrasi')}}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="tipe_ujian" value="2">
            <div class="the-box">
                <div class="table-responsive">

                    <table class="table table-th-block">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Nama Periode</th>
                                <th>Jadwal Periode</th>
                                <th>Kuota</th>
                                <th>Jumlah Peserta</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $registeredPeriodId = !empty($currentRegistration) ? $currentRegistration->pendaftaran_id : null;
                            @endphp
                            @forelse ($data as $key => $value)
                            @php
                            $isRegisteredPeriod = !empty($registeredPeriodId) && (string) $registeredPeriodId === (string) $value->pendaftaran_id;
                            @endphp
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->nama_periode}}</td>
                                <td>{{$value->tgl_start}} - {{$value->tgl_end}}</td>
                                <td>{{$value->kuota}}</td>
                                <td>{{$value->jml_peserta}}</td>
                                <td>
                                    <div class="btn-group">
                                        @if($isRegisteredPeriod)
                                            <button class="btn btn-success btn-perspective" type="button" disabled>
                                                <i class="fa fa-check"></i> Terdaftar
                                            </button>
                                            @if(!$currentRegistrationScheduled)
                                            <button type="button" class="btn btn-danger btn-perspective"
                                                onclick="showModal(this)" data-target="#modalCancelRegistration"
                                                data-toggle="modal"
                                                data-href="{{url('mhs/signup_ujianmeja/batalkan/'.$value->pendaftaran_id)}}">
                                                Batalkan
                                            </button>
                                            @else
                                            <button class="btn btn-default btn-perspective" type="button" disabled>
                                                Sudah Dijadwalkan
                                            </button>
                                            @endif
                                        @elseif(!empty($registeredPeriodId))
                                            <span class="text-muted">-</span>
                                        @elseif($mstsyaratujian == $trtsyaratujian && !empty($mstsyaratujian) &&
                                        $value->jml_peserta < $value->kuota)
                                            <button class="btn btn-primary btn-perspective" type="submit"
                                                name="pendaftaran_id" value="{{$value->pendaftaran_id}}">Daftar</button>
                                        @else
                                            <button class="btn btn-primary btn-perspective" type="button"
                                                disabled>Daftar</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada periode pendaftaran yang tersedia.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
        </form>
        <!-- END DATA TABLE -->


        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Persyaratan Ujian</h3>
        @if (session('status') == 'success')
        <div class="alert alert-success alert-block square fade in alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p><strong>Status!</strong></p>
            <p>Catatan Berhasil Ditambahkan<i class="fa fa-smile-o"></i></p>
        </div>
        @elseif(session('status') == 'error')
        <div class="alert alert-danger alert-block square fade in alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <p><strong>Status!</strong></p>
            <p>Data Gagal Ditambahkan, Konten Masih Kosong!<i class="fa fa-smile-o"></i></p>
        </div>
        @endif
        @if(session('document_status') == 'success')
        <div class="alert alert-success alert-block square fade in alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{session('document_message')}}
        </div>
        @endif
        @if($errors->has('document_links'))
        <div class="alert alert-danger alert-block square fade in alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{$errors->first('document_links')}}
        </div>
        @endif
        <div class="the-box">
            <div class="table-responsive">
                <form action="{{url("mhs/syarat_ujianpost_all")}}" method="post">
                    {{csrf_field()}}
                    <input type="hidden" name="tipe_ujian" value="2">
                    <table class="table table-striped table-hover exam-requirements-table" id="">
                        <thead class="the-box dark full">
                            <tr>
                                <th class="document-number-column document-compact-column">No</th>
                                <th class="document-name-column">Nama Dokumen</th>
                                <th class="document-link-column">Link Dokumen</th>
                                <th class="document-status-column document-compact-column">Status</th>
                                <th class="document-action-column document-compact-column">Aksi</th>
                                <th class="document-note-column document-compact-column">Catatan</th>
                                <th class="document-file-column document-compact-column">File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($syarat as $key => $value)
                            @php $trtsyaratujian = $submittedRequirements->get($value->syarat_ujian_id); @endphp
                            <tr class="odd gradeX">
                                <td class="document-number-column document-compact-column">{{$loop->iteration}}</td>
                                <td class="document-name-column">{{$value->nama_syarat}}</td>
                                <td class="document-link-column">
                                    <input type="hidden" name="syarat_ujian_id[]"
                                        value="{{$value->syarat_ujian_id}}" />
                                    <input type="text" class="form-control bold-border document-link-input" name="link[]"
                                        value="{{old('link.'.$key, empty($trtsyaratujian) ? '' : $trtsyaratujian->link)}}" />
                                </td>
                                <td class="document-status-column document-compact-column">
                                    @if(!empty($trtsyaratujian))
                                    @if($trtsyaratujian->status == "0")
                                    <span class="badge badge-danger">ditolak</span>
                                    @elseif($trtsyaratujian->status == "1")
                                    <span class="badge badge-primary">diterima</span>
                                    @elseif($trtsyaratujian->status == "2")
                                    <span class="badge badge-warning">menunggu</span>
                                    @endif
                                    @endif
                                </td>
                                <td class="document-action-column document-compact-column">
                                    @if(!empty($trtsyaratujian))
                                    <button type="button" onclick="showModal(this)"
                                        data-href="{{ url("mhs/syarat_ujiandel/2/$value->syarat_ujian_id")}}"
                                        data-target="#modalDanger" data-toggle="modal" class="btn btn-danger"
                                        title="Hapus link"><i
                                            class="fa fa-trash"></i></button>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="document-note-column document-compact-column">
                                    @if(!empty($trtsyaratujian))
                                    <a class="btn btn-info"
                                        href="{{url('mhs/signup_ujianmeja/catatan')}}/{{$trtsyaratujian->id}}"><i
                                            class="fa fa-newspaper-o"></i></a>
                                    @endif

                                </td>
                                <td class="document-file-column document-compact-column">
                                    @if(!empty($trtsyaratujian))
                                    <button type="button" onclick="showModal(this)"
                                        data-href="{{$trtsyaratujian->link}}" data-target="#modalDefault"
                                        data-toggle="modal" class="btn bg-dark" style="color: #fff"><i
                                            class="fa fa-paperclip"></i></button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="exam-document-save-toolbar">
                        <span>*Gunakan http/https untuk link dokumen</span>
                        <button type="submit" class="btn btn-primary btn-perspective">
                            <i class="fa fa-save"></i> Simpan Semua Persyaratan
                        </button>
                    </div>
                </form>
            </div><!-- /.table-responsive -->
            <div style="display: flex">
                @php
                $trtsyaratujianx = $submittedRequirements->count();
                $trtpengajuandokumen = \App\TrtPengajuanDokumen::where(["C_NPM" => auth()->user()->name, "type" =>
                2])->count();
                @endphp
                @if(count($syarat) == $trtsyaratujianx && count($syarat) != 0)
                @if($trtpengajuandokumen == 0)
                <button data-href="{{url("/mhs/ajukan_dokumen/2")}}" onclick="showModal(this)"
                    data-target="#modalWarning" data-toggle="modal" class="btn btn-warning btn-perspective"
                    style="margin-left: auto">Ajukan</button>
                @else
                <button data-href="{{url("/mhs/ajukan_dokumen/2")}}" onclick="showModal(this)"
                    data-target="#modalDanger1" data-toggle="modal" class="btn btn-danger btn-perspective"
                    style="margin-left: auto">Batalkan ajuan</button>
                @endif
                @else
                <button class="btn btn-warning btn-perspective" disabled style="margin-left: auto">Ajukan</button>
                @endif
            </div>
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->
</div>

<div class="modal fade" id="modalDanger1" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-no-shadow modal-no-border bg-danger">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Batalkan Ajuan</h4>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin membatalkan ajuan?
            </div>
            <div class="modal-footer">
                <button onclick="goOn(this)" class="btn btn-default">Batalkan</button>
            </div><!-- /.modal-footer -->
        </div><!-- /.modal-content .modal-no-shadow .modal-no-border .the-box .info .full -->
    </div><!-- /.modal-dialog -->
</div><!-- /#InfoModalColor -->

<div class="modal fade" id="modalCancelRegistration" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-no-shadow modal-no-border bg-danger">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Batalkan Pendaftaran</h4>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin keluar dari periode ujian meja ini?
            </div>
            <div class="modal-footer">
                <button onclick="goOn(this)" class="btn btn-default">Batalkan Pendaftaran</button>
            </div>
        </div>
    </div>
</div>

@endsection

{{--ModalAjukan--}}
@section("modalWarningTitle")
Ajukan
@endsection
@section("modalWarningBody")
Apakah Anda yakin ingin mengajukan dokumen?
@endsection
@section("modalWarningFooter")
<button onclick="goOn(this)" class="btn btn-default">Ajukan</button>
@endsection

{{--ModalDownload--}}
@section("modalDefaultTitle")
Download Lampiran
@endsection
@section("modalDefaultBody")
Apakah Anda yakin ingin men-download lampiran?
@endsection
@section("modalDefaultFooter")
<button onclick="goOnNewTab(this)" class="btn btn-primary">Download</button>
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
    //Modal
    let modal, modalId, modalFooter, link;

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

    const goOnNewTab = () => {
        modal.querySelector(".modal-backdrop").click();
        window.open(link);
    };

</script>
@endsection
