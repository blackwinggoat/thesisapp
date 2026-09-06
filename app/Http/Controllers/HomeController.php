<?php

namespace App\Http\Controllers;

use App\Services\ProdiJenisTugasAkhirReportService;
use Barryvdh\DomPDF\Facade as PDF;
use Auth;
use DB;
use helper;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jenisTugasAkhirTrendCharts = [
            'series' => [],
            'by_cohort' => [],
            'by_academic_year' => [],
        ];
        $homeScopeLulusanPeriode = [];
        $homeScopeLulusanBidang = [];
        $homeStatusBimbinganProdi = (object) ['y' => '', 'PP' => 0, 'PUM' => 0, 'L' => 0];
        $homeRataLamaBimbinganProdiPerAngkatan = [];
        $homeDashboardScopeLabel = '';

        $user = auth()->user();
        $userLevel = (int) ($user->level ?? 0);
        if (in_array($userLevel, [2, 3, 5], true)) {
            $username = strtolower(trim((string) ($user->name ?? '')));
            $nimPrefix = null;
            $scopeUsername = $username;

            if ($userLevel === 5) {
                if (in_array($username, ['proditi', 'teknik informatika', 'ti'], true)) {
                    $nimPrefix = '130';
                    $homeDashboardScopeLabel = 'Teknik Informatika';
                } elseif (in_array($username, ['prodinyalilis', 'prodisi', 'sistem informasi', 'si'], true)) {
                    $nimPrefix = '131';
                    $homeDashboardScopeLabel = 'Sistem Informasi';
                }
            } else {
                $scopeUsername = '__all_programs__';
                $homeDashboardScopeLabel = 'Semua Program Studi';
            }

            try {
                $service = app(ProdiJenisTugasAkhirReportService::class);
                if ($userLevel === 5 && $nimPrefix !== null) {
                    $jenisTugasAkhirTrendCharts = $service->buildTrendCharts($nimPrefix);
                } elseif ($userLevel !== 5) {
                    $jenisTugasAkhirTrendCharts = $service
                        ->buildTrendChartsForPrograms(['130', '131']);
                }

                $homeScopeLulusanPeriode = $this->summarizeGraduatesByAcademicYear(
                    $jenisTugasAkhirTrendCharts
                );
            } catch (\Throwable $e) {
                Log::warning('Grafik kelulusan pada Home gagal dimuat.', [
                    'username' => $username,
                    'message' => $e->getMessage(),
                ]);
            }

            $homeScopeLulusanBidang = helper::getScopeTaLulusanBidangChartByUsername($scopeUsername);
            $homeStatusBimbinganProdi = helper::getStatusBimbinganSummaryProdiByUsername($scopeUsername);
            $homeRataLamaBimbinganProdiPerAngkatan = helper::getRataLamaProsesBimbinganProdiPerAngkatanByUsername($scopeUsername);
        }

        return view('/tugasakhir/layouts/content', compact(
            'jenisTugasAkhirTrendCharts',
            'homeScopeLulusanPeriode',
            'homeScopeLulusanBidang',
            'homeStatusBimbinganProdi',
            'homeRataLamaBimbinganProdiPerAngkatan',
            'homeDashboardScopeLabel'
        ));
    }

    protected function summarizeGraduatesByAcademicYear(array $trendCharts)
    {
        $seriesKeys = collect($trendCharts['series'] ?? [])->pluck('key')->filter()->values();

        return collect($trendCharts['by_academic_year'] ?? [])->map(function ($row) use ($seriesKeys) {
            $total = $seriesKeys->sum(function ($key) use ($row) {
                return (int) ($row[$key] ?? 0);
            });

            return [
                'y' => (string) ($row['period'] ?? '-'),
                'total' => $total,
            ];
        })->values()->all();
    }

    public function surat_sk_pembimbing($nomor)
    {
        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->whereRaw("REPLACE(mst_sk_pembimbing.nomor_sk, '/', '') = ?", [$nomor])
            ->get();

        if ($data_sk->isEmpty()) {
            return response('Data surat SK pembimbing tidak ditemukan.', 404);
        }

        $tgl_ujian = helper::tgl_indo_lengkap(date('Y-m-d'));

        return view('tugasakhir.fakultas.cetakskpembimbing', compact('data_sk', 'tgl_ujian'));
    }

    public function surat_sk_pembimbing_pdf($nomor)
    {
        $data_sk = DB::table('mst_sk_pembimbing as sk')
            ->join('trt_bimbingan as bimbingan', 'sk.bimbingan_id', '=', 'bimbingan.bimbingan_id')
            ->select([
                'sk.sk_pembimbing_id',
                'sk.nomor_sk',
                'sk.status as status_sk',
                'sk.created_at as sk_created_at',
                'bimbingan.bimbingan_id',
                'bimbingan.C_NPM',
                'bimbingan.pembimbing_I_id',
                'bimbingan.pembimbing_II_id',
                'bimbingan.judul',
                'bimbingan.jenis_tugas_akhir_id',
            ])
            ->whereRaw("REPLACE(sk.nomor_sk, '/', '') = ?", [$nomor])
            ->first();

        if (!$data_sk) {
            return response('Data surat SK pembimbing tidak ditemukan.', 404);
        }

        $safeNomorSk = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $data_sk->nomor_sk);

        return PDF::loadView('tugasakhir.fakultas.cetakskpembimbing_pdf', compact('data_sk'))
            ->setPaper('a4', 'portrait')
            ->stream('SK-Pembimbing-' . trim($safeNomorSk, '-') . '.pdf');
    }
}
