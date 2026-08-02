<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MahasiswaTopikViewTest extends TestCase
{
    public function testTopikViewHandlesBimbinganWithoutAnActiveTopik()
    {
        $controller = file_get_contents(
            __DIR__ . '/../../app/Http/Controllers/mhs.php'
        );
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/mhs/pengajuan_topik.blade.php'
        );

        $this->assertStringContainsString("'status' => 1", $controller);
        $this->assertStringContainsString("'topik' => \$topik->topik_id", $controller);
        $this->assertStringContainsString("'topik',", $controller);
        $this->assertStringContainsString("'bidangilmuid'", $controller);
        $this->assertStringContainsString('@if($topik)', $view);
        $this->assertStringContainsString(
            'Data topik penelitian aktif tidak tersedia untuk bimbingan ini.',
            $view
        );
        $this->assertStringNotContainsString(
            '\\App\\Model\\trt_topik::where',
            $view
        );
    }
}
