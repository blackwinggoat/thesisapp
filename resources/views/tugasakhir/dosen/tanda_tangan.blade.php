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
            <h3 class="page-heading">Tanda Tangan Dosen</h3>
            <style>
                .signature-preview-frame {
                    align-items: center;
                    background: #f8fafc;
                    border: 1px solid #d8e0e8;
                    display: flex;
                    height: 150px;
                    justify-content: center;
                    padding: 8px;
                }
                .signature-preview-frame img {
                    display: block;
                    height: 130px;
                    max-width: 100%;
                    object-fit: contain;
                    width: 100%;
                }
                .signature-delete-form {
                    margin-top: 12px;
                }
                .signature-delete-note {
                    color: #6b7280;
                    font-size: 12px;
                    line-height: 1.4;
                    margin: 9px 0 0;
                }
                .signature-method {
                    border: 1px solid #d8e0e8;
                    min-height: 318px;
                    padding: 18px;
                }
                .signature-method h5 {
                    color: #1f2937;
                    font-size: 16px;
                    font-weight: 600;
                    margin: 0 0 7px;
                }
                .signature-method p {
                    color: #64748b;
                    line-height: 1.45;
                    margin: 0 0 16px;
                }
                .signature-drawpad {
                    border: 1px solid #cbd5e1;
                    cursor: crosshair;
                    display: block;
                    height: 160px;
                    touch-action: none;
                    width: 100%;
                }
                .signature-upload-feedback {
                    display: none;
                    margin: 0 0 12px;
                }
                .signature-upload-feedback.is-visible {
                    display: block;
                }
                @media (max-width: 991px) {
                    .signature-method {
                        margin-bottom: 16px;
                        min-height: 0;
                    }
                }
            </style>
            <div class="row">
                <div class="col-md-9">
                    <div class="the-box">
                        <h4>Simpan Tanda Tangan</h4>
                        <p class="text-muted">Pilih satu cara saja. Tombol pada masing-masing bagian hanya menyimpan cara tersebut.</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="signature-method">
                                    <h5><i class="fa fa-upload"></i> Upload File Tanda Tangan</h5>
                                    <p>Pilih foto atau hasil scan TTD berformat PNG atau JPG. Area putih di sekeliling TTD akan dirapikan otomatis.</p>
                                    <form id="signature_upload_form" action="{{ url('/dsn/upload_ttd') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="sumber_tanda_tangan" value="upload">
                                        <div id="upload_feedback" class="alert alert-danger signature-upload-feedback" role="alert"></div>
                                        <div class="form-group">
                                            <label for="upload_ttd">Pilih File PNG atau JPG</label>
                                            <input type="file" class="form-control" id="upload_ttd" name="upload_ttd"
                                                accept="image/png, image/jpeg">
                                            <p class="help-block">Ukuran maksimal {{ $signatureUploadLimit['label'] }} mengikuti batas server saat ini.</p>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-upload"></i> Upload dan Simpan
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="signature-method">
                                    <h5><i class="fa fa-pencil"></i> Gambar Tanda Tangan Langsung</h5>
                                    <p>Gunakan mouse atau sentuhan untuk menggambar. Cara ini tidak membutuhkan file.</p>
                                    <form id="signature_draw_form" action="{{ url('/dsn/upload_ttd') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="sumber_tanda_tangan" value="draw">
                                        <input type="hidden" id="ttd_image" name="ttd_image">
                                        <div id="draw_feedback" class="alert alert-danger signature-upload-feedback" role="alert"></div>
                                        <canvas id="drawpad_ttd" class="signature-drawpad"></canvas>
                                        <div style="margin-top: 12px;">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa fa-save"></i> Simpan Gambar Tanda Tangan
                                            </button>
                                            <button type="button" class="btn btn-default" id="clear_signature">
                                                <i class="fa fa-eraser"></i> Bersihkan Gambar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.the-box -->
                </div><!-- /.col-md-9 -->

                <!-- Col 3: Display Signature -->
                <div class="col-md-3">
                    <div class="the-box">
                        <h4>Pratinjau Tanda Tangan</h4>
                        {{-- alert if $tandaTangan is false --}}
                        @if (!$tandaTangan || $tandaTanganPerluUnggahUlang)
                            <div class="alert alert-danger alert-block square fade in alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                {{ $tandaTanganPerluUnggahUlang ? 'Tanda tangan lama tidak dapat digunakan. Silakan unggah atau gambar ulang tanda tangan Anda.' : 'Anda belum mengunggah tanda tangan. Silakan unggah tanda tangan Anda.' }}
                            </div>
                        @endif
                        <div class="signature-preview-frame">
                            <img id="ttd_preview"
                                src="{{ $tandaTanganPreview ?: asset('gambar/no_image.jpg') }}"
                                data-default-src="{{ $tandaTanganPreview ?: asset('gambar/no_image.jpg') }}"
                                alt="Tanda Tangan">
                        </div>
                        @if ($tandaTangan)
                            <form action="{{ route('dosen.tanda_tangan.delete') }}" method="POST" class="signature-delete-form"
                                onsubmit="return confirm('Hapus tanda tangan saat ini? Anda perlu mengunggah atau menggambar ulang tanda tangan setelahnya.');">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fa fa-trash"></i> Hapus Tanda Tangan
                                </button>
                            </form>
                            <p class="signature-delete-note">Tanda tangan aktif dan salinan normalisasi lama akan dihapus.</p>
                        @endif
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
            var hasInk = false;
            var uploadInput = document.getElementById('upload_ttd');
            var uploadForm = document.getElementById('signature_upload_form');
            var drawForm = document.getElementById('signature_draw_form');
            var uploadFeedback = document.getElementById('upload_feedback');
            var drawFeedback = document.getElementById('draw_feedback');
            var preview = document.getElementById('ttd_preview');
            var maxUploadBytes = {{ (int) $signatureUploadLimit['bytes'] }};
            var maxUploadLabel = @json($signatureUploadLimit['label']);

            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#1f2937';

            function showFeedback(element, message) {
                element.textContent = message;
                element.classList.add('is-visible');
            }

            function hideFeedback(element) {
                element.textContent = '';
                element.classList.remove('is-visible');
            }

            function canvasPoint(event) {
                var rect = canvas.getBoundingClientRect();
                return {
                    x: (event.clientX - rect.left) * (canvas.width / rect.width),
                    y: (event.clientY - rect.top) * (canvas.height / rect.height)
                };
            }

            canvas.addEventListener('pointerdown', function(event) {
                event.preventDefault();
                var point = canvasPoint(event);
                drawing = true;
                ctx.beginPath();
                ctx.moveTo(point.x, point.y);
                if (canvas.setPointerCapture) {
                    canvas.setPointerCapture(event.pointerId);
                }
            });

            canvas.addEventListener('pointermove', function(event) {
                if (drawing) {
                    event.preventDefault();
                    var point = canvasPoint(event);
                    ctx.lineTo(point.x, point.y);
                    ctx.stroke();
                    hasInk = true;
                    hideFeedback(drawFeedback);
                }
            });

            function stopDrawing(event) {
                if (event) {
                    event.preventDefault();
                }
                drawing = false;
            }

            canvas.addEventListener('pointerup', stopDrawing);
            canvas.addEventListener('pointercancel', stopDrawing);

            uploadInput.addEventListener('change', function() {
                hideFeedback(uploadFeedback);
                var file = uploadInput.files && uploadInput.files[0];
                if (!file) {
                    return;
                }

                var hasAllowedExtension = /\.(png|jpe?g)$/i.test(file.name || '');
                var isImage = file.type === 'image/png' || file.type === 'image/jpeg' || hasAllowedExtension;
                if (!isImage) {
                    uploadInput.value = '';
                    preview.src = preview.getAttribute('data-default-src');
                    showFeedback(uploadFeedback, 'Gunakan file tanda tangan berformat PNG atau JPG.');
                    return;
                }
                if (file.size > maxUploadBytes) {
                    uploadInput.value = '';
                    preview.src = preview.getAttribute('data-default-src');
                    showFeedback(uploadFeedback, 'Ukuran file melebihi batas ' + maxUploadLabel + '. Pilih file yang lebih kecil.');
                    return;
                }

                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });

            uploadForm.addEventListener('submit', function(event) {
                if (!uploadInput.files || !uploadInput.files[0]) {
                    event.preventDefault();
                    showFeedback(uploadFeedback, 'Pilih file PNG atau JPG terlebih dahulu.');
                }
            });

            drawForm.addEventListener('submit', function(event) {
                if (!hasInk) {
                    event.preventDefault();
                    showFeedback(drawFeedback, 'Gambar tanda tangan terlebih dahulu sebelum disimpan.');
                    return;
                }

                document.getElementById('ttd_image').value = canvas.toDataURL('image/png');
                preview.src = document.getElementById('ttd_image').value;
            });

            document.getElementById('clear_signature').addEventListener('click', function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                document.getElementById('ttd_image').value = '';
                hasInk = false;
                hideFeedback(drawFeedback);
                preview.src = preview.getAttribute('data-default-src');
            });
        });
    </script>
@endsection
