<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use ORM;

class Version20150515000000 extends AbstractMigration
{

    public function getDescription()
    {
        return '5.7.5a1';
    }

    public function up(Schema $schema)
    {
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
            'PageFeeds',
        ));

        // I can't seem to get the doctrine cache to clear any other way.
        $cms = \Core::make('app');
        $cms->clearCaches();

        $this->purgeOrphanedScrapbooksBlocks();
    }

    public function down(Schema $schema)
    {
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
            array()
        );
        foreach($orphanedCollectionVersionBlocks AS $row) {
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
