<?php

namespace Tests\Unit;

use App\Helper;
use Tests\TestCase;

class AcademicSemesterScheduleTest extends TestCase
{
    public function testAcademicSemesterUsesTheApprovedGanjilAndGenapRanges()
    {
        $ganjilAwal = Helper::getSemesterAkademik('2026-09-01');
        $ganjilAkhir = Helper::getSemesterAkademik('28 Februari 2027');
        $genapAwal = Helper::getSemesterAkademik('2026-03-01');
        $genapAkhir = Helper::getSemesterAkademik('31 Agustus 2026');

        $this->assertSame('Ganjil', $ganjilAwal->semester);
        $this->assertSame('2026/2027', $ganjilAwal->tahun_akademik);
        $this->assertSame('2026-09-01', $ganjilAwal->start_date);
        $this->assertSame('2027-02-28', $ganjilAwal->end_date);
        $this->assertSame('Ganjil', $ganjilAkhir->semester);
        $this->assertSame('2026/2027', $ganjilAkhir->tahun_akademik);

        $this->assertSame('Genap', $genapAwal->semester);
        $this->assertSame('2025/2026', $genapAwal->tahun_akademik);
        $this->assertSame('2026-03-01', $genapAwal->start_date);
        $this->assertSame('2026-08-31', $genapAkhir->end_date);
    }

    public function testCorrespondenceAndBimbinganSummaryUseTheCentralSemesterRule()
    {
        $surat = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/surat_usulantimujian.blade.php');
        $suratLegacy = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/suratpengusulan_ujian.blade.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Prodi.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/prodi/dosen_pembimbing.blade.php');

        $this->assertSame('Ganjil', Helper::getNamaSemester('01 Februari 2027'));
        $this->assertSame('Genap', Helper::getNamaSemester('01 Maret 2026'));
        $this->assertSame('2025/2026', Helper::getPeriode('31 Agustus 2026'));
        $this->assertSame('2026/2027', Helper::getPeriode('01 September 2026'));
        $this->assertSame('Periode Semester 2025/2026 Genap', Helper::getPeriodeSemester('01 Maret 2026'));
        $this->assertStringContainsString('helper::getNamaSemester($tgl_ujian)', $surat);
        $this->assertStringContainsString('helper::getNamaSemester(isset($tgl) ? $tgl : null)', $suratLegacy);
        $this->assertStringContainsString('getRingkasanBimbinganPerDosen($semesterRange)', $controller);
        $this->assertStringContainsString("whereBetween('tb.created_at'", $controller);
        $this->assertStringNotContainsString('date("2021-")', $view);
        $this->assertStringNotContainsString('App\\Model\\trt_bimbingan', $view);
    }
}
