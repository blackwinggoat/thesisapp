<?php

namespace Tests\Feature;

use Illuminate\Auth\GenericUser;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DosenSignatureDeletionTest extends TestCase
{
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
            $table->string('C_KODE_DOSEN', 20);
            $table->binary('tanda_tangan')->nullable();
            $table->timestamps();
        });
        Schema::create('mst_tanda_tangan_normalization_backups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_tanda_tangan')->unique();
            $table->string('C_KODE_DOSEN', 20)->nullable();
            $table->longText('original_tanda_tangan_base64');
            $table->string('original_sha256', 64);
            $table->string('normalized_sha256', 64)->nullable();
            $table->timestamps();
        });

        Auth::guard()->setUser(new GenericUser([
            'id' => 1,
            'name' => 'DOSEN-01',
            'email' => 'dosen@example.test',
            'level' => 7,
        ]));
    }

    public function testLecturerCanDeleteOnlyTheirOwnActiveSignatureAndBackup()
    {
        DB::table('mst_tanda_tangan')->insert([
            [
                'id_tanda_tangan' => 10,
                'C_KODE_DOSEN' => 'DOSEN-01',
                'tanda_tangan' => 'signature-dosen-01',
            ],
            [
                'id_tanda_tangan' => 11,
                'C_KODE_DOSEN' => 'DOSEN-02',
                'tanda_tangan' => 'signature-dosen-02',
            ],
        ]);
        DB::table('mst_tanda_tangan_normalization_backups')->insert([
            [
                'id_tanda_tangan' => 10,
                'C_KODE_DOSEN' => 'DOSEN-01',
                'original_tanda_tangan_base64' => base64_encode('signature-dosen-01-asli'),
                'original_sha256' => hash('sha256', 'signature-dosen-01-asli'),
                'normalized_sha256' => hash('sha256', 'signature-dosen-01'),
            ],
            [
                'id_tanda_tangan' => 11,
                'C_KODE_DOSEN' => 'DOSEN-02',
                'original_tanda_tangan_base64' => base64_encode('signature-dosen-02-asli'),
                'original_sha256' => hash('sha256', 'signature-dosen-02-asli'),
                'normalized_sha256' => hash('sha256', 'signature-dosen-02'),
            ],
        ]);

        $response = $this->withHeaders([
            'referer' => url('/dsn/tanda_tangan'),
        ])->delete('/dsn/tanda_tangan');

        $response->assertStatus(302);
        $response->assertSessionHas('status', 'success');
        $this->assertDatabaseMissing('mst_tanda_tangan', [
            'id_tanda_tangan' => 10,
            'C_KODE_DOSEN' => 'DOSEN-01',
        ]);
        $this->assertDatabaseMissing('mst_tanda_tangan_normalization_backups', [
            'id_tanda_tangan' => 10,
            'C_KODE_DOSEN' => 'DOSEN-01',
        ]);
        $this->assertDatabaseHas('mst_tanda_tangan', [
            'id_tanda_tangan' => 11,
            'C_KODE_DOSEN' => 'DOSEN-02',
        ]);
        $this->assertDatabaseHas('mst_tanda_tangan_normalization_backups', [
            'id_tanda_tangan' => 11,
            'C_KODE_DOSEN' => 'DOSEN-02',
        ]);
    }
}
