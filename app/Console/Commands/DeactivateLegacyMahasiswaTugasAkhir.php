<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeactivateLegacyMahasiswaTugasAkhir extends Command
{
    protected $signature = 'thesis:deactivate-legacy-mahasiswa
        {--before=2018 : Angkatan sebelum tahun ini akan diperiksa}
        {--apply : Jalankan perubahan status setelah backup database diverifikasi}';

    protected $description = 'Deprecated: mass student deactivation is disabled';

    public function handle()
    {
        $this->warn('Perintah penonaktifan mahasiswa telah dinonaktifkan. Semua mahasiswa non-lulusan diperlakukan sebagai aktif.');

        return 0;
    }
}
