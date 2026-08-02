<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeactivateLegacyMahasiswaTugasAkhir extends Command
{
    protected $signature = 'thesis:deactivate-legacy-mahasiswa
        {--before=2018 : Angkatan sebelum tahun ini akan diperiksa}
        {--apply : Jalankan perubahan status setelah backup database diverifikasi}';

    protected $description = 'Deactivate legacy active students that have not graduated in ThesisApps';

    public function handle()
    {
        $before = (int) $this->option('before');
        if ($before < 1900 || $before > 2100) {
            throw new RuntimeException('The --before option must be a valid academic year.');
        }

        $candidates = $this->candidateQuery($before);
        $perAngkatan = (clone $candidates)
            ->selectRaw('m.TAHUN_MASUK, COUNT(*) AS total')
            ->groupBy('m.TAHUN_MASUK')
            ->orderBy('m.TAHUN_MASUK')
            ->get();
        $nims = (clone $candidates)
            ->orderBy('m.C_NPM')
            ->pluck('m.C_NPM');
        $bimbinganAktif = $this->countBimbinganYangAkanDinonaktifkan($nims);

        $this->line('Batas angkatan: sebelum ' . $before);
        $this->line('Mahasiswa aktif yang belum lulus: ' . $nims->count());
        $this->line('Bimbingan proses yang akan menjadi Non Aktif: ' . $bimbinganAktif);
        foreach ($perAngkatan as $row) {
            $this->line('Angkatan ' . $row->TAHUN_MASUK . ': ' . $row->total);
        }

        if (!$this->option('apply')) {
            $this->warn('Dry run only. Re-run with --apply after a verified database backup.');

            return 0;
        }

        $hasil = DB::transaction(function () use ($before) {
            $targetNims = $this->candidateQuery($before)
                ->lockForUpdate()
                ->orderBy('m.C_NPM')
                ->pluck('m.C_NPM');
            $masterUpdated = 0;
            $bimbinganUpdated = 0;

            foreach ($targetNims->chunk(250) as $chunk) {
                $masterUpdated += DB::table('t_mst_mahasiswa')
                    ->whereIn('C_NPM', $chunk->all())
                    ->where('C_KODE_STATUS_AKTIF_MHS', 'A')
                    ->update(['C_KODE_STATUS_AKTIF_MHS' => 'N']);

                $bimbinganUpdated += DB::table('trt_bimbingan')
                    ->whereIn('C_NPM', $chunk->all())
                    ->whereNotIn('status_bimbingan', [3, 4])
                    ->update(['status_bimbingan' => 4]);
            }

            return compact('masterUpdated', 'bimbinganUpdated');
        });

        $this->info('Mahasiswa dinonaktifkan: ' . $hasil['masterUpdated']);
        $this->info('Bimbingan diubah menjadi Non Aktif: ' . $hasil['bimbinganUpdated']);

        return 0;
    }

    private function candidateQuery($before)
    {
        return DB::table('t_mst_mahasiswa as m')
            ->where('m.C_KODE_STATUS_AKTIF_MHS', 'A')
            ->whereBetween('m.TAHUN_MASUK', ['1000', '9999'])
            ->whereRaw('CAST(m.TAHUN_MASUK AS UNSIGNED) < ?', [$before])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('trt_bimbingan as tb')
                    ->whereRaw('tb.C_NPM = m.C_NPM')
                    ->where('tb.status_bimbingan', 3);
            });
    }

    private function countBimbinganYangAkanDinonaktifkan($nims)
    {
        $count = 0;
        foreach ($nims->chunk(250) as $chunk) {
            $count += DB::table('trt_bimbingan')
                ->whereIn('C_NPM', $chunk->all())
                ->whereNotIn('status_bimbingan', [3, 4])
                ->count();
        }

        return $count;
    }
}
