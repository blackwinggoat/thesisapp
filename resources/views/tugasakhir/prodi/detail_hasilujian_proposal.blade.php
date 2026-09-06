@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Jadwal Ujian</li>
        </ol>
        @if (session('status') === 'warning')
            <div class="alert alert-warning alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p>{{ session('message') }}</p>
            </div>
        @endif
        <div class="the-box">
            <div class="form-group">
                <label class="col-lg-2 control-label">Tanggal Ujian</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control bold-border" name="jadwal" value="{{$info->tgl_ujian}}"
                        disabled>
                </div>
            </div>
            <br><br>
            <div class="form-group">
                <label class="col-lg-2 control-label">Jumlah Peserta</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control datepicker bold-border" name="jml_peserta"
                        value="{{$info->jml_peserta}}" disabled>
                </div>
            </div>
            <br><br>
            <div class="form-group">
                <label class="col-lg-2 control-label">Tipe Ujian</label>
                <div class="col-lg-6">
                    @php
                    if($info->tipe_ujian == 0):
                    $tipe = "Proposal";
                    elseif($info->tipe_ujian == 2):
                    $tipe = "Ujian Meja";
                    endif;
                    @endphp
                    <input type="text" class="form-control bold-border" value="{{$tipe}}" name="jml_peserta" disabled>
                </div>
            </div>
            <br>
            <br>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nim</th>
                            <th>Nama Mahasiswa</th>
                            <th>Pembimbing Utama</th>
                            <th>Pembimbing Pendamping</th>
                            <th>Penguji I</th>
                            <th>Penguji II</th>
                            <th>Penguji III</th>
                            <th>Ketua Sidang</th>
                            <th>{{ !empty($isHistory) ? 'Keterangan' : 'Approve' }}</th>
                            <th>Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $i => $d)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$i}}</td>
                            <td>{{$d->C_NPM}}</td>
                            <td>{{$d->NAMA_MAHASISWA}}</td>
                            @include('tugasakhir.prodi.partials.hasil_ujian_penilai_cells')
                            <td>
                                @if (!empty($isHistory))
                                    <span class="label label-success">Nilai Mahasiswa Telah Diapprove Oleh Prodi</span>
                                @else
                                    @if (empty($d->penguji_I_id) && empty($d->penguji_II_id) && empty($d->penguji_III_id) && empty($d->ketua_sidang_id))
                                    Silahkan Set Penguji dan Ketua Sidang
                                    @else
                                    @if ((string) $d->status_bimbingan === '0')
                                        @if (helper::isPenilaianLengkapByRegId($d->reg_id))
                                            @if (($d->status_tolak_proposal ?? '') == 0)
                                                <button  onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                                                data-href="{{url('/prodi/approve_hasilujian_proposal_post/')}}/{{$d->bimbingan_id}}/{{$d->C_NPM}}/{{$d->pendaftaran_id}}"
                                                class="btn btn-primary visible-lg-inline">Terima
                                                </button>
                                                <button onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal"
                                                data-href="{{url('/prodi/tolak_hasilujian_proposal_post/')}}/{{$d->bimbingan_id}}/{{$d->C_NPM}}/{{$d->pendaftaran_id}}"
                                                class="btn btn-danger visible-lg-inline">Tolak
                                                </button>
                                            @else
                                                <button class="btn btn-primary disabled">Anda Ditolak
                                                </button>
                                            @endif
                                        @else
                                            <button  onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                                            data-href=""
                                            class="btn btn-primary disabled visible-lg-inline">Terima
                                            </button>
                                            <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                                            data-href=""
                                            class="btn btn-danger visible-lg-inline disabled">Tolak
                                            </button>
                                        @endif
                                    @else
                                        <button
                                        class="btn btn-secondary" disabled>Telah diterima
                                        </button>
                                    @endif
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if (empty($d->penguji_I_id) && empty($d->penguji_II_id) && empty($d->penguji_III_id) && empty($d->ketua_sidang_id))
                                Silahkan Set Penguji dan Ketua Sidang
                                @else
                                    @if (helper::isPenilaianLengkapByRegId($d->reg_id))
                                        <a href="{{url('prodi/lembaran_hasilujian_proposal')}}/{{$d->pendaftaran_id}}/{{$d->C_NPM}}/{{$d->reg_id}}" class="btn btn-info" target="_blank"><i class="fa fa-paperclip"></i></a>
                                    @else
                                        <span>Penilaian Belum Lengkap</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div>
    </div>
</div>
@endsection

{{--ModalSelesai--}}
@section("modalWarningTitle")
Selesai
@endsection
@section("modalWarningBody")
Apakah Anda yakin selesai mengkonfirmasi dokumen?
@endsection
@section("modalWarningFooter")
<button onclick="goOn(this)" class="btn btn-default">Selesai</button>
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

{{--ModalTerima--}}
@section("modalPrimaryTitle")
Terima
@endsection
@section("modalPrimaryBody")
Apakah Anda yakin ingin approve hasil ujian ?
@endsection
@section("modalPrimaryFooter")
<button onclick="goOn(this)" class="btn btn-default">Terima</button>
@endsection

{{--ModalTolak--}}
@section("modalDangerTitle")
Tolak
@endsection
@section("modalDangerBody")
Apakah Anda yakin ingin menolak mahasiswa ini ?
@endsection
@section("modalDangerFooter")
<button onclick="goOn(this)" class="btn btn-default">Tolak</button>
@endsection

@section("script")
<script>
    //Modal
    let modal, modalId, modalFooter, link, form, formaction, sui;

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

            const submit = () => {
            form = document.querySelector(`form[action="${formaction}"]`);
            form.submit();
        };

</script>
@endsection
