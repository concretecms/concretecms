<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20181212000000 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // remove database query log: it's more trouble than it's worth. Just use blackfire or something
        // similar.
        $page = Page::getByPath('/dashboard/system/optimization/query_log');
        if ($page && !$page->isError()) {
            $page->delete();
        }

    }
}
