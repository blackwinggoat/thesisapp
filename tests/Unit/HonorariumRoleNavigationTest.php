<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HonorariumRoleNavigationTest extends TestCase
{
    public function testHonorariumSetupIsOwnedByAkademikProdiOnly()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $akademikProdiStart = strpos($routes, "Route::group(['middleware' => 'akademik_prodi'");
        $akademikProdiEnd = strpos($routes, "Route::group(['middleware' => 'dekan'", $akademikProdiStart);
        $akademikFakultasStart = strpos($routes, "Route::group(['middleware' => 'akademik_fakultas'");
        $akademikFakultasEnd = strpos($routes, "Route::group(['middleware' => 'dosen'", $akademikFakultasStart);
        $honorariumStart = strpos($routes, "Route::group(['prefix' => 'fakultas/honorarium'");

        $this->assertNotFalse($akademikProdiStart);
        $this->assertNotFalse($akademikProdiEnd);
        $this->assertNotFalse($akademikFakultasStart);
        $this->assertNotFalse($akademikFakultasEnd);
        $this->assertNotFalse($honorariumStart);
        $this->assertGreaterThan($akademikProdiStart, $honorariumStart);
        $this->assertLessThan($akademikProdiEnd, $honorariumStart);
        $this->assertFalse($honorariumStart > $akademikFakultasStart && $honorariumStart < $akademikFakultasEnd);

        $akademikFakultasSidebar = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarakademikfakultas.blade.php'
        );
        $akademikProdiSidebar = file_get_contents(
            __DIR__ . '/../../resources/views/tugasakhir/layouts/sidebarakademikprodi.blade.php'
        );

        $this->assertStringNotContainsString("route('honorarium_penetapan_home')", $akademikFakultasSidebar);
        $this->assertStringContainsString("route('honorarium_penetapan_home')", $akademikProdiSidebar);
    }
}
