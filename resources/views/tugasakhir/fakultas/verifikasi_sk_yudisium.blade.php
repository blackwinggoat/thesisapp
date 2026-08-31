<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi SK Yudisium</title>
    <style>
        body { background: #f4f6f8; color: #1f2933; font-family: Arial, sans-serif; margin: 0; }
        .page { margin: 48px auto; max-width: 680px; padding: 0 20px; }
        .document { background: #fff; border-top: 5px solid #14823b; box-shadow: 0 8px 24px rgba(31, 41, 51, 0.12); padding: 32px; }
        h1 { font-family: Georgia, serif; font-size: 28px; margin: 0 0 8px; }
        .verified { color: #147a39; font-size: 18px; font-weight: bold; margin: 0 0 28px; }
        table { border-collapse: collapse; width: 100%; }
        td { border-bottom: 1px solid #d9e1e7; padding: 11px 0; vertical-align: top; }
        td:first-child { color: #52606d; width: 42%; }
        @media (max-width: 520px) {
            .page { margin: 24px auto; padding: 0 12px; }
            .document { padding: 24px 18px; }
            h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="document">
            <h1>SK Yudisium Terverifikasi</h1>
            <p class="verified">Dokumen ini diterbitkan oleh Fakultas Ilmu Komputer UMI.</p>
            <table>
                <tr><td>Nomor Surat</td><td>{{ $dokumen->nomor_surat }}</td></tr>
                <tr><td>Program Studi</td><td>{{ $programStudi }}</td></tr>
                <tr><td>Tanggal Ujian</td><td>{{ helper::tgl_indo_lengkap($dokumen->tanggal_ujian) }}</td></tr>
                <tr><td>Jumlah Mahasiswa</td><td>{{ $jumlahMahasiswa }} mahasiswa</td></tr>
            </table>
        </section>
    </main>
</body>
</html>
