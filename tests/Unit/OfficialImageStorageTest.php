<?php

namespace Tests\Unit;

use App\Helper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OfficialImageStorageTest extends TestCase
{
    public function testItStoresOfficialImagesOnThePrivateDisk()
    {
        Storage::fake('official');
        $image = UploadedFile::fake()->image('signature.png');

        $fileName = Helper::storeOfficialImage($image, 'ttd_kaprodi_10');

        $this->assertSame(0, strpos($fileName, 'ttd_kaprodi_10_'));
        $this->assertTrue(Storage::disk('official')->exists($fileName));
        $this->assertStringStartsWith('data:image/png;base64,', Helper::officialImageDataUri($fileName));
    }

    public function testItOnlyDeletesManagedOfficialImages()
    {
        Storage::fake('official');
        Storage::disk('official')->put('ttd_kaprodi_10_abcdef1234567.png', 'signature');
        Storage::disk('official')->put('ttd_kaprodi_si.png', 'default signature');
        Storage::disk('official')->put('stempelprodi.png', 'stamp');

        $this->assertTrue(Helper::deleteManagedOfficialImage('ttd_kaprodi_10_abcdef1234567.png'));
        $this->assertFalse(Helper::deleteManagedOfficialImage('ttd_kaprodi_si.png'));
        $this->assertFalse(Helper::deleteManagedOfficialImage('stempelprodi.png'));
        $this->assertTrue(Storage::disk('official')->exists('ttd_kaprodi_si.png'));
        $this->assertTrue(Storage::disk('official')->exists('stempelprodi.png'));
    }

    public function testItRejectsUnsafeNamesAndUnsupportedUploads()
    {
        Storage::fake('official');

        $this->assertFalse(Helper::isSafeOfficialImageName('../signature.png'));
        $this->assertSame(asset('gambar/no_image.jpg'), Helper::officialImageDataUri('../signature.png'));

        $this->expectException(\RuntimeException::class);
        Helper::storeOfficialImage(
            UploadedFile::fake()->create('signature.php', 10, 'image/png'),
            'ttd_kaprodi_10'
        );
    }
}
