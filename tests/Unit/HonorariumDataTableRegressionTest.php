<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumDataTableRegressionTest extends TestCase
{
    public function testHonorariumTablesDoNotReuseTheGlobalDatatableIdentifier()
    {
        $listView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium.blade.php');
        $detailView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/honorarium_detail.blade.php');

        $this->assertStringContainsString('id="honorarium-date-table"', $listView);
        $this->assertStringContainsString("var tableSelector = '#honorarium-date-table';", $listView);
        $this->assertStringContainsString('$.fn.dataTable.isDataTable(tableSelector)', $listView);
        $this->assertStringContainsString('id="honorarium-detail-table"', $detailView);
        $this->assertStringContainsString("var tableSelector = '#honorarium-detail-table';", $detailView);
        $this->assertStringContainsString('$.fn.dataTable.isDataTable(tableSelector)', $detailView);
    }
}
