<?php

namespace Concrete\Tests\Update;

use Concrete\Core\Updater\Migrations\Configuration;
use Doctrine\Migrations\Version\Version;
use PHPUnit\Framework\TestCase;

class LatestMigrationTest extends TestCase
{
    public function testLatestMigration(): void
    {
        $configuredDBVersion = $this->getConfiguredDBVersion();
        $latestMigrationID = $this->getLatestMigrationID();
        $this->assertSame($configuredDBVersion, $latestMigrationID, "The last migration should be {$configuredDBVersion} instead of {$latestMigrationID}");
    }

    protected function getConfiguredDBVersion(): string
    {
        $config = app('config');

        return $config->get('concrete.version_db');
    }

    protected function getLatestMigrationID(): string
    {
        $configuration = new Configuration();
        $versions = $configuration->getMigrations();
        $versionIDs = array_map(
            function (Version $version): string {
                return $version->getVersion();
            },
            $versions
        );
        sort($versionIDs);

        return array_pop($versionIDs);
    }
}
