<?php

namespace Tests\Unit;

use App\Http\Controllers\mhs;
use PHPUnit\Framework\TestCase;

class MahasiswaDosenWhatsAppTest extends TestCase
{
    public function testLecturerWhatsappNumbersUseWaMeInternationalFormat()
    {
        $method = new \ReflectionMethod(mhs::class, 'normalizeNomorWhatsappDosen');
        $method->setAccessible(true);
        $controller = new mhs;

        $this->assertSame('6281234567890', $method->invoke($controller, '0812-3456-7890'));
        $this->assertSame('6281234567890', $method->invoke($controller, '+6281234567890'));
        $this->assertSame('6281234567890', $method->invoke($controller, '81234567890'));
        $this->assertNull($method->invoke($controller, '021-555-1234'));
    }

    public function testLecturerListRendersWhatsappLinkOnlyForNormalizedNumbers()
    {
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/mhs/daftar_dosen.blade.php');

        $this->assertStringContainsString('https://wa.me/{{ $value->nomor_whatsapp }}', $view);
        $this->assertStringContainsString('fa fa-whatsapp', $view);
        $this->assertStringContainsString('target="_blank"', $view);
        $this->assertStringNotContainsString('{{ $value->nomor_telpon ?? \'-\' }}', $view);
        $this->assertStringContainsString('@else', $view);
    }
}
