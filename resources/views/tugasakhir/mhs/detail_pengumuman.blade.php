@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">


    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading">Blog column <small>Sub heading here</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Blog apps</a></li>
            <li class="active">Blog column</li>
        </ol>
        <!-- End breadcrumb -->

        <div class="row">
            @foreach ($data as $value)
            <div class="col-sm-4">
                <div class="featured-post-wide">
                    @if ($value->gambar == '')
                    <img class="media-object" src="{{asset('gambar/no_image.jpg')}}" height="200" width="100%" class="featured-img" alt="Image">
                    @else
                    <img class="media-object" src="{{asset('gambar/'.$value->gambar)}}" height="200" width="100%" class="featured-img" alt="Image">
                    @endif
                    <i class="fa fa-newspaper-o icon-info icon-square icon-xs icon-type"></i>
                    <div class="featured-text relative">
                        <h3><a href="#fakelink">{{$value->judul}}</a></h3>
                        <p class="date">{{$value->last_update}}</p>
                        <p>
                            {{mb_strimwidth(strip_tags($value->isi), 0, 100, '...')}}
                        </p>
                        <hr />
                        <p class="text-right"><a href="{{url('mhs/pengumuman/show/'.$value->pengumuman_id)}}" class="btn btn-success">Read more</a></p>
                    </div><!-- /.featured-text -->
                </div><!-- /.featured-post-wide -->
                <!-- END STATIC IMAGE POST -->
            </div><!-- /.col-sm-4 -->
            @endforeach
        </div><!-- /.row -->



    </div><!-- /.container-fluid -->


</div><!-- /.page-content -->
</div><!-- /.wrapper -->
<!-- END PAGE CONTENT -->
@endsection