<?php

namespace Tests\Unit;

use App\Http\Controllers\mhs;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;

class MahasiswaChatReplacementTest extends TestCase
{
    public function testLegacyChatRouteUsesInternalMessaging()
    {
        $response = (new mhs())->chat();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('mhs/mail_inbox'), $response->getTargetUrl());
        $this->assertFileNotExists(
            __DIR__ . '/../../resources/views/tugasakhir/mhs/chat.blade.php'
        );
    }
}
