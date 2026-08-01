<?php

namespace Tests\Unit;

use App\Helper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnnouncementImageStorageTest extends TestCase
{
    public function testItStoresAndDeletesManagedAnnouncementImages()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->create('announcement.jpg', 100, 'image/jpeg');

        $path = Helper::storeAnnouncementImage($image);

        $this->assertSame(0, strpos($path, 'uploads/announcements/'));
        $this->assertTrue(Storage::disk('public')->exists($path));
        $this->assertTrue(Helper::deleteAnnouncementImage($path));
        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    public function testItBuildsManagedLegacyAndPlaceholderUrls()
    {
        $this->assertSame(
            asset('storage/uploads/announcements/example.jpg'),
            Helper::announcementImageUrl('uploads/announcements/example.jpg')
        );
        $this->assertSame(
            asset('gambar/legacy.jpg'),
            Helper::announcementImageUrl('legacy.jpg')
        );
        $this->assertSame(
            asset('gambar/no_image.jpg'),
            Helper::announcementImageUrl('')
        );
    }

    public function testItNeverDeletesLegacyPublicImages()
    {
        Storage::fake('public');

        $this->assertFalse(Helper::deleteAnnouncementImage('stempelprodi.png'));
        $this->assertFalse(Helper::deleteAnnouncementImage('uploads/announcements/../private.txt'));
    }

    public function testItRejectsUnsupportedExtensions()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('announcement.php', 10, 'image/jpeg');

        $this->expectException(\RuntimeException::class);

        Helper::storeAnnouncementImage($file);
    }
}
