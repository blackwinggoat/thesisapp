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
            <li class="active">Periode Jabatan</li>
        </ol>

        <!-- End breadcrumb -->
        <h3 class="page-heading">Daftar Periode Jabatan </h3>
        @if (Session::get("status") == "berhasil")
            <div class="alert alert-success" role="alert"><strong>Berhasil! </strong>Data Berhasil Disimpan</div>
        @elseif(Session::get("status") == "gagal")
            <div class="alert alert-danger" role="alert"><strong>Gagal! </strong>Data Gagal Disimpan</div>
        @endif
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Progam Studi</th>
                        <th>Tanggal Menjabat</th>
                        <th>Tanggal Berakhir</th>
                        <th>Email</th>
                        <th>Nomor Telepon</th>
                        <th>TTD</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $value)
                    <tr class="odd gradeX">
                        <td width="1%" align="center">{{++$key}}</td>
                        <td>{{$value->nama}}</td>
                        <td>{{$value->prodi}}</td>
                        <td>{{$value->tanggal_menjabat}}</td>
                        <td>{{$value->tanggal_berakhir}}</td>
                        <td>{{$value->email}}</td>
                        <td>{{$value->no_telepon}}</td>
                        @if ($value->ttd == '')
                            <td><img src="{{asset('gambar/no_image.jpg')}}" height="50" width="50"></td>
                        @else
                            <td><img src="{{asset('gambar/'.$value->ttd)}}" height="50" width="50"></td>
                        @endif
                        <td>
                            <a href="#" id="update-data-btn" 
                            data-id="{{$value->id_jabatan}}"
                            data-nama="{{$value->nama}}"
                            data-prodi="{{$value->prodi}}"
                            data-tanggal-menjabat="{{$value->tanggal_menjabat}}"
                            data-tanggal-berakhir="{{$value->tanggal_berakhir}}"
                            data-email="{{$value->email}}"
                            data-no-telepon="{{$value->no_telepon}}"
                            data-ttd="{{$value->ttd}}"
                            ><i class="fa fa-pencil icon-square icon-xs icon-dark"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->

    <div class="modal fade" id="update-data-modal" tabindex="-1" role="dialog" aria-labelledby="update-data-modal-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="update-data-form" action="{{ route('update_master_periode_jabatan') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_jabatan" id="id_jabatan">
                    @csrf
                    <div class="modal-header">
                        <h4 class="modal-title" id="update-data-modal-label">Update Data Periode Jabatan</h4>
                        </button>
                    </div>
                    <div class="modal-body">
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="form-group">
                                <label for="prodi">Prodi</label>
                                <input type="text" class="form-control" id="prodi" name="prodi" required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_menjabat">Tanggal Menjabat</label>
                                <input type="date" class="form-control" id="tanggal_menjabat" name="tanggal_menjabat" required>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_berakhir">Tanggal Berakhir</label>
                                <input type="date" class="form-control" id="tanggal_berakhir" name="tanggal_berakhir" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="no_telepon">Nomor Telepon</label>
                                <input type="text" class="form-control" id="no_telepon" name="no_telepon" required>
                            </div>
                            <div class="form-group">
                                <label for="ttd">TTD</label>
                                <input type="file" class="form-control" id="ttd" name="ttd">
                            </div>
                            <div class="form-group">
                                <img src="" id="ttd-img-tag"/>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" >Save changes</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
@endsection


@section("script")
    <script>
        $(document).ready(function() {
            $('body').on('click', '#update-data-btn', function() {
                var id_jabatan = $(this).data('id');
                var nama = $(this).data('nama');
                var prodi = $(this).data('prodi');
                var tanggal_menjabat = $(this).data('tanggal-menjabat');
                var tanggal_berakhir = $(this).data('tanggal-berakhir');
                var email = $(this).data('email');
                var no_telepon = $(this).data('no-telepon');
                var ttd = $(this).data('ttd');
                $('#id_jabatan').val(id_jabatan);
                $('#nama').val(nama);
                $('#prodi').val(prodi);
                $('#tanggal_menjabat').val(tanggal_menjabat);
                $('#tanggal_berakhir').val(tanggal_berakhir);
                $('#email').val(email);
                $('#no_telepon').val(no_telepon);
                if(ttd == ''){
                    $('#ttd-img-tag').attr('src', '{{asset('gambar/no_image.jpg')}}');
                    $('#ttd-img-tag').css('border-radius', '10%');
                    $('#ttd-img-tag').css('margin-left', 'auto');
                    $('#ttd-img-tag').css('margin-right', 'auto');
                    $('#ttd-img-tag').css('display', 'block');
                    $('#ttd-img-tag').css('width', '400px');
                }else{
                    $('#ttd-img-tag').attr('src', '{{asset('gambar/')}}/'+ttd);
                    $('#ttd-img-tag').css('border-radius', '10%');
                    $('#ttd-img-tag').css('border-radius', '10%');
                    $('#ttd-img-tag').css('margin-left', 'auto');
                    $('#ttd-img-tag').css('margin-right', 'auto');
                    $('#ttd-img-tag').css('display', 'block');
                    $('#ttd-img-tag').css('width', '400px');
                }
                $('#update-data-modal').modal('show');
            });
        });
    </script>
@endsection



