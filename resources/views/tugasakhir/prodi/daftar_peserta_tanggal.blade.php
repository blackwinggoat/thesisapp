@extends('tugasakhir.index')
@section('isi')
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/prodi/jadwal') }}">Periode Ujian</a></li>
            <li class="active">Peserta per Tanggal</li>
        </ol>

        @php
            $showProdiColumn = !in_array(Auth::user()->name, ['proditi', 'prodisi', 'akademikproditi', 'akademikprodisi']);
        @endphp

        <h3 class="page-heading">Daftar Peserta Ujian Gabungan</h3>
        <div class="the-box">
            <div class="row">
                <div class="col-sm-4">
                    <strong>Tanggal Ujian</strong>
                    <div>{{$info->tanggal_label}}</div>
                </div>
                <div class="col-sm-4">
                    <strong>Tipe Ujian</strong>
                    <div>
                        @foreach($info->tipe_ujian_list as $tipe)
                            <span class="label {{$tipe->kode === 0 ? 'label-info' : ($tipe->kode === 2 ? 'label-danger' : 'label-default')}}" style="display: inline-block; margin: 4px 4px 0 0;">
                                {{$tipe->label}}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-4">
                    <strong>Jumlah Peserta</strong>
                    <div>{{$info->jumlah_peserta}} mahasiswa</div>
                </div>
            </div>

            <hr>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            @if($showProdiColumn)
                            <th>Program Studi</th>
                            @endif
                            <th>Periode</th>
                            <th>Tipe Ujian</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Pembimbing Utama</th>
                            <th>Pembimbing Pendamping</th>
                            <th>Penguji I</th>
                            <th>Penguji II</th>
                            <th>Penguji III</th>
                            <th>Ketua Sidang</th>
                            <th>Set Penguji</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $d)
                        <tr>
                            <td class="text-center">{{$i + 1}}</td>
                            @if($showProdiColumn)
                            <td>{{$d->prodi_label}}</td>
                            @endif
                            <td>{{$d->nama_periode}}</td>
                            <td><span class="label {{$d->tipe_ujian == 0 ? 'label-info' : ($d->tipe_ujian == 2 ? 'label-danger' : 'label-default')}}">{{$d->tipe_ujian_label}}</span></td>
                            <td>{{$d->C_NPM}}</td>
                            <td>{{$d->NAMA_MAHASISWA}}</td>
                            <td>{{$dosenByKode->get($d->pembimbing_I_id, '--')}}</td>
                            <td>{{$dosenByKode->get($d->pembimbing_II_id, '--')}}</td>
                            <td>{{$dosenByKode->get($d->penguji_I_id, '--')}}</td>
                            <td>{{$dosenByKode->get($d->penguji_II_id, '--')}}</td>
                            <td>{{$dosenByKode->get($d->penguji_III_id, '--')}}</td>
                            <td>{{$dosenByKode->get($d->ketua_sidang_id, '--')}}</td>
                            <td class="text-center">
                                <a class="btn btn-primary btn-xs" href="{{ url('prodi/set_penguji/'.$d->pendaftaran_id.'/'.$d->C_NPM.'/'.$d->tipe_ujian) }}?kembali_tanggal={{ urlencode($info->tgl_ujian) }}" title="Set penguji">
                                    <i class="fa fa-user-plus"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{$showProdiColumn ? 13 : 12}}" class="text-center">Belum ada peserta pada jadwal ujian tanggal ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
