<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;


class Version20180831213421 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $c = \Page::getByPath('/dashboard/system/express/entities');
        if ($c && !$c->isError()) {
            $c->setAttribute('exclude_nav', false);
            $c->setAttribute('exclude_search_index', false);
        }
    }
}
