<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Page\Page;

class Version20190417180607 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/users/groups/message', 'Send Message to Group',
            ['meta_keywords' => 'user, group, people, messages']);
    }

    public function downgradeDatabase()
    {
        $page = Page::getByPath('/dashboard/users/groups/message');

        if ($page) {
            $page->delete();
        }
    }
}
