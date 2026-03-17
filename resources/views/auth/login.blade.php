
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Login | SIMPRODI - Tugas Akhir</title>
    <style>
        body {
            background : url("{{ asset('img/bg3@2x.png') }}") no-repeat center center fixed;
            background-size: contain;
            background-size: cover;
        }

        .gambar{
            background : url("{{ asset('img/thesis1@2x.png') }}");
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            height: 490px;
            width: 490px;
            margin: 0 auto;
            opacity: 1;
            transition: 2s;
            animation-name: fadeInOpacity;
            animation-iteration-count: 1;
            animation-timing-function: ease-in;
            animation-duration: 2s;
        }

        @keyframes  fadeInOpacity {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }


        .button-login {
            margin-left: 209px;
            margin-top: 2px;
            background: #CFA323;
            padding-left: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
            padding-right: 10px;
            border: none;
            color: black;
        }

        .password-group {
            display: inline-flex;
            align-items: center;
            margin-left: 20px;
        }

        .password-field {
            padding: 2px;
            border: 1px solid #ccc;
        }

        .toggle-password {
            margin-left: 6px;
            padding: 2px 8px;
            border: 1px solid #cfcfcf;
            background: #ffffff;
            color: #333333;
            cursor: pointer;
            line-height: 1.2;
        }
    </style>
</head>

<body>
    <br><br><br>
        <div class="gambar">
        <form role = "form"  action="{{ route('login') }}" aria-label="{{ __('Login') }}" method="POST">
            @csrf
            <label style="color:white;margin-top:280px;margin-left:102px;" for="">USERNAME</label>
            <input required placeholder="Enter username" style="margin-left:24px;padding:2px;" name="email" ><br>    
            <label style="color:white;margin-left:102px;" for="">PASSWORD</label>
            <span class="password-group">
                <input required id="password" class="password-field" name="password" placeholder="Enter password" type="password">
                <button type="button" id="togglePassword" class="toggle-password" aria-label="Show password" title="Show password">
                    <span id="togglePasswordText">Show</span>
                </button>
            </span><br>
            <button type="submit" class="button-login">Login</button>
        </form>
    </div>
</body>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var passwordInput = document.getElementById('password');
        var togglePassword = document.getElementById('togglePassword');
        var togglePasswordText = document.getElementById('togglePasswordText');

        if (!passwordInput || !togglePassword) {
            return;
        }

        togglePassword.addEventListener('click', function () {
            var showPassword = passwordInput.getAttribute('type') === 'password';

            passwordInput.setAttribute('type', showPassword ? 'text' : 'password');
            togglePassword.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            togglePassword.setAttribute('title', showPassword ? 'Hide password' : 'Show password');
            togglePasswordText.textContent = showPassword ? 'Hide' : 'Show';
        });
    });
</script>

</html>
