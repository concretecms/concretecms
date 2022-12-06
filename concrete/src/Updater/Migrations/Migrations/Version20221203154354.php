<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20221203154354 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $pk = Key::getByHandle('access_api');
        if (!$pk instanceof Key) {
            Key::add('admin', 'access_api', 'Access API', '', false, false);
        }
        $pk = Key::getByHandle('view_welcome_content');
        if (!$pk instanceof Key) {
            Key::add('admin', 'view_welcome_content', 'View Welcome Content', 'Controls whether a user sees the Welcome Back modal interstitial, including upgrades and help.', false, false);
        }
    }
}
