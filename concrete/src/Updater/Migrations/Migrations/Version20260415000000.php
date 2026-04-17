<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20260415000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/permissions/early_page_not_found', 'Early Page Not Found', ['meta_keywords' => '404, crawler, hack, crack, spy, wp-admin, wp-login, found']);
    }
}
