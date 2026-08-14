<?php

namespace Tests\Feature;

use App\Exceptions\Handler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Tests\TestCase;

class ProfilePhotoUploadErrorHandlingTest extends TestCase
{
    public function testMahasiswaPhotoUploadTooLargeRedirectsBackWithHelpfulMessage()
    {
        $request = Request::create('/mhs/kelengkapan_kontak', 'POST');
        $request->headers->set('referer', 'http://localhost/mhs/profil');

        $response = app(Handler::class)->render($request, new PostTooLargeException());

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringEndsWith('/mhs/profil', $response->headers->get('Location'));
    }

    public function testDosenPhotoUploadTooLargeRedirectsBackWithHelpfulMessage()
    {
        $request = Request::create('/dsn/kelengkapan_profil', 'POST');
        $request->headers->set('referer', 'http://localhost/dsn/profil');

        $response = app(Handler::class)->render($request, new PostTooLargeException());

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringEndsWith('/dsn/profil', $response->headers->get('Location'));
    }
}
