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
                <li><a href="{{ url('prodi/dosen_pembimbing')}}">Dosen Pembimbing</a></li>
                <li class="active">Detail Dosen Pembimbing</li>
            </ol>


            <h3 class="page-heading">Detail Dosen Pembimbing</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">

                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">NIDN</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="nidn" value="{{$data->C_KODE_DOSEN}}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Nama</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="nama" value="{{$data->NAMA_DOSEN}}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">No Handphone</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="noHp" value="{{$data->NO_TELP}}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Alamat</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="alamat" value="{{$data->ALAMAT}}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Jumlah Bimbingan</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="jmlBimbingan" value="{{$total}}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Detail Bimbingan</label>
                        <div class="table-responsive col-lg-7">
                            <table class="table table-th-block">
                                <thead>
                                <tr>
                                    <th>Tahapan Mahasiswa Bimbingan</th>
                                    <th>Pembimbing Utama</th>
                                    <th>Pembimbing Pendamping</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Persiapan Proposal</td>
                                    <td>{{count($ppropI)}}</td>
                                    <td>{{count($ppropII)}}</td>
                                </tr>
                                <tr>
                                    <td>Persiapan Ujian Meja</td>
                                    <td>{{count($pmejaI)}}</td>
                                    <td>{{count($pmejaII)}}</td>
                                </tr>
                                <tr>
                                    <td>Lulusan</td>
                                    <td>{{count($alumniI)}}</td>
                                    <td>{{count($alumniII)}}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div><!-- /.table-responsive -->
                    </div>
                </fieldset>
            </div><!-- /.the-box -->
            <!-- End breadcrumb -->
            <h3 class="page-heading">Daftar Mahasiswa Bimbingan</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            {{-- <th>No SK Bimbingan</th> --}}
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Tahapan Bimbingan</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $a = 0;
                        ?>
                        @foreach ($data_bimbingan1 as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$a}}</td>
                                {{-- <td>{{helper::getNomorSkByNIM($value->C_NPM)}}</td> --}}
                                <td>{{$value->C_NPM}}</td>
                                <td>{{$value->NAMA_MAHASISWA}}</td>
                                <td>
                                    @if($value->status_bimbingan==0)
                                        Persiapan Proposal
                                    @elseif($value->status_bimbingan==2)
                                        Persiapan Ujian Meja
                                    @elseif($value->status_bimbingan==3)
                                        Persiapan Wisuda
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @foreach ($data_bimbingan2 as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$a}}</td>
                                {{-- <td>{{helper::getNomorSkByNIM($value->C_NPM)}}</td> --}}
                                <td>{{$value->C_NPM}}</td>
                                <td>{{$value->NAMA_MAHASISWA}}</td>
                                <td>
                                    @if($value->status_bimbingan==0)
                                        Persiapan Proposal
                                    @elseif($value->status_bimbingan==2)
                                        Persiapan Ujian Meja
                                    @elseif($value->status_bimbingan==3)
                                        Persiapan Wisuda
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



