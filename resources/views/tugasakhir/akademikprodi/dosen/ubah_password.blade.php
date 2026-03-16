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
            <li><a href="{{ url('/dsn/ubah_password')}}"> Ubah Password</a></li>
            <li class="active">Set Penguji</li>
        </ol>


        <h3 class="page-heading">Ubah Password</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">

            <form method="post" action="{{url('dsn/ubah_password')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <fieldset>
                    <input type="hidden" name="name" value="{{auth()->user()->name}}">
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Password Baru</label>
                        <div class="col-lg-5">
                            <input type="password" class="form-control bold-border" name="password_baru" required/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Ulangi Password</label>
                        <div class="col-lg-5">
                            <input type="password" class="form-control bold-border" name="ulangi_password" required/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <div class="col-xs-7" align="right">
                            <button class="btn btn-primary btn-perspective" type="button" onclick="showPostModal(this)"
                                data-formaction="{{url('dsn/ubah_password/')}}" data-target="#modalPrimary"
                                data-toggle="modal">Submit</button>
                        </div>
                    </div>
                </fieldset>
            </form>
            @if(session()->has('success'))
            <div class="form-group mt-2">
                <label class="col-lg-2 control-label"></label>
                <div class="col-lg-5">
                    <div class="alert alert-success" role="alert">
                        <p class="text-center">{{ session()->get('success') }}</p>
                    </div>
                </div>
            </div>
            <br><br>
            @endif
            @if(session()->has('error'))
            <div class="form-group mt-2">
                <label class="col-lg-2 control-label"></label>
                <div class="col-lg-5">
                    <div class="alert alert-danger" role="alert">
                        <p class="text-center">{{ session()->get('error') }}</p>
                    </div>
                </div>
            </div>
            <br><br>
            @endif
        </div>
    </div>
</div>
@endsection

{{--ModalSetUser--}}
@section("modalPrimaryTitle")
Set Penguji
@endsection
@section("modalPrimaryBody")
Apakah Anda yakin untuk mengubah password ?
@endsection
@section("modalPrimaryFooter")
<button onclick="submit(this)" class="btn btn-default">Ubah</button>
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

    const submit = () => {
        form = document.querySelector(`form[action="${formaction}"]`);
        form.submit();
    }

</script>
@endsection
