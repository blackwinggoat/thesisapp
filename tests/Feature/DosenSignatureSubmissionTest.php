<?php

namespace Tests\Feature;

use App\Http\Controllers\dosen;
use App\Services\DosenSignatureImageService;
use Illuminate\Auth\GenericUser;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GenericMimeSignatureUpload extends UploadedFile
{
    public function getMimeType()
    {
        return 'application/octet-stream';
    }
}

class DosenSignatureSubmissionTest extends TestCase
{
    protected $temporaryFiles = [];

    protected function setUp()
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('mst_tanda_tangan', function (Blueprint $table) {
            $table->increments('id_tanda_tangan');
            $table->string('C_KODE_DOSEN', 20)->unique();
            $table->binary('tanda_tangan')->nullable();
            $table->timestamps();
        });

        Auth::guard()->setUser(new GenericUser([
            'id' => 1,
            'name' => 'DOSEN-01',
            'email' => 'dosen@example.test',
            'level' => 7,
        ]));
    }

    protected function tearDown()
    {
        foreach ($this->temporaryFiles as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }

        parent::tearDown();
    }

    public function testUploadFileAndDrawpadUseSeparateSubmissionSources()
    {
        $uploadResponse = (new dosen())->upload_ttd_post(Request::create(
            '/dsn/upload_ttd',
            'POST',
            ['sumber_tanda_tangan' => 'upload'],
            [],
            ['upload_ttd' => $this->uploadedSignatureFile()]
        ));

        $this->assertSame('success', $uploadResponse->getSession()->get('status'));
        $this->assertSame('Tanda tangan berhasil diunggah dan disimpan.', $uploadResponse->getSession()->get('message'));

        $firstSignature = DB::table('mst_tanda_tangan')
            ->where('C_KODE_DOSEN', 'DOSEN-01')
            ->value('tanda_tangan');
        $this->assertSame(420, (new DosenSignatureImageService())->inspect($firstSignature)['width']);

        $drawResponse = (new dosen())->upload_ttd_post(Request::create(
            '/dsn/upload_ttd',
            'POST',
            [
                'sumber_tanda_tangan' => 'draw',
                'ttd_image' => 'data:image/png;base64,' . base64_encode($this->signaturePngContents()),
            ]
        ));

        $this->assertSame('success', $drawResponse->getSession()->get('status'));
        $this->assertSame('Tanda tangan hasil gambar berhasil disimpan.', $drawResponse->getSession()->get('message'));
        $this->assertSame(1, DB::table('mst_tanda_tangan')->where('C_KODE_DOSEN', 'DOSEN-01')->count());
    }

    public function testUploadWithoutAFileReturnsAnActionableMessage()
    {
        $response = (new dosen())->upload_ttd_post(new Request([
            'sumber_tanda_tangan' => 'upload',
        ]));

        $this->assertSame('error', $response->getSession()->get('status'));
        $this->assertSame('Pilih berkas tanda tangan terlebih dahulu.', $response->getSession()->get('message'));
    }

    public function testUploadAcceptsPngContentsWhenHostingMimeGuesserIsGeneric()
    {
        $response = (new dosen())->upload_ttd_post(Request::create(
            '/dsn/upload_ttd',
            'POST',
            ['sumber_tanda_tangan' => 'upload'],
            [],
            ['upload_ttd' => $this->uploadedSignatureFileWithGenericMime()]
        ));

        $this->assertSame('success', $response->getSession()->get('status'));
        $this->assertSame(1, DB::table('mst_tanda_tangan')->where('C_KODE_DOSEN', 'DOSEN-01')->count());
    }

    protected function uploadedSignatureFile()
    {
        $path = tempnam(sys_get_temp_dir(), 'thesis-signature-');
        file_put_contents($path, $this->signaturePngContents());
        $this->temporaryFiles[] = $path;

        return new UploadedFile($path, 'tanda-tangan.png', 'image/png', UPLOAD_ERR_OK, true);
    }

    protected function uploadedSignatureFileWithGenericMime()
    {
        $path = tempnam(sys_get_temp_dir(), 'thesis-signature-');
        file_put_contents($path, $this->signaturePngContents());
        $this->temporaryFiles[] = $path;

        return new GenericMimeSignatureUpload($path, 'tanda-tangan.png', 'application/octet-stream', UPLOAD_ERR_OK, true);
    }

    protected function signaturePngContents()
    {
        $image = imagecreatetruecolor(600, 180);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        $ink = imagecolorallocatealpha($image, 22, 74, 145, 0);
        imagefill($image, 0, 0, $transparent);
        imagealphablending($image, true);
        imagesetthickness($image, 5);
        imageline($image, 140, 135, 270, 55, $ink);
        imageline($image, 255, 55, 450, 135, $ink);

        ob_start();
        imagepng($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        return $contents;
    }
}
