<?php

namespace App\Services;

use App\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProdiJenisTugasAkhirReportService
{
    public function build($nimPrefix, $programStudi, $mode = 'tahun_ajaran', $selectedPeriod = null)
    {
        $rows = $this->loadGraduatedStudents($nimPrefix);

        return $this->aggregate($rows, $programStudi, $mode, $selectedPeriod);
    }

    public function aggregate($rows, $programStudi, $mode = 'tahun_ajaran', $selectedPeriod = null)
    {
        $mode = in_array($mode, ['tahun_ajaran', 'angkatan'], true) ? $mode : 'tahun_ajaran';
        $rows = collect($rows)->map(function ($row) {
            return is_array($row) ? $row : (array) $row;
        })->values();

        $periodKey = $mode === 'angkatan' ? 'angkatan' : 'tahun_ajaran';
        $periodOptions = $this->sortPeriods(
            $rows->pluck($periodKey)->filter()->unique()->values()->all(),
            $mode
        );

        $selectedPeriod = trim((string) $selectedPeriod);
        if ($selectedPeriod === '' || !in_array($selectedPeriod, $periodOptions, true)) {
            $selectedPeriod = $periodOptions[0] ?? '';
        }

        $selectedRows = $rows->filter(function ($row) use ($periodKey, $selectedPeriod) {
            return (string) ($row[$periodKey] ?? '') === $selectedPeriod;
        })->sortByDesc(function ($row) {
            return implode('|', [
                isset($row['tanggal_lulus']) ? (string) $row['tanggal_lulus'] : '',
                isset($row['nim']) ? (string) $row['nim'] : '',
            ]);
        })->values();

        $typeColumns = $rows->groupBy(function ($row) {
            return (string) ($row['jenis_code'] ?? 'TA-SM');
        })->map(function ($typeRows, $code) {
            $first = $typeRows->first();

            return [
                'code' => $code,
                'description' => $first['jenis_description'] ?? $code,
            ];
        })->sortKeys()->values();

        $totalSelected = $selectedRows->count();
        $distribution = $typeColumns->map(function ($type, $index) use ($selectedRows, $totalSelected) {
            $count = $selectedRows->where('jenis_code', $type['code'])->count();

            return array_merge($type, [
                'count' => $count,
                'percentage' => $totalSelected > 0 ? round(($count / $totalSelected) * 100, 2) : 0,
                'tone' => 'tone-' . (($index % 6) + 1),
            ]);
        })->filter(function ($type) {
            return $type['count'] > 0;
        })->sortByDesc('count')->values();

        $comparison = collect($periodOptions)->map(function ($period) use ($rows, $periodKey, $typeColumns) {
            $periodRows = $rows->filter(function ($row) use ($periodKey, $period) {
                return (string) ($row[$periodKey] ?? '') === (string) $period;
            });
            $total = $periodRows->count();
            $counts = [];

            foreach ($typeColumns as $type) {
                $count = $periodRows->where('jenis_code', $type['code'])->count();
                $counts[$type['code']] = [
                    'count' => $count,
                    'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0,
                ];
            }

            return [
                'period' => $period,
                'total' => $total,
                'counts' => $counts,
            ];
        })->values();

        $dominant = $distribution->first();
        $fallbackDateCount = $selectedRows->where('tanggal_source', 'tidak_diketahui')->count();
        $dateSourceCounts = $selectedRows->groupBy(function ($row) {
            return (string) ($row['tanggal_source'] ?? 'tidak_diketahui');
        })->map(function ($sourceRows) {
            return $sourceRows->count();
        })->all();
        $defaultTypeCount = $selectedRows->where('jenis_default', true)->count();
        $generatedAt = Carbon::now();

        $report = [
            'program_studi' => $programStudi,
            'mode' => $mode,
            'mode_label' => $mode === 'angkatan' ? 'Angkatan' : 'Tahun Ajaran',
            'selected_period' => $selectedPeriod,
            'period_options' => $periodOptions,
            'type_columns' => $typeColumns->all(),
            'distribution' => $distribution->all(),
            'comparison' => $comparison->all(),
            'rows' => $selectedRows->all(),
            'generated_at' => $generatedAt,
            'summary' => [
                'total' => $totalSelected,
                'total_all_periods' => $rows->count(),
                'type_count' => $distribution->count(),
                'dominant_code' => $dominant['code'] ?? '-',
                'dominant_percentage' => $dominant['percentage'] ?? 0,
                'fallback_date_count' => $fallbackDateCount,
                'default_type_count' => $defaultTypeCount,
                'date_source_counts' => $dateSourceCounts,
            ],
        ];
        $report['report_hash'] = $this->buildReportHash($report);

        return $report;
    }

    public function buildVerificationToken(array $report, $key = null)
    {
        $key = $this->resolveSigningKey($key);
        $generatedAt = $report['generated_at'] instanceof Carbon
            ? $report['generated_at']
            : Carbon::parse($report['generated_at']);
        $payload = [
            'version' => 1,
            'program_studi' => (string) $report['program_studi'],
            'mode' => (string) $report['mode'],
            'mode_label' => (string) $report['mode_label'],
            'period' => (string) $report['selected_period'],
            'total' => (int) $report['summary']['total'],
            'report_hash' => (string) $report['report_hash'],
            'generated_at' => $generatedAt->format('Y-m-d H:i:s'),
        ];
        $encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', $encoded, $key);

        return $encoded . '.' . $signature;
    }

    public function decodeVerificationToken($token, $key = null)
    {
        $parts = explode('.', (string) $token, 2);
        if (count($parts) !== 2) {
            return null;
        }

        list($encoded, $signature) = $parts;
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $encoded) || !preg_match('/^[a-f0-9]{64}$/', $signature)) {
            return null;
        }

        $expected = hash_hmac('sha256', $encoded, $this->resolveSigningKey($key));
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $base64Payload = strtr($encoded, '-_', '+/');
        $base64Payload .= str_repeat('=', (4 - strlen($base64Payload) % 4) % 4);
        $decoded = base64_decode($base64Payload, true);
        if ($decoded === false) {
            return null;
        }

        $payload = json_decode($decoded, true);

        if (!is_array($payload)
            || (int) ($payload['version'] ?? 0) !== 1
            || !in_array($payload['mode'] ?? '', ['tahun_ajaran', 'angkatan'], true)
            || !preg_match('/^[a-f0-9]{64}$/', (string) ($payload['report_hash'] ?? ''))
            || (int) ($payload['total'] ?? -1) < 0) {
            return null;
        }

        return $payload;
    }

    protected function loadGraduatedStudents($nimPrefix)
    {
        $nimLike = preg_match('/^\d{3}$/', (string) $nimPrefix)
            ? $nimPrefix . '%'
            : '__invalid_prodi__%';

        $examDates = DB::table('trt_jadwal_ujian_per_mhs as jpm')
            ->join('trt_jadwal_ujian as ju', 'ju.id', '=', 'jpm.jadwal_ujian')
            ->join('mst_pendaftaran as mp', 'mp.pendaftaran_id', '=', 'ju.pendaftaran_id')
            ->where('jpm.C_NPM', 'LIKE', $nimLike)
            ->whereIn('mp.tipe_ujian', [2, 3])
            ->whereDate('ju.tgl_ujian', '<=', Carbon::today()->format('Y-m-d'))
            ->select('jpm.C_NPM as nim', DB::raw('MAX(ju.tgl_ujian) as tanggal_ujian'))
            ->groupBy('jpm.C_NPM')
            ->pluck('tanggal_ujian', 'nim');

        $resultDates = DB::table('trt_hasil as hasil')
            ->join('trt_reg as reg', 'reg.reg_id', '=', 'hasil.reg_id')
            ->join('trt_bimbingan as bimbingan', 'bimbingan.bimbingan_id', '=', 'reg.bimbingan_id')
            ->where('bimbingan.C_NPM', 'LIKE', $nimLike)
            ->where('reg.status', 2)
            ->select('bimbingan.C_NPM as nim', DB::raw('MAX(COALESCE(hasil.updated_at, hasil.created_at)) as tanggal_hasil'))
            ->groupBy('bimbingan.C_NPM')
            ->pluck('tanggal_hasil', 'nim');

        return DB::table('trt_bimbingan as tb')
            ->join('t_mst_mahasiswa as mhs', 'mhs.C_NPM', '=', 'tb.C_NPM')
            ->leftJoin('mst_jenis_tugas_akhir as jta', 'jta.jenis_tugas_akhir_id', '=', 'tb.jenis_tugas_akhir_id')
            ->where('tb.status_bimbingan', 3)
            ->where('tb.C_NPM', 'LIKE', $nimLike)
            ->select([
                'tb.bimbingan_id',
                'tb.C_NPM as nim',
                'mhs.NAMA_MAHASISWA as nama',
                'mhs.TGL_LULUS as tanggal_lulus_master',
                'tb.judul',
                'tb.jenis_tugas_akhir_id',
                'tb.last_update',
                'tb.updated_at',
                'tb.created_at',
                'jta.kode_jenis_tugas_akhir as jenis_code',
                'jta.deskripsi as jenis_description',
            ])
            ->orderBy('tb.bimbingan_id', 'desc')
            ->get()
            ->unique('nim')
            ->map(function ($row) use ($examDates, $resultDates) {
                $examDate = $this->validDate($examDates->get($row->nim));
                $masterDate = $this->validDate($row->tanggal_lulus_master);
                $resultDate = $this->validDate($resultDates->get($row->nim));
                $statusDate = $this->firstValidDate([$row->last_update, $row->updated_at, $row->created_at]);
                $graduationDate = $examDate ?: ($masterDate ?: ($resultDate ?: $statusDate));
                $dateSource = $examDate
                    ? 'jadwal_ujian'
                    : ($masterDate
                        ? 'master_mahasiswa'
                        : ($resultDate ? 'hasil_ujian_ta' : ($statusDate ? 'status_kelulusan' : 'tidak_diketahui')));
                $typeCode = trim((string) $row->jenis_code);
                $isDefaultType = $typeCode === '';

                if ($isDefaultType) {
                    $typeCode = 'TA-SM';
                }

                return [
                    'nim' => (string) $row->nim,
                    'nama' => trim((string) $row->nama),
                    'judul' => trim((string) $row->judul),
                    'jenis_code' => $typeCode,
                    'jenis_description' => trim((string) $row->jenis_description) ?: $this->defaultTypeDescription($typeCode),
                    'jenis_default' => $isDefaultType,
                    'angkatan' => $this->cohortFromNim($row->nim),
                    'tahun_ajaran' => $graduationDate ? Helper::getSemesterAkademik($graduationDate)->tahun_akademik : 'Tidak diketahui',
                    'tanggal_lulus' => $graduationDate ?: '',
                    'tanggal_lulus_label' => $graduationDate ? Helper::tgl_indo_lengkap($graduationDate) : '-',
                    'tanggal_source' => $dateSource,
                ];
            })
            ->values();
    }

    protected function sortPeriods(array $periods, $mode)
    {
        usort($periods, function ($left, $right) use ($mode) {
            if ($left === 'Tidak diketahui') {
                return 1;
            }
            if ($right === 'Tidak diketahui') {
                return -1;
            }

            $leftYear = (int) substr((string) $left, 0, 4);
            $rightYear = (int) substr((string) $right, 0, 4);
            if ($leftYear === $rightYear) {
                return strcmp((string) $right, (string) $left);
            }

            return $rightYear <=> $leftYear;
        });

        return array_values($periods);
    }

    protected function buildReportHash(array $report)
    {
        $evidence = collect($report['rows'])->map(function ($row) {
            return [
                'nim' => (string) ($row['nim'] ?? ''),
                'jenis' => (string) ($row['jenis_code'] ?? ''),
                'tanggal' => (string) ($row['tanggal_lulus'] ?? ''),
            ];
        })->sortBy('nim')->values()->all();

        return hash('sha256', json_encode([
            'program_studi' => $report['program_studi'],
            'mode' => $report['mode'],
            'period' => $report['selected_period'],
            'evidence' => $evidence,
        ]));
    }

    protected function cohortFromNim($nim)
    {
        $cohort = substr(preg_replace('/\D+/', '', (string) $nim), 3, 4);
        $year = (int) $cohort;

        return preg_match('/^\d{4}$/', $cohort) && $year >= 1990 && $year <= ((int) date('Y') + 1)
            ? $cohort
            : 'Tidak diketahui';
    }

    protected function firstValidDate(array $dates)
    {
        foreach ($dates as $date) {
            $valid = $this->validDate($date);
            if ($valid) {
                return $valid;
            }
        }

        return null;
    }

    protected function validDate($date)
    {
        $candidate = substr(trim((string) $date), 0, 10);
        if ($candidate === '' || $candidate === '0000-00-00') {
            return null;
        }

        try {
            return Carbon::parse($candidate)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function defaultTypeDescription($code)
    {
        $descriptions = [
            'TA-SM' => 'Tugas Akhir Skripsi Mandiri',
            'TA-SK' => 'Tugas Akhir Skripsi Kolaborasi',
            'NS-AI' => 'Non Skripsi - Artikel Ilmiah',
            'NS-KT' => 'Non Skripsi - Karya Teknologi',
            'NS-KP' => 'Non Skripsi - Karya Profesional',
            'NS-AR' => 'Non Skripsi - Artikel Review',
        ];

        return $descriptions[$code] ?? $code;
    }

    protected function resolveSigningKey($key)
    {
        $key = trim((string) $key);
        if ($key !== '') {
            return $key;
        }

        return (string) config('app.key');
    }
}
