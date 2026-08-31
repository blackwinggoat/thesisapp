<?php

namespace Tests\Unit;

use App\Http\Middleware\akademik_fakultas;
use App\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class AdminAkademikFakultasAccessTest extends TestCase
{
    public function testAdminAndAkademikFakultasCanPassTheAcademicFacultyMiddleware()
    {
        foreach ([1, 4] as $level) {
            $user = new User();
            $user->level = $level;
            $this->app['auth']->guard()->setUser($user);

            $request = Request::create('/fakultas/rekap-ujian-selesai', 'GET');
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $response = (new akademik_fakultas())->handle($request, function () {
                return response('allowed');
            });

            $this->assertSame('allowed', $response->getContent());
        }
    }

    public function testOtherRolesRemainBlockedFromAcademicFacultyMiddleware()
    {
        $user = new User();
        $user->level = 7;
        $this->app['auth']->guard()->setUser($user);

        $request = Request::create('/fakultas/rekap-ujian-selesai', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = (new akademik_fakultas())->handle($request, function () {
            return response('allowed');
        });

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertSame(url('/'), $response->getTargetUrl());
    }

    public function testAdminSidebarContainsTheCompletedExamRecapLink()
    {
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebar.blade.php');

        $this->assertStringContainsString('MENU AKADEMIK-FAKULTAS', $sidebar);
        $this->assertStringContainsString("route('fakultas.rekap_ujian_selesai')", $sidebar);
        $this->assertStringContainsString('SK Yudisium', $sidebar);
    }
}
