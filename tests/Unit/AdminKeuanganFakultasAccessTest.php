<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use App\Http\Middleware\keuangan_fakultas;
use App\User;
use Tests\TestCase;

class AdminKeuanganFakultasAccessTest extends TestCase
{
    public function testAdminAndKeuanganFakultasCanPassTheFinancialMiddleware()
    {
        foreach ([1, 9] as $level) {
            $user = new User();
            $user->level = $level;
            $this->app['auth']->guard()->setUser($user);

            $request = Request::create('/honorarium', 'GET');
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $response = (new keuangan_fakultas())->handle($request, function () {
                return response('allowed');
            });

            $this->assertSame('allowed', $response->getContent());
        }
    }

    public function testOtherRolesRemainBlockedFromFinancialMiddleware()
    {
        $user = new User();
        $user->level = 7;
        $this->app['auth']->guard()->setUser($user);

        $request = Request::create('/honorarium', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = (new keuangan_fakultas())->handle($request, function () {
            return response('allowed');
        });

        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        $this->assertSame(url('/'), $response->getTargetUrl());
    }

    public function testAdminSidebarContainsTheCompleteFinancialMenu()
    {
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebar.blade.php');

        $this->assertStringContainsString('MENU KEUANGAN FAKULTAS', $sidebar);
        $this->assertStringContainsString("route('master_pembayaran_home')", $sidebar);
        $this->assertStringContainsString("route('sanksi_pembayaran_home')", $sidebar);
        $this->assertStringContainsString("route('honorarium_home')", $sidebar);
        $this->assertStringContainsString("route('report_periode_ujian_home')", $sidebar);
        $this->assertStringContainsString("route('report_dosen_home')", $sidebar);
    }
}
