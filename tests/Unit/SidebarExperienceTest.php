<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SidebarExperienceTest extends TestCase
{
    public function testSidebarToggleIsAccessibleResponsiveAndSharedAcrossRoles()
    {
        $navigation = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/navigation.blade.php');
        $script = file_get_contents(__DIR__ . '/../../public/master/assets/js/apps.js');
        $style = file_get_contents(__DIR__ . '/../../public/master/assets/css/style.css');
        $responsiveStyle = file_get_contents(__DIR__ . '/../../public/master/assets/css/style-responsive.css');

        $this->assertStringContainsString('data-sidebar-toggle', $navigation);
        $this->assertStringContainsString('data-sidebar-dismiss', $navigation);
        $this->assertStringContainsString('sidebarPreferenceKey', $script);
        $this->assertStringContainsString('sidebar-mobile-open', $script);
        $this->assertStringContainsString("event.key === 'Escape'", $script);
        $this->assertStringContainsString('body.sidebar-collapsed .page-content', $style);
        $this->assertStringContainsString('body.sidebar-mobile-open .sidebar-backdrop', $responsiveStyle);
    }
}
