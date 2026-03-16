@extends('tugasakhir.index')
@section('isi')

			<!-- BEGIN PAGE CONTENT -->
			<div class="page-content">
				
				
				<div class="container-fluid">
					<!-- Begin page heading -->
					<h1 class="page-heading">Flot chart <small>Sub heading here</small></h1>
					<!-- End page heading -->
				
					<!-- Begin breadcrumb -->
					<ol class="breadcrumb default square rsaquo sm">
						<li><a href="index.html"><i class="fa fa-home"></i></a></li>
						<li><a href="#fakelink">Chart or graph</a></li>
						<li class="active">Flot chart</li>
					</ol>
					<!-- End breadcrumb -->
					
					
					<!-- BEGIN FLOT CHART -->
					<div class="the-box">
						<div id="visitors-chart">
							<div id="visitors-container" style="width: 100%;height:300px; text-align: center; margin:0 auto;"></div>
						</div><!-- /.visitors-chart -->
					</div><!-- /.the-box -->
					
					<div class="the-box">
						<div id="reatltime-chart">
							<div id="reatltime-chartContainer" style="width:100%;height:300px; text-align: center; margin:0 auto;"></div>
						</div><!-- /.realtime-chart -->
					</div><!-- /.the-box -->
					<!-- END FLOT CHART -->
					
					
					
				
				</div><!-- /.container-fluid -->
				
@endsection