@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Form Ubah Note</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Form Usul Judul</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <form method="post" action="{{url('dsn/usul_judul_post')}}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Penerima</label>
								<select data-placeholder="Select people..." class="form-control chosen-select" multiple
									tabindex="4" name="penerima_id[]">
									{{-- <option value="">-- Anak Bimbingan --</option>
									@foreach ($data as $key => $value)
									<option value="{{$value->C_NPM}}">{{helper::getNamaMhs($value->C_NPM)}}</option>
									@endforeach --}}
									{{-- <option value="semua_mahasiswa"  style="color: green; font-weight: bold">-- Semua Mahasiswa --</option>
									@foreach ($data_semua_mahasiswa as $key => $value)
									<option value="{{$value->name}}">{{helper::getNamaMhs($value->name)}}</option>
									@endforeach --}}
									<option value="semua_mahasiswa" style="font-weight: bold">-- Mahasiswa Belum Memiliki Judul --</option>
									@foreach ($data_mahasiswa_belum_ada_judul as $key => $value)
									<option value="{{$value->name}}">{{helper::getNamaMhs($value->name)}}</option>
									@endforeach
									{{-- <option value="" style="color: brown; font-weight: bold">-- Mahasiswa Belum Menerima Usulan Judul --</option>
									@foreach ($data_mahasiswa_belum_menerima_usulan_judul as $key => $value)
									<option value="{{$value->name}}">{{helper::getNamaMhs($value->name)}}</option>
									@endforeach --}}
								</select>
							</div>
						</div><!-- /.col-sm-4 -->
						<div class="col-sm-8">
							<div class="form-group"> 
								<label>Perihal Pesan</label>
								<input type="text" class="form-control input-lg" placeholder="Judul"
									name="usulan_judul">
							</div><!-- /.input-group -->
						</div><!-- /.col-sm-8 -->
						<div class="col-sm-8">
							<div class="form-group">
								<label>Jenis Tugas Akhir</label>
								<select class="form-control" name="jenis_tugas_akhir_id" required>
									@foreach ($jenisTugasAkhir as $jenis)
									<option value="{{$jenis->jenis_tugas_akhir_id}}" @if($jenis->kode_jenis_tugas_akhir === 'TA-SM') selected @endif>
										{{$jenis->kode_jenis_tugas_akhir}} - {{$jenis->deskripsi}}
									</option>
									@endforeach
								</select>
							</div>
						</div>
					</div><!-- /.row -->
					<div class="form-group">
						<button type="submit" class="btn btn-primary"><i class="fa fa-rocket"></i>Submit</button>
					</div>
				</form>
        </div><!-- /.the-box -->
    </div><!-- /.container-fluid -->
</div>
@endsection
