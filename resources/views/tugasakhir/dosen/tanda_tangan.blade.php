@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
            <!-- End page heading -->

            <!-- Begin breadcrumb -->
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="index.html"><i class="fa fa-home"></i></a></li>
                <li><a href="#fakelink">Home</a></li>
                <li class="active">Tanda Tangan</li>
            </ol>
            <!-- End breadcrumb -->


            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif


            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Berita Acara</h3>
            <div class="row">
                <!-- Col 9: Form Upload or DrawPad -->
                <div class="col-md-9">
                    <div class="the-box">
                        <h4>Upload Tanda Tangan atau Gunakan DrawPad</h4>
                        <form action="{{ url('/dsn/upload_ttd') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group" id="upload_section">
                                <label for="upload_ttd">Upload Tanda Tangan (Format: PNG, JPG)</label>
                                <input type="file" class="form-control" id="upload_ttd" name="upload_ttd"
                                    accept="image/png, image/jpeg">
                            </div>
                            <div class="form-group" id="drawpad_section">
                                <label for="drawpad_ttd">Atau Gambar Tanda Tangan Langsung</label>
                                <canvas id="drawpad_ttd"
                                    style="border: 1px solid #ccc; width: 100%; height: 200px;"></canvas>
                                <input type="hidden" id="ttd_image" name="ttd_image">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-danger" onclick="clearSignature()">Clear</button>
                        </form>
                    </div><!-- /.the-box -->
                </div><!-- /.col-md-9 -->

                <!-- Col 3: Display Signature -->
                <div class="col-md-3">
                    <div class="the-box">
                        <h4>Pratinjau Tanda Tangan</h4>
                        {{-- alert if $tandaTangan is false --}}
                        @if (!$tandaTangan)
                            <div class="alert alert-danger alert-block square fade in alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                Anda belum mengunggah tanda tangan. Silakan unggah tanda tangan Anda.
                            </div>
                        @endif
                        <img id="ttd_preview"
                            src="{{ $tandaTangan ? 'data:image/png;base64,' . base64_encode($tandaTangan->tanda_tangan) : asset('gambar/no_image.jpg') }}"
                            alt="Tanda Tangan" style="width: 100%; height: auto; border: 1px solid #ccc;">
                    </div><!-- /.the-box -->
                </div><!-- /.col-md-3 -->
            </div><!-- /.row -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var canvas = document.getElementById('drawpad_ttd');
            var ctx = canvas.getContext('2d');
            var drawing = false;
            var uploadInput = document.getElementById('upload_ttd');
            var uploadSection = document.getElementById('upload_section');
            var drawpadSection = document.getElementById('drawpad_section');

            // Adjust canvas size to be responsive
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;

            // Handle drawing on canvas
            canvas.addEventListener('mousedown', function(e) {
                drawing = true;
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
            });

            canvas.addEventListener('mousemove', function(e) {
                if (drawing) {
                    ctx.lineTo(e.offsetX, e.offsetY);
                    ctx.stroke();
                }
            });

            canvas.addEventListener('mouseup', function() {
                drawing = false;
                document.getElementById('ttd_image').value = canvas.toDataURL('image/png');
                document.getElementById('ttd_preview').src = canvas.toDataURL('image/png');
                uploadSection.style.display = 'none'; // Hide upload section when drawing is done
            });

            canvas.addEventListener('mouseleave', function() {
                drawing = false;
            });

            // Handle file upload preview
            uploadInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('ttd_preview').src = e.target.result;
                        canvas.style.pointerEvents = 'none'; // Disable drawing
                        document.getElementById('ttd_image').value = ''; // Clear drawpad value
                        drawpadSection.style.display = 'none'; // Hide drawpad when file is uploaded
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            });

            // Function to clear the canvas and reset input fields
            window.clearSignature = function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                document.getElementById('ttd_image').value = '';
                document.getElementById('ttd_preview').src = '{{ asset('gambar/no_image.jpg') }}';
                canvas.style.pointerEvents = 'auto'; // Enable drawing again
                uploadInput.value = ''; // Clear file input

                // Show both sections again after clearing
                uploadSection.style.display = 'block';
                drawpadSection.style.display = 'block';
            }
        });
    </script>
@endsection
