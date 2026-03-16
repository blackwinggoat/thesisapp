
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

        .password-wrapper {
            position: relative;
            display: inline-block;
        }

        .password-input {
            margin-left: 20px;
            padding: 2px;
            padding-right: 36px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 6px;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            padding: 0;
            cursor: pointer;
            color: #444;
        }

        .toggle-password:focus {
            outline: none;
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
            <div class="password-wrapper">
                <input required class="password-input" id="password" name="password" placeholder="Enter password" type="password">
                <button class="toggle-password" type="button" id="togglePassword" aria-label="Tampilkan password">
                    <svg id="eyeOpen" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M1 12C3.5 7 7.5 4.5 12 4.5C16.5 4.5 20.5 7 23 12C20.5 17 16.5 19.5 12 19.5C7.5 19.5 3.5 17 1 12Z" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <svg id="eyeClosed" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="display:none;">
                        <path d="M3 3L21 21" stroke="currentColor" stroke-width="2"/>
                        <path d="M10.6 10.7C10.2 11.1 10 11.5 10 12C10 13.1 10.9 14 12 14C12.5 14 12.9 13.8 13.3 13.4" stroke="currentColor" stroke-width="2"/>
                        <path d="M9.4 5.3C10.2 5 11.1 4.8 12 4.8C16.4 4.8 20.4 7.2 23 12C21.8 14.2 20.3 15.9 18.6 17" stroke="currentColor" stroke-width="2"/>
                        <path d="M6.2 6.2C4.2 7.5 2.4 9.4 1 12C3.6 16.8 7.6 19.2 12 19.2C13.7 19.2 15.2 18.8 16.6 18" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            </div><br>
            <button type="submit" class="button-login">Login</button>
        </form>
    </div>
</body>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    togglePassword.addEventListener('click', function () {
        const isHidden = passwordInput.getAttribute('type') === 'password';
        passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
        eyeOpen.style.display = isHidden ? 'none' : 'block';
        eyeClosed.style.display = isHidden ? 'block' : 'none';
        togglePassword.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
    });
</script>

</html>
