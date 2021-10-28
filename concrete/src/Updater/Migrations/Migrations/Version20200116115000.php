<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20200116115000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        if ($this->shouldUpdateCollectionVersions()) {
            $this->updateCollectionVersions();
        }
    }

    /**
     * Should we update the CollectionVersions database table?
     *
     * This is needed if we are re-executing this migration.
     *
     * @return bool
     */
    private function shouldUpdateCollectionVersions(): bool
    {
        $config = $this->app->make('config');
        $lastPerformedMigrationID = (string) $config->get('concrete.version_db_installed');
        if ($lastPerformedMigrationID === '') {
            return true;
        }
        $thisMigrationID = preg_replace('/.*Version(\d+)$/', '${1}', __CLASS__);

        return $thisMigrationID > $lastPerformedMigrationID;
    }

    /**
     * Clear the cvName field of the CollectionVersions table for aliases,
     * so that concrete5 knows that users want the current name of the aliased page.
     */
    private function updateCollectionVersions(): void
    {
        $this->connection->executeUpdate(
            <<<'EOT'
UPDATE
    CollectionVersions
    INNER JOIN Pages ON CollectionVersions.cID = Pages.cID
SET
    CollectionVersions.cvName = ''
WHERE
    Pages.cPointerID IS NOT NULL
    AND Pages.cPointerID <> 0
EOT
            );
    }
}
