@extends('tugasakhir.index')
@section('isi')

			<!-- BEGIN PAGE CONTENT -->
			<div class="page-content">
				
				
				<div class="container-fluid">
					<!-- Begin page heading -->
					<h1 class="page-heading">Easy pie chart and knob <small>Sub heading here</small></h1>
					<!-- End page heading -->
				
					<!-- Begin breadcrumb -->
					<ol class="breadcrumb default square rsaquo sm">
						<li><a href="index.html"><i class="fa fa-home"></i></a></li>
						<li><a href="#fakelink">Chart or graph</a></li>
						<li class="active">Easy pie chart and knob</li>
					</ol>
					<!-- End breadcrumb -->
	
					<h4 class="small-title">EASY PIE CHART</h4>
					<!-- BEGIN EASY PIE CHART -->
					<div class="row">
						<div class="col-sm-3">
							<div class="the-box text-center">
								<h4>Easy pie chart 1</h4>
								<hr />
								<span class="chart easy-pie-chart-1" data-percent="80">
									<span class="percent"></span>
								</span>
							</div><!-- /.the-box -->
						</div><!-- /.col-sm-3 -->
						<div class="col-sm-3">
							<div class="the-box text-center">
								<h4>Easy pie chart 2</h4>
								<hr />
								<span class="chart easy-pie-chart-2" data-percent="86">
									<span class="percent"></span>
								</span>
							</div><!-- /.the-box -->
						</div><!-- /.col-sm-3 -->
						<div class="col-sm-3">
							<div class="the-box text-center">
								<h4>Easy pie chart 3</h4>
								<hr />
								<span class="chart easy-pie-chart-3" data-percent="86">
									<span class="percent"></span>
								</span>
							</div><!-- /.the-box -->
						</div><!-- /.col-sm-3 -->
						<div class="col-sm-3">
							<div class="the-box text-center">
								<h4>Easy pie chart 4</h4>
								<hr />
								<span class="chart easy-pie-chart-4" data-percent="56">
									<span class="percent"></span>
								</span>
							</div><!-- /.the-box -->
						</div><!-- /.col-sm-3 -->
					</div><!-- /.row -->
					<!-- END EASY PIE CHART -->
						
					
					<h4 class="small-title">JQUERY KNOB</h4>
					<!-- BEGIN JQUERY KNOB -->
					<div class="row">
						<div class="col-sm-3">
							<div class="the-box text-center">
								<h4>Disable display input</h4>
								<hr />
								<input class="knob" data-width="160" data-displayInput=false value="35">
							</div><!-- /.the-box -->
						</div><!-- /.col-sm-3 -->
						<div class="col-sm-3">
							<div class="the-box text-center">
								<h4>'cursor' mode</h4>
								<hr />
								<input class="knob" data-width="160" data-cursor=true data-fgColor="#222222" data-thickness=.3 value="29">
							</div><!-- /.the-box  -->
						</div><!-- /.col-sm-3 -->
						<div class="col-sm-3">
							<div class="the-box text-center">
								<h4>Display previous value</h4>
								<hr />
								<input class="knob" data-width="160" data-min="-100" data-displayPrevious=true value="44">
							</div><!-- /.the-box  -->
						</div><!-- /.col-sm-3 -->
						<div class="col-sm-3">
							<div class="the-box text-center">
								<h4>Angle offset</h4>
								<hr />
								<input class="knob" data-width="160" data-angleOffset=90 data-linecap=round value="35">
							</div><!-- /.the-box -->
						</div><!-- /.col-sm-3 -->
					</div><!-- /.row -->
					<!-- END JQUERY KNOB -->
					
					
				
				</div><!-- /.container-fluid -->
				
				
				
	
@endsection