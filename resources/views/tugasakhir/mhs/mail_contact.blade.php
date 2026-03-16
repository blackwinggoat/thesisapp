@extends('tugasakhir.index')
@section('isi')
			<!-- BEGIN PAGE CONTENT -->
			<div class="page-content">
				
				
				<div class="container-fluid">
					
					
					<!-- BEGIN MAIL APPS CONTACT -->
					<div class="mail-apps-wrap">
						<div class="the-box bg-success no-border no-margin heading">
							<div class="row">
								<div class="col-sm-6">
									<h1><i class="fa fa-users icon-lg icon-circle icon-bordered"></i> Mail contact</h1>
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
									<a href="#fakelink" class="btn btn-danger"><i class="fa fa-plus"></i> New contact</a>
								</div>
								<div class="btn-group">
									<button data-toggle="tooltip" title="Delete" type="button" class="btn btn-success"><i class="fa fa-trash-o"></i></button>
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
									  <a href="#fakelink" class="list-group-item active">All <span class="badge badge-success">201</span></a>
									  <a href="#fakelink" class="list-group-item">Work <span class="badge badge-success">5</span></a>
									  <a href="#fakelink" class="list-group-item">Family <span class="badge badge-info">11</span></a>
									  <a href="#fakelink" class="list-group-item">Client <span class="badge badge-warning">15</span></a>
									</div>
								</div><!-- /.col-sm-4 col-md-3 -->
								<div class="col-sm-8 col-md-9">
									<div class="the-box full no-border">
										<form role="form">
											<div class="form-group has-feedback no-label">
											  <input type="text" class="form-control" placeholder="Search contact...">
											  <span class="fa fa-search form-control-feedback"></span>
											</div>
										</form>
									</div>
									<div class="list-group success square no-side-border">
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-1.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Paris Hawker</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-2.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Thomas White</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-3.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Doina Slaivici</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-4.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Harry Nichols</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-5.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Mihaela Cihac</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-6.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Harold Chavez</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-7.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Elizabeth Owens</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-8.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Frank Oliver</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-9.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Mya Weastell</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-10.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Carl Rodriguez</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-11.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Nikita Carter</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-12.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Craig Dixon</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-13.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Mikayla King</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-14.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Richard Dixon</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
									  <a href="#fakelink" class="list-group-item mail-list">
										<input type="checkbox" class="i-grey-flat">
										<img src="assets/img/avatar/avatar-15.jpg" class="avatar img-circle" alt="Avatar">
										<span class="name">Brenda Fuller</span>
										<span class="subject text-danger">someone@domain.com</span>
										<span class="time"><span class="label label-info">Family</span></span>
									  </a>
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
					<!-- END MAIL APPS CONTACT -->
					
				
				</div><!-- /.container-fluid -->
				
				
				
			</div><!-- /.page-content -->
		</div><!-- /.wrapper -->
		<!-- END PAGE CONTENT -->
@endsection
