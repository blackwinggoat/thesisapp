<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AkademikProdiMahasiswaPerformanceTest extends TestCase
{
    public function testStudentListUsesServerPaginationAndBatchedAccountStatus()
    {
        $controller = file_get_contents(
            __DIR__ . '/../../app/Http/Controllers/AkademikProdi.php'
        );
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/akademikprodi/mahasiswa.blade.php'
        );

        $this->assertStringContainsString('public function mahasiswa(Request $request)', $controller);
        $this->assertStringContainsString('->paginate($perPage)', $controller);
        $this->assertStringContainsString("->whereIn('name', \$pageNims)", $controller);
        $this->assertStringContainsString("'t_mst_mahasiswa.TAHUN_MASUK'", $controller);
        $this->assertStringNotContainsString('getStatusAkunPerMahasiswa', $view);
        $this->assertStringNotContainsString('id="datatable-example"', $view);
        $this->assertStringContainsString('{{ $data->links() }}', $view);
        $this->assertStringContainsString("url('akademikprodi/mahasiswa')", $view);
    }
}
