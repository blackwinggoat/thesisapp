@extends('tugasakhir.index')

@section('isi')
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Pengaturan Email Sistem</li>
        </ol>

        @if (session('status') === 'berhasil')
            <div class="alert alert-success"><strong>Berhasil!</strong> Pengaturan email sistem sudah disimpan.</div>
        @elseif (session('status') === 'gagal')
            <div class="alert alert-danger"><strong>Gagal!</strong> Pengaturan email sistem belum dapat disimpan.</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Periksa kembali data berikut:</strong>
                <ul style="margin: 8px 0 0 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="the-box">
            <h3 class="page-heading" style="margin-top: 0;">Pengaturan Email Reset Password</h3>
            <p class="text-muted" style="max-width: 780px;">
                Data ini digunakan Thesis App untuk mengirim email reset password. Password SMTP disimpan terenkripsi dan tidak ditampilkan kembali setelah disimpan.
            </p>

            <form method="POST" action="{{ route('admin.mail_settings.update') }}" class="form-horizontal">
                @csrf

                <div class="form-group">
                    <label class="col-sm-3 control-label">Aktifkan Email Reset</label>
                    <div class="col-sm-7">
                        <label class="checkbox-inline">
                            <input type="checkbox" name="enabled" value="1" {{ old('enabled', $settings['enabled'] ?? false) ? 'checked' : '' }}>
                            Gunakan konfigurasi email dari halaman ini
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Mail Driver</label>
                    <div class="col-sm-7">
                        <select name="driver" class="form-control" required>
                            @foreach (['smtp', 'sendmail', 'mail', 'log'] as $driver)
                                <option value="{{ $driver }}" {{ old('driver', $settings['driver'] ?? 'smtp') === $driver ? 'selected' : '' }}>{{ strtoupper($driver) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">SMTP Host</label>
                    <div class="col-sm-7">
                        <input type="text" name="host" class="form-control" value="{{ old('host', $settings['host'] ?? '') }}" placeholder="mail.domain.ac.id">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">SMTP Port</label>
                    <div class="col-sm-7">
                        <input type="number" name="port" class="form-control" value="{{ old('port', $settings['port'] ?? 587) }}" min="1" max="65535">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">SMTP Username</label>
                    <div class="col-sm-7">
                        <input type="text" name="username" class="form-control" value="{{ old('username', $settings['username'] ?? '') }}" autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">SMTP Password</label>
                    <div class="col-sm-7">
                        <input type="password" name="password" class="form-control" value="" placeholder="{{ $passwordMask ?: 'Isi password SMTP' }}" autocomplete="new-password">
                        @if ($passwordMask)
                            <span class="help-block">Kosongkan jika tidak ingin mengganti password SMTP yang sudah tersimpan.</span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Encryption</label>
                    <div class="col-sm-7">
                        <select name="encryption" class="form-control">
                            @foreach (['tls' => 'TLS', 'ssl' => 'SSL', '' => 'Tanpa Enkripsi'] as $value => $label)
                                <option value="{{ $value }}" {{ old('encryption', $settings['encryption'] ?? 'tls') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Email Pengirim</label>
                    <div class="col-sm-7">
                        <input type="email" name="from_address" class="form-control" value="{{ old('from_address', $settings['from_address'] ?? '') }}" placeholder="no-reply@thesis.fikom.app">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Nama Pengirim</label>
                    <div class="col-sm-7">
                        <input type="text" name="from_name" class="form-control" value="{{ old('from_name', $settings['from_name'] ?? 'Thesis App FIKOM UMI') }}" placeholder="Thesis App FIKOM UMI">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-7 col-sm-offset-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
