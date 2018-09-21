<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170420000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        Page::getByPath('/dashboard/system/backup')->delete();
        Page::getByPath('/dashboard/system/backup/backup')->delete();
        Page::getByPath('/dashboard/system/backup/update')->delete();

        $this->createSinglePage('/dashboard/system/update', 'Update concrete5');

        $this->createSinglePage('/dashboard/system/update/update', 'Apply Update', ['meta_keywords' => 'upgrade, new version, update']);
    }
}
