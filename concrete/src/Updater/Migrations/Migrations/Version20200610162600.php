<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\File\DownloadStatistics;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20200610162600 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // Delete DownloadStatistics records not associated to file entities
        $this->connection->executeUpdate(
            <<<'EOT'
DELETE DownloadStatistics
FROM DownloadStatistics
LEFT JOIN Files ON DownloadStatistics.fID = Files.fID
WHERE Files.fID IS NULL
EOT
        );
        // Refresh the table definition
        $this->refreshEntities([
            File::class,
            DownloadStatistics::class,
        ]);
        // Set to NULL the uID column with a value of 0
        $this->connection->update('DownloadStatistics', ['uID' => null], ['uID' => 0]);
        // Set to NULL the rcID column with a value of 0
        $this->connection->update('DownloadStatistics', ['rcID' => null], ['rcID' => 0]);
    }
}
