<?php

namespace Tests\Feature;

use Illuminate\Contracts\Http\Kernel;
use ReflectionMethod;
use Tests\TestCase;

class RouteIntegrityTest extends TestCase
{
    public function testControllerRoutesReferencePublicMethods()
    {
        foreach ($this->app['router']->getRoutes() as $route) {
            $action = $route->getActionName();

            if ($action === 'Closure') {
                continue;
            }

            $this->assertStringContainsString('@', $action, "Unsupported route action: {$action}");
            list($controller, $method) = explode('@', $action, 2);

            $this->assertTrue(class_exists($controller), "Missing route controller: {$controller}");
            $this->assertTrue(method_exists($controller, $method), "Missing route method: {$action}");
            $this->assertTrue(
                (new ReflectionMethod($controller, $method))->isPublic(),
                "Route method is not public: {$action}"
            );
        }
    }

    public function testEveryApplicationRoleHasRegisteredMiddlewareAndRoutes()
    {
        $minimumRouteCounts = [
            'admin' => 2,
            'dekan' => 6,
            'wakil_dekan' => 6,
            'akademik_fakultas' => 22,
            'kaprodi' => 125,
            'akademik_prodi' => 33,
            'dosen' => 53,
            'mhs' => 52,
            'keuangan_fakultas' => 16,
        ];
        $this->app->make(Kernel::class)->bootstrap();
        $router = $this->app['router'];
        $registeredMiddleware = $router->getMiddleware();
        $routes = $router->getRoutes();

        $this->assertGreaterThanOrEqual(334, count($routes));

        foreach ($minimumRouteCounts as $role => $minimumCount) {
            $this->assertArrayHasKey($role, $registeredMiddleware, "Missing middleware alias: {$role}");
            $this->assertTrue(
                class_exists($registeredMiddleware[$role]),
                "Missing middleware class: {$registeredMiddleware[$role]}"
            );

            $actualCount = 0;
            foreach ($routes as $route) {
                if (in_array($role, $route->gatherMiddleware(), true)) {
                    $actualCount++;
                }
            }

            $this->assertGreaterThanOrEqual(
                $minimumCount,
                $actualCount,
                "Role {$role} lost protected routes"
            );
        }
    }
}
