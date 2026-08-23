<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Reset Password | Thesis App FIKOM UMI</title>
    <style>
        body {
            min-height: 100vh;
            background: url("{{ asset('img/bg3@2x.png') }}") no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .reset-card {
            width: 100%;
            max-width: 480px;
            background: rgba(255, 255, 255, .96);
            border-radius: 8px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .24);
            padding: 30px;
        }

        .reset-title {
            margin: 0;
            color: #1F2937;
            font-size: 24px;
            font-weight: 700;
            line-height: 1.25;
        }

        .reset-subtitle {
            margin: 7px 0 22px;
            color: #64748B;
            font-size: 14px;
            line-height: 1.55;
        }

        .form-control {
            height: 42px;
            border-radius: 5px;
        }

        .btn-reset {
            width: 100%;
            height: 42px;
            background: #CFA323;
            border: none;
            border-radius: 5px;
            color: #111827;
            font-weight: 700;
        }

        .login-link {
            display: block;
            margin-top: 16px;
            text-align: center;
            color: #1F6F8B;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <h1 class="reset-title">Buat Password Baru</h1>
        <p class="reset-subtitle">Gunakan password baru yang kuat dan tidak mudah ditebak.</p>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.request') }}" aria-label="Reset password">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Email terdaftar</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password baru</label>
                <input id="password" type="password" class="form-control" name="password" required>
            </div>

            <div class="form-group">
                <label for="password-confirm">Konfirmasi password baru</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn-reset">Simpan Password Baru</button>
        </form>

        <a class="login-link" href="{{ route('login') }}">Kembali ke halaman login</a>
    </div>
</body>
</html>
