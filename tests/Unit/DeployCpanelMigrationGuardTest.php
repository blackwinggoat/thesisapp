<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DeployCpanelMigrationGuardTest extends TestCase
{
    public function testDeploymentDoesNotFailWhenNoMigrationWasExplicitlyApproved()
    {
        $script = file_get_contents(__DIR__ . '/../../scripts/deploy-cpanel.sh');

        $this->assertStringContainsString('if [[ ! -f "$MIGRATION_APPROVAL_FILE" ]]; then', $script);
        $this->assertStringContainsString('return 0', $script);
    }
}
