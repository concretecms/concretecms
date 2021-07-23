<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20210331213100 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        Page::getByPath("/dashboard/users/points")->delete();
        Page::getByPath("/dashboard/users/points/actions")->delete();
        Page::getByPath("/dashboard/users/points/assign")->delete();
    }
}
