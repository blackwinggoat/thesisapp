@extends('tugasakhir.index')
@section('isi')
			
			
			
			<!-- BEGIN PAGE CONTENT -->
			<div class="page-content">
				
				
				<div class="container-fluid">
					<!-- Begin page heading -->
					<h1 class="page-heading">Morris chart <small>Sub heading here</small></h1>
					<!-- End page heading -->
				
					<!-- Begin breadcrumb -->
					<ol class="breadcrumb default square rsaquo sm">
						<li><a href="index.html"><i class="fa fa-home"></i></a></li>
						<li><a href="#fakelink">Chart or graph</a></li>
						<li class="active">Morris chart</li>
					</ol>
					<!-- End breadcrumb -->
					
					<!-- BEGIN MORRIS CHART -->
					<div class="row">
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">Grafik Jumlah Lulusan pertahun Ajaran</h4>
								<div id="morris-line-example" style="height: 250px;"></div>
							</div><!-- .the-box -->
						</div><!-- /.col-sm-6 -->
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">Grafik Jumlah Lulusan berdasarkan Bidang Ilmu</h4>
								<div id="morris-area-example" style="height: 250px;"></div>
							</div><!-- .the-box  -->
						</div><!-- /.col-sm-6 -->
						<div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">Perbandingan Jumlah lulusan dan Jumlah mahasiswa baru</h4>
								<div id="morris-bar-example" style="height: 250px;"></div>
							</div><!-- .the-box -->
						</div><!-- /.col-sm-6 -->
						<!-- <div class="col-sm-6">
							<div class="the-box">
								<h4 class="small-title">MORRIS DONUT</h4>
								<div id="morris-donut-example" style="height: 250px;"></div>
							</div><!-- .the-box  -->
						</div><!-- /.col-sm-6 --> -->
					</div>
					<!-- END MORRIS CHART -->
					
					
					
					
				
				</div><!-- /.container-fluid -->
				
				
				
		
@endsection