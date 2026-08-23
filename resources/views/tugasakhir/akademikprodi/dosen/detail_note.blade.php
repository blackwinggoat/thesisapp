@extends('tugasakhir.index')
@section('isi')
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
            <li class="active">Form Ubah Note</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Form Ubah Note</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <form method="post" action="{{url('dsn/detail_note')}}/{{$data[0]->topik_id}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <fieldset>
                    <div class="form-group">
                            <label class="col-lg-2 control-label">Note</label>
                            <div class="col-lg-10 mb-5">
                                <textarea class="summernote-sm" name="note">{{$data[0]->note}}</textarea>
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
