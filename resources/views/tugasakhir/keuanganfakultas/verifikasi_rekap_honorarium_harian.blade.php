<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Rekap Honorarium Harian</title>
    <style>
        body { background: #f4f6f8; color: #1f2933; font-family: Arial, sans-serif; margin: 0; }
        .page { margin: 48px auto; max-width: 720px; padding: 0 20px; }
        .document { background: #fff; border-top: 5px solid #16794a; box-shadow: 0 8px 24px rgba(31, 41, 51, 0.12); padding: 32px; }
        h1 { font-family: Georgia, serif; font-size: 28px; margin: 0 0 8px; }
        .verified { color: #147a39; font-size: 18px; font-weight: bold; margin: 0 0 28px; }
        table { border-collapse: collapse; width: 100%; }
        td { border-bottom: 1px solid #d9e1e7; padding: 11px 0; vertical-align: top; }
        td:first-child { color: #52606d; width: 42%; }
        .hash { font-family: Menlo, Consolas, monospace; font-size: 12px; overflow-wrap: anywhere; }
        .privacy { color: #627d98; font-size: 12px; line-height: 1.5; margin: 22px 0 0; }
        @media (max-width: 520px) {
            .page { margin: 24px auto; padding: 0 12px; }
            .document { padding: 24px 18px; }
            h1 { font-size: 24px; }
            td { display: block; width: 100% !important; }
            td:first-child { border-bottom: 0; padding-bottom: 2px; }
            td:last-child { padding-top: 2px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="document">
            <h1>Rekap Terverifikasi</h1>
            <p class="verified">Dokumen diterbitkan melalui Thesis App FIKOM UMI.</p>
            <table>
                <tr><td>Jenis Dokumen</td><td>Rekap Honorarium Harian</td></tr>
                <tr><td>Tanggal Ujian</td><td>{{ collect($payload['dates'])->map(function ($date) { return helper::tgl_indo_lengkap($date); })->implode(', ') }}</td></tr>
                <tr><td>Jumlah Mahasiswa</td><td>{{ number_format($payload['student_count']) }} mahasiswa</td></tr>
                <tr><td>Jumlah Dosen</td><td>{{ number_format($payload['lecturer_count']) }} dosen</td></tr>
                <tr><td>Jumlah Penugasan</td><td>{{ number_format($payload['assignment_count']) }} penugasan</td></tr>
                <tr><td>Pejabat Pengesah</td><td>{{ $payload['signer_name'] }}<br>Wakil Dekan II Bidang Keuangan dan SDM</td></tr>
                <tr><td>NIP/NIDN</td><td>{{ $payload['signer_id'] ?: '-' }}</td></tr>
                <tr><td>Waktu Penerbitan</td><td>{{ $payload['generated_at'] }} WITA</td></tr>
                <tr><td>Sidik Dokumen</td><td class="hash">{{ $payload['report_hash'] }}</td></tr>
            </table>
            <p class="privacy">Halaman ini hanya menampilkan metadata dokumen. Identitas mahasiswa, rincian penugasan dosen, dan nilai honorarium tidak dipublikasikan.</p>
        </section>
    </main>
</body>
</html>
