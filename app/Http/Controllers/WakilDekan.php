<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Services\ProdiJenisTugasAkhirReportService;
use App\Services\ReportTrendChartSvgRenderer;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Exception;
use DB;
use Illuminate\Support\Facades\Redirect;

class WakilDekan extends Controller
{
    public function ubah_password()
    {
        return view('tugasakhir.fakultas.ubah_password', [
            'passwordAction' => url('wakildekan/ubah_password'),
        ]);
    }

    public function ubah_password_post(Request $request)
    {
        if (!Hash::check($request->password_lama, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai');
        }

        if ($request->password_baru == $request->ulangi_password) {
            DB::update('update users set password = ? where id = ?', [Hash::make($request->password_baru), auth()->id()]);
            return redirect()->back()->with('success', 'Password Berhasil Diubah');
        }

        return redirect()->back()->with('error', 'Password Tidak Sama');
    }

    // Menampilkan SK Pembimbing
    public function sk_pembimbing()
    {
        $data_sk = DB::table('mst_sk_pembimbing')
            ->join('trt_bimbingan', 'mst_sk_pembimbing.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where("mst_sk_pembimbing.status", 0)
            ->orderBy('mst_sk_pembimbing.sk_pembimbing_id', 'DESC')
            ->get();
        return view('tugasakhir.wakildekan.sk_pembimbing', compact('data_sk'));
    }

    // Menampilkan Status Bimbingan Mahasiswa
    public function detail_status_bimbingan_mahasiswa($status)
    {
        $data = DB::table('trt_bimbingan')
            ->select("*")
            ->where('trt_bimbingan.status_bimbingan', $status)
            ->orderBy('updated_at', 'desc')
            ->get();

        $filterActionRoute = route('wakildekan.tampilDetailStatusBimbinganDenganFilterTanggal');
        $resetUrl = url('wakildekan/detail_status_bimbingan_mahasiswa/' . $status);

        return view('tugasakhir.prodi.detail_status_bimbingan_mahasiswa', compact('data', 'status', 'filterActionRoute', 'resetUrl'));
    }
    // Akhir Menampilkan Status Bimbingan Mahasiswa

    public function tampilDetailStatusBimbinganDenganFilterTanggal(Request $request)
    {
        $tanggalDari = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');
        $status = $request->input('status');

        $query = DB::table('trt_bimbingan')
            ->select('*')
            ->where('trt_bimbingan.status_bimbingan', $status);

        if (!empty($tanggalDari) && !empty($tanggalSampai)) {
            $query->whereBetween('trt_bimbingan.updated_at', [$tanggalDari, $tanggalSampai]);
        }

        $data = $query
            ->orderBy('updated_at', 'desc')
            ->get();

        $filterActionRoute = route('wakildekan.tampilDetailStatusBimbinganDenganFilterTanggal');
        $resetUrl = url('wakildekan/detail_status_bimbingan_mahasiswa/' . $status);

        return view('tugasakhir.prodi.detail_status_bimbingan_mahasiswa', compact('data', 'status', 'filterActionRoute', 'resetUrl'));
    }


    // Approve SK Pembimbing
    public function approve_sk_pembimbing($id)
    {
        try {
            DB::update('update mst_sk_pembimbing set status = 1 where sk_pembimbing_id = ?', [$id]);
            return redirect::to('wakildekan/sk_pembimbing')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('wakildekan/sk_pembimbing')->with('status', 'error');
        }
    }

    // Menampilkan SK Pembimbing
    public function sk_ujian_ta()
    {
        $data_sk = DB::table('mst_sk_penugasan')
            ->join('trt_bimbingan', 'mst_sk_penugasan.bimbingan_id', '=', 'trt_bimbingan.bimbingan_id')
            ->select('*')
            ->where("mst_sk_penugasan.status", 0)
            ->orderBy('mst_sk_penugasan.sk_penugasan_id', 'DESC')
            ->get();
        return view('tugasakhir.wakildekan.sk_ujian_ta', compact('data_sk'));
    }

    // Approve SK Pembimbing
    public function approve_sk_ujian_ta($id)
    {
        try {
            DB::update('update mst_sk_penugasan set status = 1 where sk_penugasan_id = ?', [$id]);
            return redirect::to('wakildekan/sk_ujian_ta')->with('status', 'success');
        } catch (Exception $exception) {
            return redirect::to('wakildekan/sk_ujian_ta')->with('status', 'error');
        }
    }

    public function report_jenis_tugas_akhir(Request $request)
    {
        $service = app(ProdiJenisTugasAkhirReportService::class);
        $programStudies = $this->jenisTugasAkhirProgramStudies();
        $reports = [];
        $filterState = ['mode' => [], 'periode' => []];
        $trendPayload = [];

        foreach ($programStudies as $programCode => $scope) {
            $report = $service->build(
                $programCode,
                $scope['program_studi'],
                $request->input('mode.' . $programCode, 'tahun_ajaran'),
                $request->input('periode.' . $programCode)
            );

            $reports[$programCode] = [
                'scope' => $scope,
                'report' => $report,
            ];
            $filterState['mode'][$programCode] = $report['mode'];
            $filterState['periode'][$programCode] = $report['selected_period'];
            $trendPayload[$programCode] = [
                'charts' => $report['trend_charts'],
                'active' => [
                    'by_cohort' => $report['mode'] === 'angkatan' ? $report['selected_period'] : null,
                    'by_academic_year' => $report['mode'] === 'tahun_ajaran' ? $report['selected_period'] : null,
                ],
            ];
        }

        return view('tugasakhir.wakildekan.report_jenis_tugas_akhir', compact(
            'reports',
            'filterState',
            'trendPayload'
        ));
    }

    public function report_jenis_tugas_akhir_pdf(Request $request)
    {
        $programStudies = $this->jenisTugasAkhirProgramStudies();
        $programCode = (string) $request->input('program_studi');
        if (!isset($programStudies[$programCode])) {
            abort(404);
        }

        $scope = $programStudies[$programCode];
        $service = app(ProdiJenisTugasAkhirReportService::class);
        $chartRenderer = app(ReportTrendChartSvgRenderer::class);
        $report = $service->build(
            $programCode,
            $scope['program_studi'],
            $request->input('mode', 'tahun_ajaran'),
            $request->input('periode')
        );
        $trendChartImages = [
            'by_cohort' => $chartRenderer->render(
                $report['trend_charts']['by_cohort'],
                $report['trend_charts']['series'],
                $report['mode'] === 'angkatan' ? $report['selected_period'] : null
            ),
            'by_academic_year' => $chartRenderer->render(
                $report['trend_charts']['by_academic_year'],
                $report['trend_charts']['series'],
                $report['mode'] === 'tahun_ajaran' ? $report['selected_period'] : null
            ),
        ];
        $kaprodi = Helper::getKaprodiByProdiAndTanggal($scope['program_studi'], Carbon::today());
        $verificationToken = $service->buildVerificationToken($report);
        $verificationUrl = route('verifikasi_report_jenis_tugas_akhir', ['token' => $verificationToken]);
        $emailProgramStudi = $programCode === '130'
            ? 's1.teknik.informatika@umi.ac.id'
            : 's1.sistem.informasi@umi.ac.id';
        $safePeriod = preg_replace('/[^0-9A-Za-z-]+/', '-', $report['selected_period']);
        $filename = 'Laporan-Persebaran-Jenis-TA-'
            . $programCode . '-'
            . ($safePeriod ?: date('Ymd')) . '.pdf';

        return PDF::loadView('tugasakhir.prodi.report_jenis_tugas_akhir_pdf', compact(
            'scope',
            'report',
            'trendChartImages',
            'kaprodi',
            'verificationUrl',
            'emailProgramStudi'
        ))
            ->setPaper('a4', 'landscape')
            ->stream($filename);
    }

    protected function jenisTugasAkhirProgramStudies()
    {
        return [
            '130' => [
                'nim_prefix' => '130',
                'program_studi' => 'Teknik Informatika',
                'can_select_program' => false,
            ],
            '131' => [
                'nim_prefix' => '131',
                'program_studi' => 'Sistem Informasi',
                'can_select_program' => false,
            ],
        ];
    }
}
