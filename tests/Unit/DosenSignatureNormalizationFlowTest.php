<?php

namespace Tests\Unit;

use Tests\TestCase;

class DosenSignatureNormalizationFlowTest extends TestCase
{
    public function test_uploaded_and_drawn_signatures_are_normalized_before_being_stored()
    {
        $controller = file_get_contents(__DIR__ . '/../../app/Http/Controllers/dosen.php');
        $view = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/dosen/tanda_tangan.blade.php');
        $legacyHonorariumView = file_get_contents(__DIR__ . '/../../resources/views/tugasakhir/keuanganfakultas/cetak_honorarium_per_mahasiswa.blade.php');

        $this->assertStringContainsString('DosenSignatureImageService', $controller);
        $this->assertStringContainsString('->normalize($tanda_tangan)', $controller);
        $this->assertStringContainsString('tandaTanganPerluUnggahUlang', $controller);
        $this->assertStringContainsString('signature-preview-frame', $view);
        $this->assertStringContainsString('object-fit: contain', $legacyHonorariumView);
        $this->assertStringNotContainsString('object-fit: cover', $legacyHonorariumView);
    }

    public function test_normalization_command_keeps_an_original_backup_before_replacing_a_signature()
    {
        $command = file_get_contents(__DIR__ . '/../../app/Console/Commands/NormalizeDosenSignatures.php');
        $migration = file_get_contents(__DIR__ . '/../../database/migrations/2026_09_22_010000_create_dosen_signature_normalization_backups.php');
        $deployScript = file_get_contents(__DIR__ . '/../../scripts/deploy-production.sh');
        $deployHook = file_get_contents(__DIR__ . '/../../scripts/deploy-cpanel.sh');

        $this->assertStringContainsString('thesis:normalize-dosen-signatures', $command);
        $this->assertStringContainsString('original_tanda_tangan_base64', $command);
        $this->assertStringContainsString('storeBackupAndNormalize', $command);
        $this->assertStringContainsString('mst_tanda_tangan_normalization_backups', $migration);
        $this->assertStringContainsString('--normalize-dosen-signatures', $deployScript);
        $this->assertStringContainsString('run_approved_dosen_signature_normalization', $deployHook);
    }
}
