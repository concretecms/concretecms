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
        $this->assertSame(
            $configuredDBVersion,
            $latestMigrationID,
            <<<EOT
The ID of the latest migration is {$latestMigrationID}, and it doesn't match the value of the concrete.version_db configuration key ({$configuredDBVersion}).
If you added a new migration, you should also update the value of version_db in the /concrete/config/concrete.php file
EOT
        );
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
