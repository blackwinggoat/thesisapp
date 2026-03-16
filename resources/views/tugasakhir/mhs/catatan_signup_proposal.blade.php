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
            <li class="active">Form Ubah Catatan</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Catatan</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <form method="post" action="{{url('mhs/signup_proposal_catatan')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <fieldset>
                    <input type="hidden" name="id" value="{{$data[0]->id}}">
                    <div class="form-group">
                            <label class="col-lg-2 control-label">Note</label>
                            <div class="col-lg-10 mb-5">
                                <textarea class="summernote-sm" name="catatan">{{$data[0]->catatan}}</textarea>
                            </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                            <label class="col-lg-2 control-label"></label>
                            <div class="col-lg-10 mb-5">
                                Terakhir Kali Diubah : <span class="badge badge-primary">{{$data[0]->updated_at}}</span>
                            </div>
                    </div>
                    <div class="form-group mt-2">
                        <div class="col-lg-12" align="right">
                            <button class="btn btn-primary btn-perspective" type="submit">Ubah</button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div><!-- /.the-box -->
    </div><!-- /.container-fluid -->
</div>
@endsection
