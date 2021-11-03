<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20150515000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription(): string
    {
        return '5.7.5a1';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        $this->refreshDatabaseTables([
            'PageFeeds',
            'PageTypeComposerOutputBlocks',
        ]);

        // I can't seem to get the doctrine cache to clear any other way.
        $this->app->clearCaches();

        $this->purgeOrphanedScrapbooksBlocks();
    }

    protected function purgeOrphanedScrapbooksBlocks()
    {
        $this->refreshDatabaseTables(['PageTypeComposerOutputBlocks']);
        $orphanedCollectionVersionBlocks = $this->connection->fetchAll(
            '
            select cID, cvID, cvb.bID, arHandle
            from CollectionVersionBlocks cvb
                inner join btCoreScrapbookDisplay btCSD on cvb.bID = btCSD.bID
                inner join Blocks b on b.bID = btCSD.bOriginalID
                left join BlockTypes bt on b.btID = bt.btID
            where bt.btID IS NULL',
            []
        );
        foreach ($orphanedCollectionVersionBlocks as $row) {
            $nc = \Page::getByID($row['cID'], $row['cvID']);
            if (!is_object($nc) || $nc->isError()) {
                continue;
            }
            $b = \Block::getByID($row['bID'], $nc, $row['arHandle']);
            if (is_object($b)) {
                $b->deleteBlock();
            }
        }
    }
}
