<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\Scope;
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
        $pk = Key::getByHandle('view_announcement_content');
        if (!$pk instanceof Key) {
            Key::add('admin', 'view_announcement_content', 'View Announcement Content', 'Controls whether a user sees the announcement modal interstitial, including upgrades and help, upon login.', false, false);
        }

        // Fix an oauth2 client bug (unrelated to the rest of the migration but here it is anyway)
        $this->refreshEntities([
            Client::class,
            Scope::class,
        ]);
    }
}
