@extends('tugasakhir.index')
@section('isi')

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Draft Final <small>Mahasiswa</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Draft Final</li>
        </ol>

        @if (session('draft_final_success'))
            <div class="alert alert-success" role="alert"><strong>Berhasil! </strong>{{ session('draft_final_success') }}</div>
        @endif
        @if (session('draft_final_error'))
            <div class="alert alert-danger" role="alert"><strong>Gagal! </strong>{{ session('draft_final_error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Periksa kembali link draft final:</strong>
                <ul style="margin: 8px 0 0 18px; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="the-box">
                    <form method="post" action="{{ url('mhs/draft_final') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Link Draft Final Proposal</label>
                            <input type="url" class="form-control" name="draft_proposal_url" value="{{ old('draft_proposal_url', $draft->draft_proposal_url ?? '') }}" placeholder="https://drive.google.com/file/d/.../view">
                            <small class="text-muted">Gunakan link file Google Drive/Docs, bukan folder. Atur akses file menjadi dapat dilihat oleh penerima link.</small>
                        </div>
                        <div class="form-group">
                            <label>Link Draft Final Tugas Akhir</label>
                            <input type="url" class="form-control" name="draft_tugas_akhir_url" value="{{ old('draft_tugas_akhir_url', $draft->draft_tugas_akhir_url ?? '') }}" placeholder="https://drive.google.com/file/d/.../view">
                            <small class="text-muted">Link ini nanti dapat dilampirkan saat mahasiswa mengirim pesan jadwal kepada dosen.</small>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Link</button>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="the-box">
                    <h4>Status Link</h4>
                    <p>
                        <strong>Proposal:</strong>
                        @if (!empty($draft->draft_proposal_url))
                            <span class="label label-success">Tersimpan</span>
                            <a href="{{ $draft->draft_proposal_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-default" style="margin-left: 6px;"><i class="fa fa-external-link"></i></a>
                            <a href="{{ url('mhs/mail_new?draft=proposal&perihal=Draft%20Final%20Proposal') }}" class="btn btn-xs btn-success" style="margin-left: 6px;"><i class="fa fa-envelope"></i> Kirim</a>
                        @else
                            <span class="label label-default">Belum ada</span>
                        @endif
                    </p>
                    <p>
                        <strong>Tugas Akhir:</strong>
                        @if (!empty($draft->draft_tugas_akhir_url))
                            <span class="label label-success">Tersimpan</span>
                            <a href="{{ $draft->draft_tugas_akhir_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-default" style="margin-left: 6px;"><i class="fa fa-external-link"></i></a>
                            <a href="{{ url('mhs/mail_new?draft=tugas_akhir&perihal=Draft%20Final%20Tugas%20Akhir') }}" class="btn btn-xs btn-success" style="margin-left: 6px;"><i class="fa fa-envelope"></i> Kirim</a>
                        @else
                            <span class="label label-default">Belum ada</span>
                        @endif
                    </p>
                    <hr>
                    <p class="text-muted">Validasi sistem menolak link folder Google Drive dan hanya menerima pola link file dari Google Drive, Google Docs, Sheets, atau Slides.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
