@extends('tugasakhir.index')
@section('isi')
			<!-- BEGIN PAGE CONTENT -->
			<div class="page-content">
				<div class="container-fluid">
					<!-- BEGIN MAIL APPS NEW MAIL -->
					<div class="mail-apps-wrap">
						<div class="the-box bg-success no-border no-margin heading">
							<div class="row">
								<div class="col-sm-6">
									<h1><i class="fa fa-envelope icon-lg icon-circle icon-bordered"></i> Pesan Baru</h1>
								</div><!-- /.col-sm-6 -->
								<div class="col-sm-6 text-right">
									<button class="btn btn-success btn-sm"><i class="fa fa-cloud-upload"></i></button>
									<div class="btn-group">
										<button class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
										<i class="fa fa-cog"></i>
										</button>
										<ul class="dropdown-menu success pull-right square margin-list text-left" role="menu">
											<li><a href="#fakelink">Action</a></li>
											<li><a href="#fakelink">Another action</a></li>
											<li class="active"><a href="#fakelink">Active</a></li>
											<li class="divider"></li>
											<li><a href="#fakelink">Separated link</a></li>
										</ul>
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
							<form method="post" action="{{url('mhs/pesanpost')}}" enctype="multipart/form-data">
								{{ csrf_field() }}
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<input type="hidden" name="status_kirim" value="1">
											<label>Penerima</label>
											<select data-placeholder="Select people..." class="form-control chosen-select" multiple tabindex="4" name="penerima_id[]" required>
												@foreach ($data as $key => $value)
												    <option value="{{$value->pembimbing_I_id}}">Pembimbing Utama - {{helper::getDeskripsi($value->pembimbing_I_id)}}</option>
												@endforeach
                                                @foreach ($data as $key => $value)
                                                    <option value="{{$value->pembimbing_II_id}}">Pembimbing Pendamping - {{helper::getDeskripsi($value->pembimbing_II_id)}}</option>
												@endforeach
												@foreach (helper::getPengujiByNim(auth()->user()->name) as $key => $value)
                                                    @if ($key == 0)
														<option value="{{$value->penguji_I_id}}">Penguji I (Proposal)- {{helper::getDeskripsi($value->penguji_I_id)}}</option>
													@else
														<option value="{{$value->penguji_I_id}}">Penguji I (Ujian TA)- {{helper::getDeskripsi($value->penguji_I_id)}}</option>
													@endif
												@endforeach
												@foreach (helper::getPengujiByNim(auth()->user()->name) as $key => $value)
                                                    @if ($key == 0)
														<option value="{{$value->penguji_II_id}}">Penguji II (Proposal)- {{helper::getDeskripsi($value->penguji_II_id)}}</option>
													@else
														<option value="{{$value->penguji_II_id}}">Penguji II (Ujian TA)- {{helper::getDeskripsi($value->penguji_II_id)}}</option>
													@endif
												@endforeach
												@foreach (helper::getPengujiByNim(auth()->user()->name) as $key => $value)
                                                    @if ($key == 0)
														<option value="{{$value->penguji_III_id}}">Penguji III (Proposal)- {{helper::getDeskripsi($value->penguji_III_id)}}</option>
													@else
														<option value="{{$value->penguji_III_id}}">Penguji III (Ujian TA)- {{helper::getDeskripsi($value->penguji_III_id)}}</option>
													@endif
												@endforeach
                                                @foreach ($data as $key => $value)
                                                    <option value="{{$value->C_NPM}}">Saved Message</option>
                                                @endforeach
											</select>
										</div>
										<div class="form-group">
											<label>Attachment</label>
											<div class="input-group">
												<input type="text" class="form-control" readonly>
												<span class="input-group-btn">
													<span class="btn btn-primary btn-file">
														Browse files<input type="file" multiple name="lampiran[]">
													</span>
												</span>
											</div><!-- /.input-group -->
												<span>*File dapat berupa dokumen atau gambar (pdf,xls,doc,docx,pptx,pps,jpeg,bmp,png,xlsx,zip,rar)</span>
								<br>
										</div>
										@if(!empty(session("error")))
											<div class="alert alert-danger alert-block fade in alert-dismissable">
												<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
												<strong>Size error!</strong> {{session("error")}}
											</div>
										@endif
									</div><!-- /.col-sm-4 -->
									<div class="col-sm-8">
										<div class="form-group">
											<label>Perihal Pesan</label>
											<input type="text" class="form-control input-lg" placeholder="perihal_Pesan" name="perihal_pesan" required>
										</div><!-- /.input-group -->
										<div class="form-group">
											<textarea class="summernote-sm" name="isi_pesan">Assalamualaikum, </textarea>
										</div><!-- /.input-group -->
									</div><!-- /.col-sm-8 -->
								</div><!-- /.row -->
								<div class="form-group">
									<button type="submit" class="btn btn-success"><i class="fa fa-rocket"></i> Kirim Pesan</button>
								</div>
							</form>
						</div><!-- /.the-box -->
					</div><!-- /.mail-apps-wrap -->
					<!-- END MAIL APPS NEW MAIL -->
					
				</div><!-- /.container-fluid -->
			</div><!-- /.page-content -->
		</div><!-- /.wrapper -->
		<!-- END PAGE CONTENT -->
@endsection

