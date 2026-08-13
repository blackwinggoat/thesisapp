<?php

namespace Tests\Feature;

use App\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

class LogoutExpiryRedirectTest extends TestCase
{
    public function testExpiredLogoutRequestRedirectsBackToLogin()
    {
        $request = Request::create('/logout', 'POST');
        $response = app(Handler::class)->render($request, new TokenMismatchException());

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringEndsWith('/login', $response->headers->get('Location'));
    }
}
