<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DataTableMarkupTest extends TestCase
{
    public function testParticipantTableKeepsBodyOutsideHeader()
    {
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/prodi/peserta_ujianmeja.blade.php'
        );

        $this->assertNotRegExp('/<thead[^>]*>\s*<tbody/i', $view);
    }

    public function testDetailTableDoesNotUseColspanInsideBody()
    {
        $view = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/prodi/detail_status_bimbingan_mahasiswa.blade.php'
        );

        $this->assertRegExp('/<tbody>(.*?)<\/tbody>/s', $view);
        preg_match('/<tbody>(.*?)<\/tbody>/s', $view, $matches);

        $this->assertNotRegExp('/<td[^>]+colspan/i', $matches[1]);
    }

    public function testDetailTableDefinesItsEmptyStateThroughDataTables()
    {
        $script = file_get_contents(__DIR__ . '/../../public/master/assets/js/apps.js');

        $this->assertContains(
            "emptyTable: 'Belum ada data mahasiswa pada status ini.'",
            $script
        );
    }

    public function testLayoutCacheBustsTheRuntimeDataTablesScript()
    {
        $footer = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/footer.blade.php'
        );

        $this->assertContains(
            "?v={{ filemtime(public_path('master/assets/js/apps.js')) }}",
            $footer
        );
    }
}
