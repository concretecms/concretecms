<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20180907091500 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // We do not need to install this singlepage any longer.
        //$this->createSinglePage('/dashboard/system/permissions/security', 'Security Options', ['meta_keywords' => 'login, logout, user, agent, ip, change']);
    }
}
