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
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Peserta ujian</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Daftar Peserta Ujian</h3>
        <div class="the-box">
            <div class="form-group">
                <label class="col-lg-2 control-label">Jumlah Peserta</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control datepicker bold-border" name="jml_peserta"
                        value="{{$info[0]->jml_peserta}}" disabled>
                </div>
            </div>
            <br><br>
            <div class="form-group">
                <label class="col-lg-2 control-label">Tipe Ujian</label>
                <div class="col-lg-6">
                    @php
                    if($info[0]->tipe_ujian == 0):
                    $tipe = "Proposal";
                    elseif($info[0]->tipe_ujian == 2):
                    $tipe = "Ujian Meja";
                    endif;
                    @endphp
                    <input type="text" class="form-control bold-border" value="{{$tipe}}" name="jml_peserta" disabled>
                </div>
            </div>
            <br><br>
            <form action="{{url('prodi/ubah_periode_pendaftaran/')}}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Nim</th>
                                <th>Nama Mahasiswa</th>
                                <th>Topik</th>
                                <th>Pembimbing Utama</th>
                                <th>Pembimbing Pendamping</th>
                                <th>Pindah Periode</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($data as $i => $d)
                            <tr class="odd gradeX">
                                <input type="hidden" name="C_NPM[]" value="{{$d->C_NPM}}">
                                <td width="1%" align="center">{{++$i}}</td>
                                <td>{{$d->C_NPM}}</td>
                                <td>{{$d->NAMA_MAHASISWA}}</td>
                                <td>{{$d->judul}}</td>
                                @php
                                $pembimbing1 = \App\Dosen::where("C_KODE_DOSEN",$d->pembimbing_I_id)->first();
                                $pembimbing2 = \App\Dosen::where("C_KODE_DOSEN",$d->pembimbing_II_id)->first();
                                @endphp
                                <td>{{$pembimbing1->NAMA_DOSEN}}</td>
                                <td>{{$pembimbing2->NAMA_DOSEN}}</td>
                                <input type="hidden" name="status_ujian" value="{{$d->status_ujian}}">
                                <input type="hidden" name="tipe_ujian" value="{{$info[0]->tipe_ujian}}">
                                <td>
                                    <select class="form-control" name="pindah_periode[]" id="">
                                        <option value="{{$d->pendaftaran_id}}">Periode Yang Sama</option>
                                        @foreach (helper::getPeriodePendaftaranByStatusUjian($d->status_ujian, $info[0]->tipe_ujian) as $item)
                                            <option value="{{$item->pendaftaran_id}}">{{$item->nama_periode}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <a class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger"
                                        data-toggle="modal" data-href="{{route('hapusJadwalUjianPerMahasiswa', ['C_NPM' => $d->C_NPM, 'pendaftaran_id' => $d->pendaftaran_id])}}"><i
                                            class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <input type="submit" value="Ubah Periode" class="btn btn-info">
            </form>
        </div>
    </div><!-- /.container-fluid -->
</div>
@endsection
{{--ModalHapus--}}
@section("modalDangerTitle")
Hapus
@endsection
@section("modalDangerBody")
Apakah Anda yakin ingin menghapus data mahasiswa ini ?
@endsection
@section("modalDangerFooter")
<button onclick="goOn(this)" class="btn btn-default">Hapus</button>
@endsection


@section("script")
<script>
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

</script>
@endsection