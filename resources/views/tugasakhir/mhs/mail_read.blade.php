@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">


	<div class="container-fluid">


		<!-- BEGIN MAIL APPS READ MAIL -->
		<div class="mail-apps-wrap">
			<div class="the-box bg-success no-border no-margin heading">
				<div class="row">
					<div class="col-sm-6">
						<h1><i class="fa fa-envelope icon-lg icon-circle icon-bordered"></i> Baca Pesan</h1>
					</div><!-- /.col-sm-6 -->
					<div class="col-sm-6 text-right">

						<div class="btn-group">


						</div>
					</div><!-- /.col-sm-6 -->
				</div><!-- /.row -->
			</div>

			<div class="the-box toolbar no-border no-margin">
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group">
						<a href="{{ url('mhs/mail_inbox')}}" class="btn btn-primary"><i class="fa"></i> Pesan Masuk</a>
					</div>
					<div class="btn-group">
						<a href="{{ url('mhs/mail_sent')}}" class="btn btn-primary"><i class="fa"></i> Pesan Keluar</a>
					</div>

				</div><!-- /.btn-toolbar -->
			</div><!-- /.the-box -->

			<div class="the-box no-margin">
				<div class="row">

					<div class="col-sm-8 col-md-9">
						<h4>PERIHAL PESAN : <b>{{$data->perihal_pesan}}</b></h4>
						<div class="panel panel-transparent panel-square">
							<div class="panel-heading">
								<h3 class="panel-title">
									<a class="block-collapse" data-toggle="collapse" href="#read-mail-example-1">
										@if ($status == 1)
											@if (Auth::user()->level==8)
												<strong>Pengirim : {{ helper::getDeskripsi($data->pengirim_id)}}</strong>
											@elseif((Auth::user()->level==7))
												<strong>Pengirim : {{ helper::getNamaMhs($data->pengirim_id)}}</strong>
											@endif
										@else
											@if (Auth::user()->level==8)
												<strong>Pengirim : {{ helper::getNamaMhs($data->pengirim_id)}}</strong>
											@elseif((Auth::user()->level==7))
												<strong>Pengirim : {{ helper::getDeskripsi($data->pengirim_id)}}</strong>
											@endif
										@endif
										<span class="right-content">
											<span class="time">{{$data->created_at}}</span>
										</span>
									</a>
								</h3>
							</div>
							<div id="read-mail-example-1" class="collapse in">
								<div class="panel-body">

									<p>
										<?= $data->isi_pesan ?>
									</p>

								</div><!-- /.panel-body -->
								<div class="panel-footer">
									<p><strong>Attachment :</strong></p>
									<ul class="attachment-list">
										@php
										$lampiran = \App\LampiranPesan::where("pesan_id", $data->pesan_id)->get();
										@endphp
										@foreach($lampiran as $l)
										<li><a href="{{asset('dokumen/'.$l->lampiran)}}"
												target="_blank">{{$l->lampiran}}</a></li>
										@endforeach
									</ul>
								</div><!-- /.panel-footer -->
							</div><!-- /.collapse in -->
						</div><!-- /.panel panel-default -->
						<div class="panel panel-transparent panel-square">
							<div class="panel-heading">
								<h3 class="panel-title">
									<a class="block-collapse" data-toggle="collapse" href="#read-mail-example-2">

										<span class="right-content">
											<span class="time">11:23 AM</span>
										</span>
									</a>
								</h3>
							</div>
							<div id="read-mail-example-2" class="collapse">
								<div class="panel-body">
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-sm dropdown-toggle"
											data-toggle="dropdown">
											Detail
											<span class="caret"></span>
										</button>
										<ul class="dropdown-menu">
											<li>
												<ul class="mail-info-detail">
													<li>To : <strong>Paris Hawker</strong></li>
													<li>Email : <strong class="text-danger">someone@domain.com</strong>
													</li>
													<li>Sent : April 21, 2014 at 11:23:56 AM</li>
													<li>Subject : <strong class="text-info">Lorem ipsum dolor sit amet,
															consectetuer</strong></li>
												</ul>
											</li>
										</ul>
									</div>
									<br />
									<br />
									<p>
										Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh
										euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad
										minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut
										aliquip ex ea commodo consequat.
									</p>
									<p>
										Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh
										euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad
										minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut
										aliquip ex ea commodo consequat.
									</p>
									<p>
										Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh
										euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad
										minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut
										aliquip ex ea commodo consequat.
									</p>
								</div><!-- /.panel-body -->
							</div><!-- /.collapse -->
						</div><!-- /.panel panel-default -->
						<div class="panel panel-transparent panel-square">
							<div class="panel-heading">
								<h3 class="panel-title">
									<a class="block-collapse">

									</a>
								</h3>
							</div>
							@if ($status == 1)
								<form action="{{url('mhs/mail_reply')}}" method="post">
									@csrf
									<input type="hidden" name="pesan_id" value="{{$data->pesan_id}}">
									<button type="submit" class="btn btn-info">Balas Pesan</button>
								</form>
							@endif
							

							<div class="panel-body">
								<div class="box-reply">
								</div><!-- /.box-reply -->
							</div><!-- /.panel-body -->
						</div><!-- /.panel panel-default -->

					</div><!-- /.col-sm-8 col-md-9 -->
				</div><!-- /.row -->
			</div><!-- /.the-box -->
		</div><!-- /.mail-apps-wrap -->
		<!-- END MAIL APPS READ MAIL -->


	</div><!-- /.container-fluid -->



</div><!-- /.page-content -->
</div><!-- /.wrapper -->
<!-- END PAGE CONTENT -->
@endsection