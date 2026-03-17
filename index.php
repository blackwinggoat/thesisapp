<?php
http_response_code(503);
header('Content-Type: text/html; charset=UTF-8');
header('Retry-After: 3600');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thesis Apps Under Maintenance</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Trebuchet MS", Tahoma, sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e293b 55%, #334155);
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .panel {
            width: 100%;
            max-width: 680px;
            background: rgba(15, 23, 42, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            padding: 40px 32px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #f59e0b;
            color: #111827;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        h1 {
            margin: 20px 0 12px;
            font-size: 38px;
            line-height: 1.15;
        }

        p {
            margin: 0 auto 12px;
            max-width: 520px;
            font-size: 17px;
            line-height: 1.7;
            color: #cbd5e1;
        }

        .small {
            margin-top: 24px;
            font-size: 14px;
            color: #94a3b8;
        }

        @media (max-width: 640px) {
            .panel {
                padding: 32px 22px;
            }

            h1 {
                font-size: 30px;
            }

            p {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="panel">
        <span class="badge">Maintenance</span>
        <h1>Thesis Apps Under Maintenance</h1>
        <p>
            Sistem sedang dalam proses pemeliharaan dan peningkatan layanan.
            Silakan coba kembali beberapa saat lagi.
        </p>
        <p>
            Mohon maaf atas ketidaknyamanannya. Terima kasih atas pengertiannya.
        </p>
        <div class="small">HTTP 503 - Service Temporarily Unavailable</div>
    </div>
</body>
</html>
