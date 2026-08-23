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
            <li class="active">History Detail Rekap Nilai Proposal</li>
        </ol>
        <div style="display: flex; justify-content: flex-start; align-items: center; gap: 12px; margin-bottom: 8px;">
            <a href="{{ url('dsn/rekap_nilai_proposal_history') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Kembali ke History
            </a>
        </div>
        <div class="the-box">
            <div class="form-group">
                <label class="col-lg-2 control-label">Tanggal Ujian</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control bold-border" name="jadwal" value="{{ $info->tgl_ujian }}"
                        disabled>
                </div>
            </div>
            <br><br>
            <div class="form-group">
                <label class="col-lg-2 control-label">Jumlah Peserta</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control datepicker bold-border" name="jml_peserta"
                        value="{{ $info->jml_peserta }}" disabled>
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
                    <input type="text" class="form-control bold-border" value="{{ $tipe }}" name="jml_peserta" disabled>
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
                            <td width="1%" align="center">{{ ++$i }}</td>
                            <td>{{ $d->C_NPM }}</td>
                            <td>{{ $d->NAMA_MAHASISWA }}</td>
                            @include('tugasakhir.dosen.partials.rekap_penilai_cells')
                            <td>
                                @if (empty($d->penguji_I_id) && empty($d->penguji_II_id) && empty($d->penguji_III_id) && empty($d->ketua_sidang_id))
                                Silahkan Set Penguji dan Ketua Sidang
                                @else
                                        <a href="{{ url('dsn/detail_ujian') }}/{{ $d->C_NPM }}/0" class="btn btn-info" target="_blank"><i class="fa fa-paperclip"></i></a>
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
