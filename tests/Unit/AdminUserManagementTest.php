<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AdminUserManagementTest extends TestCase
{
    public function testAdminUserManagementRoutesAndControllerAreAvailable()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Admin.php');

        $this->assertStringContainsString("Route::get('/users', 'Admin@users')->name('admin.users.index');", $routes);
        $this->assertStringContainsString("Route::post('/users/{id}/reset-password', 'Admin@reset_user_password')->name('admin.users.reset_password');", $routes);
        $this->assertStringContainsString("Route::post('/users/{id}/login-as', 'Admin@login_as_user')->name('admin.users.login_as');", $routes);
        $this->assertStringContainsString("Route::delete('/users/{id}', 'Admin@delete_user')->name('admin.users.delete');", $routes);
        $this->assertStringContainsString("Route::post('/admin/back-to-admin', 'Admin@back_to_admin')->middleware('auth')->name('admin.back_to_admin');", $routes);

        $this->assertStringContainsString('public function users(Request $request)', $controller);
        $this->assertStringContainsString('public function reset_user_password(Request $request, $id)', $controller);
        $this->assertStringContainsString('public function delete_user(Request $request, $id)', $controller);
        $this->assertStringContainsString('public function login_as_user(Request $request, $id)', $controller);
        $this->assertStringContainsString('public function back_to_admin(Request $request)', $controller);
        $this->assertStringContainsString('Hash::make($request->password)', $controller);
        $this->assertStringContainsString("DB::table('users')->where('id', \$id)->delete();", $controller);
        $this->assertStringContainsString('(int) $targetUser->id === (int) $authUser->id', $controller);
        $this->assertStringContainsString('Auth::loginUsingId($targetUser->id)', $controller);
        $this->assertStringContainsString('Auth::loginUsingId($sourceUserId)', $controller);
        $this->assertStringContainsString("login_as_source_user_level", $controller);
        $this->assertStringContainsString('(int) $targetUser->level === 1', $controller);
        $this->assertStringContainsString('!array_key_exists((int) $targetUser->level, $roleLabels)', $controller);
    }

    public function testAdminUserManagementViewAndNavigationAreWired()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/admin/users.blade.php');
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebar.blade.php');
        $navigation = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/navigation.blade.php');

        $this->assertStringContainsString('Manajemen User', $view);
        $this->assertStringContainsString("route('admin.users.index')", $view);
        $this->assertStringContainsString("route('admin.users.reset_password'", $view);
        $this->assertStringContainsString("route('admin.users.login_as'", $view);
        $this->assertStringContainsString("route('admin.users.delete'", $view);
        $this->assertStringContainsString("method_field('DELETE')", $view);
        $this->assertStringContainsString('fa fa-trash', $view);
        $this->assertStringContainsString('Data master dosen/mahasiswa tidak ikut dihapus', $view);
        $this->assertStringContainsString('js-reset-password', $view);
        $this->assertStringContainsString('Login As', $view);
        $this->assertStringContainsString('array_key_exists((int) $user->level, $roleLabels)', $view);

        $this->assertStringContainsString("route('admin.users.index')", $sidebar);
        $this->assertStringContainsString('Manajemen User', $sidebar);
        $this->assertStringContainsString("route('admin.back_to_admin')", $navigation);
        $this->assertStringContainsString('Kembali ke Admin', $navigation);
    }

    public function testAdminLoginAsReturnButtonIsSharedAcrossRoleSidebars()
    {
        $partial = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/back_to_admin.blade.php');
        $sidebars = [
            'sidebarkaprodi.blade.php',
            'sidebarakademikprodi.blade.php',
            'sidebarwakildekan.blade.php',
            'sidebarkeuanganfakultas.blade.php',
            'sidebardekan.blade.php',
            'sidebarakademikfakultas.blade.php',
            'sidebardosen.blade.php',
            'sidebarmhs.blade.php',
        ];

        $this->assertStringContainsString("route('admin.back_to_admin')", $partial);
        $this->assertStringContainsString("session('login_as_source_user_level')", $partial);
        $this->assertStringContainsString('Kembali ke Admin', $partial);

        foreach ($sidebars as $sidebar) {
            $contents = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/' . $sidebar);
            $this->assertStringContainsString("@include('tugasakhir.layouts.back_to_admin')", $contents, $sidebar);
        }
    }
}
