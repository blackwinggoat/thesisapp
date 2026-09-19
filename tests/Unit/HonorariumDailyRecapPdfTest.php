<?php

namespace Tests\Unit;

use App\Http\Controllers\KeuanganFakultas;
use App\Services\HonorariumDailyRecapVerificationService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class HonorariumDailyRecapPdfTest extends TestCase
{
    public function testDailyRecapPdfIsConnectedToSelectedDateWorkflow()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/KeuanganFakultas.php');
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $listView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium.blade.php');
        $pdfView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/rekap_honorarium_harian_pdf.blade.php');
        $letterheadView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/_honorarium_pdf_letterhead.blade.php');
        $verificationView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/verifikasi_rekap_honorarium_harian.blade.php');

        $this->assertStringContainsString('honorarium_rekap_harian_pdf', $controller);
        $this->assertStringContainsString('honorarium_rekap_pajak_pdf', $controller);
        $this->assertStringContainsString("honorarium_rekap_pdf(\$request, 'harian')", $controller);
        $this->assertStringContainsString("honorarium_rekap_pdf(\$request, 'pajak')", $controller);
        $this->assertStringContainsString('buildHonorariumDailyRecapReports', $controller);
        $this->assertStringContainsString('honorariumDenganJadwalQuery()', $controller);
        $this->assertStringContainsString('honorariumNeedsTypeAssignment', $controller);
        $this->assertStringContainsString("getPejabatFakultasByTanggal(\n            'Wakil Dekan II'", $controller);
        $this->assertStringContainsString('getDekanByTanggal($generatedAt->format', $controller);
        $this->assertStringContainsString("Route::post('/rekap-harian-pdf'", $routes);
        $this->assertStringContainsString("Route::post('/rekap-pajak-pdf'", $routes);
        $this->assertStringContainsString("Route::get('/verifikasi/rekap-honorarium/{token}'", $routes);
        $this->assertStringContainsString('Tanda Terima Dosen', $listView);
        $this->assertStringContainsString('Rekap Honorarium Harian', $listView);
        $this->assertStringContainsString('Rekap Pajak Honorarium', $listView);
        $this->assertStringContainsString('download-honorarium-daily-recap', $listView);
        $this->assertStringContainsString('download-honorarium-tax-recap', $listView);
        $this->assertStringContainsString("route('honorarium_rekap_harian_pdf')", $listView);
        $this->assertStringContainsString("route('honorarium_rekap_pajak_pdf')", $listView);
        $this->assertStringContainsString('honorarium-report-actions', $listView);
        $this->assertStringContainsString("$('#download-honorarium-tax-recap').prop('disabled', selected === 0)", $listView);
        $this->assertStringContainsString('@if ($includeDailyRecap)', $pdfView);
        $this->assertStringContainsString('@if ($includeTaxRecap)', $pdfView);
        $this->assertStringContainsString('REKAP HONORARIUM HARIAN', $pdfView);
        $this->assertStringContainsString('Rincian Jenis Ujian', $pdfView);
        $this->assertStringContainsString('Rekap Penerimaan Dosen', $pdfView);
        $this->assertStringContainsString('Honorarium Diterima', $pdfView);
        $this->assertStringContainsString('Tipe Ujian', $pdfView);
        $this->assertStringContainsString('Paraf', $pdfView);
        $this->assertStringContainsString('REKAP PAJAK HONORARIUM', $pdfView);
        $this->assertStringContainsString('Pajak (5%)', $pdfView);
        $this->assertStringContainsString('Penyesuaian', $pdfView);
        $this->assertStringContainsString('Honor Diterima = Honor - Pajak + Penyesuaian', $pdfView);
        $this->assertStringContainsString('$taxRows->slice($taxOffset, 20)->values()', $pdfView);
        $this->assertStringContainsString('$taxOffset += 20', $pdfView);
        $this->assertStringContainsString("'offset' => \$taxOffset", $pdfView);
        $this->assertStringContainsString('$lecturerSinglePageLimit = 21', $pdfView);
        $this->assertStringContainsString('$lecturerPageSize = 27', $pdfView);
        $this->assertStringContainsString('$lecturerRemaining - 2', $pdfView);
        $this->assertStringContainsString('$lecturerPage->offset + $loop->iteration', $pdfView);
        $this->assertStringContainsString('class="document honorarium-document{{ $loop->first', $pdfView);
        $this->assertStringContainsString('class="document tax-document{{ $loop->first', $pdfView);
        $this->assertStringNotContainsString('document-page-offset', $pdfView);
        $this->assertStringContainsString('.page-break { font-size: 0; height: 0; line-height: 0; page-break-before: always;', $pdfView);
        $this->assertSame(2, substr_count($pdfView, '<div class="page-break"></div>'));
        $this->assertStringNotContainsString('page-break-after: always', $pdfView);
        $this->assertStringContainsString('tax-document-needs-initial', $pdfView);
        $this->assertStringContainsString('tax-signature-bottom', $pdfView);
        $this->assertStringContainsString('class="main-page-initial">Paraf WD II</div>', $pdfView);
        $this->assertStringContainsString('class="tax-page-initial">Paraf WD II</div>', $pdfView);
        $this->assertStringContainsString('$lecturerChunks->count() > 1', $pdfView);
        $this->assertStringContainsString('$taxChunks->count() > 1 && !$loop->last', $pdfView);
        $this->assertStringContainsString('<div class="document-title">REKAP PAJAK HONORARIUM</div>', $pdfView);
        $this->assertStringNotContainsString('REKAP PAJAK HONORARIUM{{', $pdfView);
        $this->assertStringNotContainsString('REKAP PAJAK HONORARIUM - LANJUTAN', $pdfView);
        $this->assertStringContainsString('tax-document-continuation .section { margin-top: 0;', $pdfView);
        $this->assertStringContainsString('<th class="tax-count">Jumlah</th>', $pdfView);
        $this->assertStringNotContainsString('<th class="tax-student">Mahasiswa</th>', $pdfView);
        $this->assertStringContainsString('rincianPajakHonorarium', $controller);
        $this->assertStringNotContainsString('<th class="lecturer-assignment">Penugasan</th>', $pdfView);
        $this->assertStringContainsString("->setPaper('a4', 'portrait')", $controller);
        $this->assertStringContainsString('class="btn btn-danger" id="download-honorarium-daily-recap"', $listView);
        $this->assertStringContainsString('class="btn btn-danger" id="download-honorarium-tax-recap"', $listView);
        $this->assertStringContainsString('Wakil Dekan II Bidang Keuangan dan SDM', $pdfView);
        $this->assertStringContainsString('Makassar, {{ helper::tgl_indo_lengkap($generatedAt->format(\'Y-m-d\')) }}<br>Dekan,', $pdfView);
        $this->assertSame(2, substr_count($pdfView, "@include('tugasakhir.keuanganfakultas._honorarium_pdf_letterhead')"));
        $this->assertSame(1, substr_count($letterheadView, 'class="letterhead"'));
        $this->assertStringContainsString("publicImageDataUri('images/branding/umi-pdf.jpg')", $letterheadView);
        $this->assertStringContainsString("publicImageDataUri('images/branding/fikom-pdf.jpg')", $letterheadView);
        $this->assertStringContainsString('qrCodeDataUri($report->verification_url', $pdfView);
        $this->assertStringNotContainsString('QR digunakan untuk memeriksa metadata', $pdfView);
        $this->assertStringNotContainsString('Halaman verifikasi tidak menampilkan identitas mahasiswa', $pdfView);
        $this->assertStringContainsString('Identitas mahasiswa', $verificationView);
        $this->assertStringNotContainsString("['total_honor']", $verificationView);
    }

    public function testDailyRecapGroupsStudentsTypesLecturersAndAdjustedAmounts()
    {
        $controller = new KeuanganFakultas;
        $method = new \ReflectionMethod($controller, 'buildHonorariumDailyRecapReports');
        $method->setAccessible(true);
        $rows = collect([
            $this->honorariumRow('13020220001', 'Proposal', [
                'KS' => ['D1', 100000, 1],
                'PU' => ['D2', 200000, 1],
                'PP' => ['D3', 200000, 1],
            ], 0, 1),
            $this->honorariumRow('13120220001', 'Ujian Meja', [
                'P1' => ['D1', 75000, 1],
            ]),
        ]);
        $names = collect([
            'D1' => 'Dosen Satu',
            'D2' => 'Dosen Dua',
            'D3' => 'Dosen Tiga',
        ]);

        $signature = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        $reports = $method->invoke(
            $controller,
            $rows,
            $names,
            collect(['2026-08-19' => 50000]),
            collect(['D1' => $signature])
        );
        $report = $reports->first();

        $this->assertCount(1, $reports);
        $this->assertSame(2, $report->student_count);
        $this->assertSame(2, $report->exam_type_count);
        $this->assertSame(3, $report->lecturer_count);
        $this->assertSame(4, $report->assignment_count);
        $this->assertSame(575000.0, $report->total_honor);

        $proposal = $report->exam_types->firstWhere('name', 'Proposal');
        $this->assertSame(1, $proposal->student_count);
        $this->assertSame(3, $proposal->assignment_count);
        $this->assertSame(500000.0, $proposal->total_honor);
        $this->assertSame('Proposal', $proposal->exam_name);
        $this->assertSame('Proposal', $proposal->type_name);

        $dosenSatu = $report->lecturers->firstWhere('code', 'D1');
        $dosenDua = $report->lecturers->firstWhere('code', 'D2');
        $dosenTiga = $report->lecturers->firstWhere('code', 'D3');
        $this->assertSame(175000.0, $dosenSatu->total_honor);
        $this->assertSame(150000.0, $dosenDua->total_honor);
        $this->assertSame(250000.0, $dosenTiga->total_honor);
        $this->assertStringContainsString('Ketua Sidang (1)', $dosenSatu->roles);
        $this->assertStringContainsString('Penguji I (1)', $dosenSatu->roles);
        $this->assertStringStartsWith('data:image/png;base64,', $dosenSatu->signature_data_uri);
        $this->assertSame('', $dosenDua->signature_data_uri);

        $this->assertCount(2, $report->tax_lecturers);
        $this->assertSame(2, $report->tax_assignment_count);
        $this->assertSame(421052.0, $report->tax_total_honor);
        $this->assertSame(21052.0, $report->tax_total_amount);
        $this->assertSame(0.0, $report->tax_total_adjustment);
        $this->assertSame(400000.0, $report->tax_total_received);

        $taxPu = $report->tax_lecturers->firstWhere('lecturer_code', 'D2');
        $taxPp = $report->tax_lecturers->firstWhere('lecturer_code', 'D3');
        $this->assertSame('Pembimbing Utama', $taxPu->role);
        $this->assertSame(1, $taxPu->assignment_count);
        $this->assertSame(210526, $taxPu->honor);
        $this->assertSame(10526, $taxPu->tax);
        $this->assertSame(-50000, $taxPu->adjustment);
        $this->assertSame(150000, $taxPu->received);
        $this->assertSame('Pembimbing Pendamping', $taxPp->role);
        $this->assertSame(1, $taxPp->assignment_count);
        $this->assertSame(210526, $taxPp->honor);
        $this->assertSame(10526, $taxPp->tax);
        $this->assertSame(50000, $taxPp->adjustment);
        $this->assertSame(250000, $taxPp->received);
    }

    public function testVerificationTokenProtectsMetadataWithoutPublishingAmounts()
    {
        $report = (object) [
            'tanggal' => '2026-08-19',
            'student_nims' => ['13020220001' => true],
            'student_count' => 1,
            'lecturer_count' => 1,
            'assignment_count' => 1,
            'total_honor' => 100000,
            'exam_types' => collect([(object) [
                'name' => 'Proposal',
                'student_count' => 1,
                'assignment_count' => 1,
                'total_honor' => 100000,
            ]]),
            'lecturers' => collect([(object) [
                'code' => 'D1',
                'student_count' => 1,
                'assignment_count' => 1,
                'total_honor' => 100000,
            ]]),
        ];
        $official = (object) [
            'nama' => 'Pejabat Penguji',
            'nip_nidn' => '12345',
        ];
        $service = new HonorariumDailyRecapVerificationService;
        $token = $service->buildVerificationToken(
            collect([$report]),
            $official,
            Carbon::create(2026, 9, 9, 10, 0, 0),
            'test-signing-key'
        );
        $payload = $service->decodeVerificationToken($token, 'test-signing-key');

        $this->assertSame(['2026-08-19'], $payload['dates']);
        $this->assertSame(1, $payload['student_count']);
        $this->assertSame(1, $payload['lecturer_count']);
        $this->assertSame('Pejabat Penguji', $payload['signer_name']);
        $this->assertArrayNotHasKey('total_honor', $payload);
        $this->assertNull($service->decodeVerificationToken($token . 'tampered', 'test-signing-key'));
    }

    public function testTaxRecapAggregatesLecturerRolesAndReconcilesAdjustment()
    {
        $controller = new KeuanganFakultas;
        $method = new \ReflectionMethod($controller, 'buildHonorariumDailyRecapReports');
        $method->setAccessible(true);
        $rows = collect([
            $this->honorariumRow('13020220001', 'Ujian Meja', [
                'PU' => ['D2', 200000, 1],
                'PP' => ['D3', 200000, 1],
            ]),
            $this->honorariumRow('13020220002', 'Ujian Meja', [
                'PU' => ['D2', 200000, 1],
                'PP' => ['D3', 200000, 1],
            ], 0, 1),
        ]);

        $report = $method->invoke(
            $controller,
            $rows,
            collect(['D2' => 'Dosen Dua', 'D3' => 'Dosen Tiga']),
            collect(['2026-08-19' => 50000]),
            collect()
        )->first();

        $this->assertCount(2, $report->tax_lecturers);
        $pu = $report->tax_lecturers->first(function ($item) {
            return $item->lecturer_code === 'D2' && $item->role === 'Pembimbing Utama';
        });
        $pp = $report->tax_lecturers->first(function ($item) {
            return $item->lecturer_code === 'D3' && $item->role === 'Pembimbing Pendamping';
        });

        $this->assertSame(2, $pu->assignment_count);
        $this->assertSame(421052, $pu->honor);
        $this->assertSame(21052, $pu->tax);
        $this->assertSame(-50000, $pu->adjustment);
        $this->assertSame(350000, $pu->received);
        $this->assertSame($pu->received, $pu->honor - $pu->tax + $pu->adjustment);

        $this->assertSame(2, $pp->assignment_count);
        $this->assertSame(421052, $pp->honor);
        $this->assertSame(21052, $pp->tax);
        $this->assertSame(50000, $pp->adjustment);
        $this->assertSame(450000, $pp->received);
        $this->assertSame($pp->received, $pp->honor - $pp->tax + $pp->adjustment);

        $this->assertSame(842104.0, $report->tax_total_honor);
        $this->assertSame(42104.0, $report->tax_total_amount);
        $this->assertSame(0.0, $report->tax_total_adjustment);
        $this->assertSame(800000.0, $report->tax_total_received);
    }

    public function testFacultyOfficialMigrationSeedsTheCurrentLeadershipRoles()
    {
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_09_09_070000_create_and_seed_fikom_faculty_officials.php');
        $repairMigration = file_get_contents(__DIR__ . '/../../database/migrations/2026_09_09_080000_repair_fikom_faculty_officials.php');
        $helper = file_get_contents(__DIR__ . '/../../app/Helper.php');

        $this->assertStringContainsString("'jabatan' => 'Dekan'", $migration);
        $this->assertStringContainsString("'jabatan' => 'Wakil Dekan I'", $migration);
        $this->assertStringContainsString("'jabatan' => 'Wakil Dekan II'", $migration);
        $this->assertStringContainsString("'jabatan' => 'Wakil Dekan III'", $migration);
        $this->assertStringContainsString('Dr. Ir. Hj. Harlinda, MM., M.Kom., MTA.', $migration);
        $this->assertStringContainsString("->where('jabatan', \$official['jabatan'])", $migration);
        $this->assertStringContainsString("trim((string) \$current->nama) === ''", $repairMigration);
        $this->assertStringContainsString('officialPayload($official, false)', $repairMigration);
        $this->assertStringContainsString("trim((string) (\$item->nama ?? '')) !== ''", $helper);
    }

    private function honorariumRow($nim, $type, array $assignments, $puPresent = 1, $ppPresent = 1)
    {
        $row = (object) [
            'id' => crc32($nim . $type),
            'C_NPM' => $nim,
            'tanggal_ujian' => '2026-08-19',
            'exam_type' => $type === 'Proposal' ? 0 : 2,
            'tipe_ujian' => $type,
            'pembimbing_utama_hadir' => $puPresent,
            'pembimbing_pendamping_hadir' => $ppPresent,
        ];

        foreach (['KS', 'PU', 'PP', 'P1', 'P2', 'P3'] as $role) {
            $assignment = $assignments[$role] ?? ['', 0, 0];
            $row->{$role} = $assignment[0];
            $row->{$role . '_H'} = $assignment[1];
            $row->{$role . '_Stat'} = $assignment[2];
        }

        return $row;
    }
}
