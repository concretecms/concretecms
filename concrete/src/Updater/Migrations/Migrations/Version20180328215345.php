<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180328215345 extends AbstractMigration
{

    public function upgradeDatabase()
    {
        $c = Page::getByPath('/page_not_found');
        if ($c && !$c->isError()) {
            $db = $this->connection;
            $db->executeQuery('update Pages set siteTreeID = ? where cID = ?', [
                0, $c->getCollectionID()
            ]);
        }
    }
}
