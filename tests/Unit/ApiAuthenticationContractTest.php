<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ApiAuthenticationContractTest extends TestCase
{
    public function testVersionedAuthenticationContractIsRegistered()
    {
        $routes = file_get_contents(__DIR__ . '/../../routes/api.php');
        $kernel = file_get_contents(__DIR__ . '/../../app/Http/Kernel.php');
        $middleware = file_get_contents(__DIR__ . '/../../app/Http/Middleware/ApiTokenAuth.php');
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/Api/AuthController.php');
        $documentation = file_get_contents(__DIR__ . '/../../docs/API_AUTHENTICATION.md');

        $this->assertStringContainsString("Route::prefix('v1')", $routes);
        $this->assertStringContainsString("'/auth/login'", $routes);
        $this->assertStringContainsString("'/auth/me'", $routes);
        $this->assertStringContainsString("'/auth/logout'", $routes);
        $this->assertStringContainsString("'api.token'", $routes);
        $this->assertStringContainsString("'api.token'", $kernel);
        $this->assertStringContainsString('ApiTokenAuth::class', $kernel);
        $this->assertStringContainsString("Authorization", $middleware);
        $this->assertStringContainsString("hash('sha256'", $middleware);
        $this->assertStringContainsString('random_bytes(32)', $controller);
        $this->assertStringContainsString("'client_name' => 'required|string|max:100'", $controller);
        $this->assertStringContainsString('https://thesis.fikom.app/api/v1', $documentation);
        $this->assertStringContainsString('Authorization: Bearer ACCESS_TOKEN', $documentation);
    }
}
