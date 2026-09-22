<?php

namespace Tests\Unit;

use App\Services\DosenSignatureImageService;
use RuntimeException;
use Tests\TestCase;

class DosenSignatureImageServiceTest extends TestCase
{
    public function test_it_trims_white_space_and_uses_a_consistent_transparent_canvas()
    {
        $source = imagecreatetruecolor(900, 420);
        $white = imagecolorallocate($source, 255, 255, 255);
        $blue = imagecolorallocate($source, 24, 78, 153);
        imagefill($source, 0, 0, $white);
        imagefilledellipse($source, 170, 210, 210, 42, $blue);
        imagefilledellipse($source, 350, 195, 240, 40, $blue);
        imagefilledellipse($source, 540, 220, 170, 38, $blue);

        ob_start();
        imagejpeg($source, null, 95);
        $contents = ob_get_clean();
        imagedestroy($source);

        $service = new DosenSignatureImageService();
        $normalized = $service->normalize($contents);
        $metrics = $service->inspect($normalized);

        $this->assertSame('image/png', $metrics['mime']);
        $this->assertSame(420, $metrics['width']);
        $this->assertSame(150, $metrics['height']);
        $this->assertLessThanOrEqual(11, $metrics['left_margin']);
        $this->assertLessThanOrEqual(11, $metrics['right_margin']);
        $this->assertGreaterThan(0, $metrics['top_margin']);
        $this->assertGreaterThan(0, $metrics['bottom_margin']);
        $this->assertSame(0, $metrics['opaque_background_pixels']);
        $this->assertTrue($service->hasStandardCanvas($metrics));

        $normalizedImage = imagecreatefromstring($normalized);
        $corner = imagecolorsforindex($normalizedImage, imagecolorat($normalizedImage, 0, 0));
        imagedestroy($normalizedImage);

        $this->assertSame(127, $corner['alpha']);
    }

    public function test_it_rejects_an_empty_white_signature()
    {
        $source = imagecreatetruecolor(300, 120);
        $white = imagecolorallocate($source, 255, 255, 255);
        imagefill($source, 0, 0, $white);
        ob_start();
        imagepng($source);
        $contents = ob_get_clean();
        imagedestroy($source);

        $this->expectException(RuntimeException::class);

        (new DosenSignatureImageService())->normalize($contents);
    }

    public function test_it_ignores_a_transparent_canvas_without_treating_it_as_signature_ink()
    {
        $source = imagecreatetruecolor(640, 220);
        imagealphablending($source, false);
        imagesavealpha($source, true);
        $transparentBlack = imagecolorallocatealpha($source, 0, 0, 0, 127);
        $ink = imagecolorallocatealpha($source, 16, 75, 145, 0);
        imagefill($source, 0, 0, $transparentBlack);
        imagesetthickness($source, 5);
        imageline($source, 150, 145, 330, 75, $ink);
        imageline($source, 290, 75, 470, 150, $ink);

        ob_start();
        imagepng($source);
        $contents = ob_get_clean();
        imagedestroy($source);

        $normalized = (new DosenSignatureImageService())->normalize($contents);
        $metrics = (new DosenSignatureImageService())->inspect($normalized);

        $this->assertSame(0, $metrics['opaque_background_pixels']);
        $this->assertLessThanOrEqual(11, $metrics['left_margin']);
        $this->assertLessThanOrEqual(11, $metrics['right_margin']);
    }
}
