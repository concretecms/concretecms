<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Announcement\AnnouncementService;
use Concrete\Core\Entity\Announcement\Announcement;
use Concrete\Core\Entity\Announcement\AnnouncementUserView;
use Concrete\Core\Entity\OAuth\Client;
use Concrete\Core\Entity\OAuth\Scope;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20230225150942 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $pk = Key::getByHandle('access_api');
        if (!$pk instanceof Key) {
            Key::add('admin', 'access_api', 'Access API', '', false, false);
        }
        $pk = Key::getByHandle('view_announcement_content');
        if (!$pk instanceof Key) {
            Key::add(
                'admin',
                'view_announcement_content',
                'View Announcement Content',
                'Controls whether a user sees the announcement modal interstitial, including upgrades and help, upon login.',
                false,
                false
            );
        }

        // Fix an oauth2 client bug (unrelated to the rest of the migration but here it is anyway)
        $this->refreshEntities(
            [
                Client::class,
                Scope::class,
            ]
        );

        $this->createSinglePage(
            '/dashboard/system/basics/site_information',
            'Site Information',
            ['exclude_search_index' => true]
        );

        $this->refreshEntities(
            [
                Announcement::class,
                AnnouncementUserView::class,
            ]
        );
        $service = $this->app->make(AnnouncementService::class);
        $service->createAnnouncementIfNotExists('concrete_version_920');
    }

}
