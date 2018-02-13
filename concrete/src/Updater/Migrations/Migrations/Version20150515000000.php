<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20150515000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Migrations\AbstractMigration::getDescription()
     */
    public function getDescription()
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
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema([
            'PageFeeds',
        ]);

        // I can't seem to get the doctrine cache to clear any other way.
        $cms = \Core::make('app');
        $cms->clearCaches();

        $this->purgeOrphanedScrapbooksBlocks();
    }

    protected function purgeOrphanedScrapbooksBlocks()
    {
        $db = \Database::connection();
        $orphanedCollectionVersionBlocks = $db->fetchAll(
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
