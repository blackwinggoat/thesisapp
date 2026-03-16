@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
			<div class="page-content">
				<div class="container-fluid">
                    <!-- BEGIN MAIL APPS INBOX -->
					<div class="mail-apps-wrap">
						<div class="the-box bg-success no-border no-margin heading">
							<div class="row">
								<div class="col-sm-6">
									<h1><i class="fa fa-envelope icon-lg icon-circle icon-bordered"></i> Pesan Masuk</h1>
								</div><!-- /.col-sm-6 -->
								<div class="col-sm-6 text-right">
									<button class="btn btn-success btn-sm"><i class="fa fa-cloud-upload"></i></button>
									<div class="btn-group">
										<button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
										<i class="fa fa-cog"></i>
										</button>
									</div>
								</div><!-- /.col-sm-6 -->
							</div><!-- /.row -->
						</div>
						
						<div class="the-box toolbar no-border no-margin">
							<div class="btn-toolbar" role="toolbar">
								<div class="btn-group">
									<a href="{{ url('dsn/mail_new')}}" class="btn btn-danger"><i class="fa fa-pencil"></i> Pesan Baru</a>
								</div>

								<div class="btn-group pull-right">
									<button type="button" class="btn btn-success"><i class="fa fa-chevron-left"></i></button>
									<button type="button" class="btn btn-success"><i class="fa fa-chevron-right"></i></button>
								</div><!-- /.btn-group .pull-right -->
							</div><!-- /.btn-toolbar -->
						</div><!-- /.the-box -->
						
						<div class="the-box no-margin">
							<div class="row">
								<div class="col-sm-4 col-md-3">
									<div class="list-group success square no-border">
									  <a href="{{ url('dsn/mail_inbox')}}" class="list-group-item active">Pesan Masuk <span class="badge badge-success">{{helper::getDataPesanMasuk(auth()->user()->name)}}</span></a>
									  <a href="{{ url('dsn/mail_sent')}}" class="list-group-item">Pesan Keluar <span class="badge badge-primary">{{helper::getDataPesanKeluar(auth()->user()->name)}}</span></a>
									</div>

								</div><!-- /.col-sm-4 col-md-3 -->
								<div class="col-sm-8 col-md-9">
									<div class="the-box full no-border">
										<form role="form">
											<div class="form-group has-feedback no-label">
											  <input type="text" class="form-control" placeholder="Search mail...">
											  <span class="fa fa-search form-control-feedback"></span>
											</div>
										</form>
									</div>
									<div class="list-group success square no-side-border">
									@foreach ($data as $key => $value)
									  <a href="{{ url('dsn/mail_read/'.$value->pesan_id.'/1')}}" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										  @if($value->status_baca == "0")
											  <i class="fa fa-circle text-primary"></i>
										  @endif
										<span class="name">{{ helper::getDeskripsi($value->pengirim_id)}}{{ helper::getNamaMhs($value->pengirim_id)}}</span>
										<span>{{$value->perihal_pesan}}</span>
										<span class="time">{{substr($value->created_at,0,10)}}</span>
									  </a>
									 @endforeach 
									</div>
								</div><!-- /.col-sm-8 col-md-9 -->
							</div><!-- /.row -->
						</div><!-- /.the-box -->
						
						<div class="the-box toolbar no-border no-margin">
							<div class="btn-toolbar" role="toolbar">
							
								<div class="btn-group pull-right">
									<button type="button" class="btn btn-success"><i class="fa fa-chevron-left"></i></button>
									<button type="button" class="btn btn-success"><i class="fa fa-chevron-right"></i></button>
								</div><!-- /.btn-group .pull-right -->
							</div><!-- /.btn-toolbar -->
						</div><!-- /.the-box -->
						
					</div><!-- /.mail-apps-wrap -->
					<!-- END MAIL APPS INBOX -->
					
				
				</div><!-- /.container-fluid -->
				
				
				
			</div><!-- /.page-content -->
		</div><!-- /.wrapper -->
		<!-- END PAGE CONTENT -->
@endsection
