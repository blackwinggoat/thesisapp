<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ForgotPasswordManagementTest extends TestCase
{
    public function testForgotPasswordIsAvailableAndConfiguredFromAdmin()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/web.php');
        $adminController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Admin.php');
        $forgotController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Auth/ForgotPasswordController.php');
        $resetController = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Auth/ResetPasswordController.php');
        $service = file_get_contents(__DIR__ . '/../../app/Services/SystemMailSettings.php');
        $user = file_get_contents(__DIR__ . '/../../app/User.php');
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_08_23_010000_create_system_settings_table.php');
        $login = file_get_contents(__DIR__ . '/../../resources/views/auth/login.blade.php');
        $emailView = file_get_contents(__DIR__ . '/../../resources/views/auth/passwords/email.blade.php');
        $resetView = file_get_contents(__DIR__ . '/../../resources/views/auth/passwords/reset.blade.php');
        $adminView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/admin/mail_settings.blade.php');
        $sidebar = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/layouts/sidebar.blade.php');

        $this->assertStringContainsString("Schema::create('system_settings'", $migration);
        $this->assertStringContainsString("boolean('is_encrypted')", $migration);
        $this->assertStringContainsString("Route::get('/email-sistem', 'Admin@mail_settings')", $routes);
        $this->assertStringContainsString("Route::post('/email-sistem', 'Admin@mail_settings_update')", $routes);
        $this->assertStringContainsString('public function mail_settings()', $adminController);
        $this->assertStringContainsString('SystemMailSettings::update', $adminController);
        $this->assertStringContainsString('required_if:enabled,1', $adminController);
        $this->assertStringContainsString('Email Sistem', $sidebar);
        $this->assertStringContainsString('Pengaturan Email Reset Password', $adminView);
        $this->assertStringContainsString('password smtp disimpan terenkripsi', strtolower($adminView));
        $this->assertStringContainsString('Crypt::encrypt', $service);
        $this->assertStringContainsString('Config::set(\'mail.host\'', $service);
        $this->assertStringContainsString('SystemMailSettings::apply();', file_get_contents(__DIR__ . '/../../app/Providers/AppServiceProvider.php'));
        $this->assertStringContainsString('Lupa password?', $login);
        $this->assertStringContainsString("route('password.request')", $login);
        $this->assertStringContainsString('Jika email tersebut terdaftar', $forgotController);
        $this->assertStringContainsString('throttle:5,1', $forgotController);
        $this->assertStringContainsString('SystemMailSettings::isReady()', $forgotController);
        $this->assertStringContainsString("protected \$redirectTo = '/login';", $resetController);
        $this->assertStringContainsString('sendPasswordResetNotification', $user);
        $this->assertStringContainsString('ThesisResetPasswordNotification', $user);
        $this->assertStringContainsString('Lupa Password | Thesis App FIKOM UMI', $emailView);
        $this->assertStringContainsString('Reset Password | Thesis App FIKOM UMI', $resetView);
    }
}
