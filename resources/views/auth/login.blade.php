
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
            <input required style="margin-left:20px;padding:2px;" name="password"  placeholder="Enter password" type="password"><br>
            <button type="submit" class="button-login">Login</button>
        </form>
    </div>
</body>
<script src="{{ asset('js/app.js') }}"></script>

</html>