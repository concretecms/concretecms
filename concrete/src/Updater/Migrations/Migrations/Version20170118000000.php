<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170118000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->addVersionIdToPageTypeOutputBlocks();
    }

    protected function addVersionIdToPageTypeOutputBlocks()
    {
        $this->refreshDatabaseTables(['PageTypeComposerOutputBlocks']);
        $this->output(t('Updating cvID of PageTypeComposerOutputBlocks...'));
        $this->connection->executeQuery(<<<'EOT'
UPDATE
    PageTypeComposerOutputBlocks
    LEFT JOIN CollectionVersionBlocks
    ON PageTypeComposerOutputBlocks.cID = CollectionVersionBlocks.cID
    AND PageTypeComposerOutputBlocks.bID = CollectionVersionBlocks.bID
    AND PageTypeComposerOutputBlocks.arHandle = CollectionVersionBlocks.arHandle
SET
    PageTypeComposerOutputBlocks.cvID = IFNULL(CollectionVersionBlocks.cvID, 0)
WHERE
    PageTypeComposerOutputBlocks.cvID IS NULL
    OR PageTypeComposerOutputBlocks.cvID = 0
EOT
        );
    }
}
