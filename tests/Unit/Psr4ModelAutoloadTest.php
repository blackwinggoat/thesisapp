<?php

namespace Tests\Unit;

use App\Dosen;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class Psr4ModelAutoloadTest extends TestCase
{
    public function testDosenModelUsesItsPsr4Filename()
    {
        $model = new ReflectionClass(Dosen::class);

        $this->assertSame('Dosen.php', basename($model->getFileName()));
        $this->assertTrue($model->isSubclassOf(Model::class));
    }
}
