@extends('tugasakhir.index')
@section('isi')
			
			
			<!-- BEGIN PAGE CONTENT -->
			<div class="page-content">
				
				
				<div class="container-fluid">
					<!-- Begin page heading -->
					<h1 class="page-heading">C3 chart <small>Sub heading here</small></h1>
					<!-- End page heading -->
				
					<!-- Begin breadcrumb -->
					<ol class="breadcrumb default square rsaquo sm">
						<li><a href="index.html"><i class="fa fa-home"></i></a></li>
						<li><a href="#fakelink">Chart or graph</a></li>
						<li class="active">C3 chart</li>
					</ol>
					<!-- End breadcrumb -->
					
					
					<!-- BEGIN C3 CHART -->
					<div class="row">
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">C3 BAR</h4>
								<div id="c3-bar" style="height: 300px;"></div>
							</div><!-- .the-box  -->
						</div><!-- /.col-sm-6 -->
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">C3 LINE</h4>
								<div id="c3-line" style="height: 300px;"></div>
							</div><!-- .the-box -->
						</div><!-- /.col-sm-6 -->
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">C3 LINE 2</h4>
								<div id="c3-line2" style="height: 300px;"></div>
							</div><!-- .the-box -->
						</div><!-- /.col-sm-6 -->
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">C3 STACKED BAR</h4>
								<div id="c3-bar-stacked" style="height: 300px;"></div>
							</div><!-- .the-box -->
						</div><!-- /.col-sm-6 -->
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">C3 SPLINE</h4>
								<div id="c3-spline" style="height: 300px;"></div>
							</div><!-- .the-box  -->
						</div><!-- /.col-sm-6 -->
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">C3 AREA</h4>
								<div id="c3-area" style="height: 300px;"></div>
							</div><!-- .the-box -->
						</div><!-- /.col-sm-6 -->
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">C3 COMBINATION</h4>
								<div id="c3-kombi" style="height: 300px;"></div>
							</div><!-- .the-box -->
						</div><!-- /.col-sm-6 -->
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">C3 PIE</h4>
								<div id="c3-pie" style="height: 300px;"></div>
							</div><!-- .the-box -->
						</div><!-- /.col-sm-6 -->
					</div>
					<!-- END C3 CHART -->
					
					
					
				
				</div><!-- /.container-fluid -->
				
	
@endsection