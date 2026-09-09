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
        $verificationView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/verifikasi_rekap_honorarium_harian.blade.php');

        $this->assertStringContainsString('honorarium_rekap_harian_pdf', $controller);
        $this->assertStringContainsString('buildHonorariumDailyRecapReports', $controller);
        $this->assertStringContainsString('honorariumDenganJadwalQuery()', $controller);
        $this->assertStringContainsString('honorariumNeedsTypeAssignment', $controller);
        $this->assertStringContainsString("getPejabatFakultasByTanggal(\n            'Wakil Dekan II'", $controller);
        $this->assertStringContainsString("Route::post('/rekap-harian-pdf'", $routes);
        $this->assertStringContainsString("Route::get('/verifikasi/rekap-honorarium/{token}'", $routes);
        $this->assertStringContainsString('Tanda Terima Dosen', $listView);
        $this->assertStringContainsString('Rekap Honorarium Harian', $listView);
        $this->assertStringContainsString('download-honorarium-daily-recap', $listView);
        $this->assertStringContainsString("route('honorarium_rekap_harian_pdf')", $listView);
        $this->assertStringContainsString('REKAP HONORARIUM HARIAN', $pdfView);
        $this->assertStringContainsString('Rincian Jenis Ujian', $pdfView);
        $this->assertStringContainsString('Rekap Penerimaan Dosen', $pdfView);
        $this->assertStringContainsString('Honorarium Diterima', $pdfView);
        $this->assertStringContainsString('Wakil Dekan II Bidang Keuangan dan SDM', $pdfView);
        $this->assertStringContainsString("publicImageDataUri('images/branding/umi-pdf.jpg')", $pdfView);
        $this->assertStringContainsString("publicImageDataUri('images/branding/fikom-pdf.jpg')", $pdfView);
        $this->assertStringContainsString('qrCodeDataUri($report->verification_url', $pdfView);
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

        $reports = $method->invoke($controller, $rows, $names, collect([
            '2026-08-19' => 50000,
        ]));
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

        $dosenSatu = $report->lecturers->firstWhere('code', 'D1');
        $dosenDua = $report->lecturers->firstWhere('code', 'D2');
        $dosenTiga = $report->lecturers->firstWhere('code', 'D3');
        $this->assertSame(175000.0, $dosenSatu->total_honor);
        $this->assertSame(150000.0, $dosenDua->total_honor);
        $this->assertSame(250000.0, $dosenTiga->total_honor);
        $this->assertStringContainsString('Ketua Sidang (1)', $dosenSatu->roles);
        $this->assertStringContainsString('Penguji I (1)', $dosenSatu->roles);
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
