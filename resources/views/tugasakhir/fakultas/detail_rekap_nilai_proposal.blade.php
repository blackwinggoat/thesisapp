@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Jadwal Ujian Per Mahasiswa</li>
        </ol>
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
                            <th>Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $i => $d)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$i}}</td>
                            <td>{{$d->C_NPM}}</td>
                            <td>{{$d->NAMA_MAHASISWA}}</td>
                            @php
                            $pembimbing1 = \App\Dosen::where("C_KODE_DOSEN",$d->pembimbing_I_id)->first();
                            $pembimbing2 = \App\Dosen::where("C_KODE_DOSEN",$d->pembimbing_II_id)->first();
                            $penguji1 = \App\Dosen::where("C_KODE_DOSEN",$d->penguji_I_id)->first();
                            $penguji2 = \App\Dosen::where("C_KODE_DOSEN",$d->penguji_II_id)->first();
                            $penguji3 = \App\Dosen::where("C_KODE_DOSEN",$d->penguji_III_id)->first();
                            $ketuasidang = \App\Dosen::where("C_KODE_DOSEN",$d->ketua_sidang_id)->first();
                            @endphp
                            <td>{{$pembimbing1->NAMA_DOSEN}}<?= helper::getStatusPenilaianPerDosen($d->pembimbing_I_id, $d->reg_id) ?></td>
                            <td>{{$pembimbing2->NAMA_DOSEN}}<?= helper::getStatusPenilaianPerDosen($d->pembimbing_II_id, $d->reg_id) ?></td>
                            <td>
                                @if ($penguji1 == null)
                                {{"-"}}
                                @else
                                {{$penguji1->NAMA_DOSEN}}
                                <?= helper::getStatusPenilaianPerDosen($d->penguji_I_id, $d->reg_id) ?>
                                @endif
                            </td>
                            <td>
                                @if ($penguji2 == null)
                                {{"-"}}
                                @else
                                {{$penguji2->NAMA_DOSEN}}
                                <?= helper::getStatusPenilaianPerDosen($d->penguji_II_id, $d->reg_id) ?>
                                @endif
                            </td>
                            <td>
                                @if ($penguji3 == null)
                                {{"-"}}
                                @else
                                {{$penguji3->NAMA_DOSEN}}
                                <?= helper::getStatusPenilaianPerDosen($d->penguji_III_id, $d->reg_id) ?>
                                @endif
                            </td>
                            <td>
                                @if ($ketuasidang == null)
                                {{"-"}}
                                @else
                                {{$ketuasidang->NAMA_DOSEN}}
                                <?= helper::getStatusPenilaianPerDosen($d->ketua_sidang_id, $d->reg_id) ?>
                                @endif
                            </td>
                            <td>
                                @if ($penguji1 == null && $penguji2 == null && $penguji3 == null && $ketuasidang ==
                                null)
                                Silahkan Set Penguji dan Ketua Sidang
                                @else
                                        <a href="{{url('fakultas/lembaran_hasilujian_proposal/')}}/{{$d->pendaftaran_id}}/{{$d->C_NPM}}/{{$d->reg_id}}" class="btn btn-info" target="_blank"><i class="fa fa-paperclip"></i></a>
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
Apakah Anda yakin ingin menolak pengajuan dokumen?
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
