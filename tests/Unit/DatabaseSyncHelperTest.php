<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../scripts/database-sync-helper.php';

class DatabaseSyncHelperTest extends TestCase
{
    public function testLoopbackHostGuardAcceptsOnlyLocalTargets()
    {
        $this->assertTrue(databaseSyncIsLoopbackHost('127.0.0.1'));
        $this->assertTrue(databaseSyncIsLoopbackHost('localhost'));
        $this->assertTrue(databaseSyncIsLoopbackHost('::1'));
        $this->assertFalse(databaseSyncIsLoopbackHost('database.example.com'));
    }

    public function testMysqlOptionValuesEscapeCredentials()
    {
        $this->assertSame('"pa\\\\ss\\"word"', databaseSyncMysqlOptionValue('pa\\ss"word'));
    }

    public function testDumpLocalizationReplacesOnlyDefiners()
    {
        $directory = sys_get_temp_dir() . '/thesis-db-sync-' . bin2hex(random_bytes(8));
        mkdir($directory, 0700);
        $source = $directory . '/source.sql.gz';
        $target = $directory . '/target.sql.gz';
        $sql = "CREATE DEFINER=`remote`@`localhost` VIEW `example` AS SELECT 'remote@localhost';\n";

        $archive = gzopen($source, 'wb9');
        gzwrite($archive, $sql);
        gzclose($archive);

        ob_start();
        databaseSyncLocalizeDump($source, $target);
        $replacementCount = trim(ob_get_clean());
        $localized = gzdecode(file_get_contents($target));

        $this->assertSame('1', $replacementCount);
        $this->assertStringContainsString('DEFINER=CURRENT_USER', $localized);
        $this->assertStringContainsString("SELECT 'remote@localhost'", $localized);

        unlink($source);
        unlink($target);
        rmdir($directory);
    }

    public function testNumericNormalizationMatchesMysqlAndMariadbRepresentations()
    {
        $this->assertSame('14', databaseSyncNormalizeValue(14.0, 'double(16,2)'));
        $this->assertSame('14', databaseSyncNormalizeValue('14.00', 'double(16,2)'));
        $this->assertSame('20', databaseSyncNormalizeValue(20.0, 'double(16,2)'));
        $this->assertSame('20', databaseSyncNormalizeValue('20.00', 'double(16,2)'));
    }
}
