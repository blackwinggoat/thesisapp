@extends('tugasakhir.index')
@section('isi')

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Pengajuan Topik</li>
        </ol>
        <h3 class="page-heading">Riwayat Ujian</h3>
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                    <tr>
                        <th>No</th>
                        <th>Tipe Ujian</th>
                        <th>Detail Ujian</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $key => $value)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$key}}</td>
                            @php
                            $status = '';
                                if ($value->tipe_ujian == 0)
                                    $status = 'Proposal';
                                else if($value->tipe_ujian == 2){
                                    $status = 'Ujian Meja';
                                }
                            @endphp
                            
                            <td>{{$status}}</td>
                            <td><a href="{{ url('mhs/detail_ujian')}}/{{$value->C_NPM}}/{{$value->tipe_ujian}}"><i class="fa fa-paperclip icon-square icon-xs icon-dark"></i></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->






@endsection


