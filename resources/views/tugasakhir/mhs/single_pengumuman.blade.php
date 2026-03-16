@extends('tugasakhir.index')
@section('isi')
<div class="page-content">

    <div class="container-fluid">
        <div class="the-box full-width full">
            <div class="row">
                <div class="col-sm-8 col-md-9 col-full-width-right">
                    <div class="blog-detail-image">
                        @if ($data->gambar == '')
                        <img class="media-object" src="{{asset('gambar/no_image.jpg')}}" width="100%" lass="img-blog"
                            alt="Blog image">
                        @else
                        <img class="media-object" src="{{asset('gambar/'.$data->gambar)}}" width="100%" lass="img-blog"
                            alt="Blog image">
                        @endif
                        <div class="blog-title">
                            <h5>Ditulis</h5>
                            <a href="#fakelink"><img src="{{asset('master/assets/img/avatar/avatar-1.jpg')}}"
                                    class="avatar img-circle" alt="Avatar"></a>
                            <p><strong>Admin</strong></p>
                            <h1>{{$data->judul}}</h1>
                        </div><!-- /.blog-title -->
                    </div><!-- /.blog-detail-image -->

                    <div class="the-box no-border blog-detail-content">
                        <p><span class="label label-danger square">{{$data->last_update}}</span></p>
                        <?= $data->isi ?>

                        <hr />
                    </div><!-- /the.box .no-border -->
                </div><!-- /.col-sm-9 -->

                <div class="col-sm-4 col-md-3 col-full-width-left">
                    <div class="the-box no-border no-margin more-padding">
                        <span class="label label-success square">RECENT POST</span>
                        <ul class="media-list media-sm media-dotted recent-post">
                            @foreach (helper::get5Pengumuman() as $value)
                            <li class="media">
                                <div class="media-body">
                                    <h4 class="media-heading"><a href="{{url('mhs/pengumuman/show/'.$value->pengumuman_id)}}">{{$value->judul}}</a>
                                    </h4>
                                    <p>{{mb_strimwidth(strip_tags($value->isi), 0, 100, '...')}}</p>
                                    <p class="text-danger"><small>{{$value->last_update}}</small></p>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div><!-- /.the-box .bg-primary .no-border .text-center .no-margin -->
                </div><!-- /.col-sm-3 -->
            </div><!-- /.row -->

        </div><!-- /.the-box full-width -->
    </div><!-- /.container-fluid -->
</div><!-- /.page-content -->
@endsection