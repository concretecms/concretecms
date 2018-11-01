<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181019010145 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // Delete the old singlepage
        $oldPage = Page::getByPath('/dashboard/system/permissions/security');
        if ($oldPage && $oldPage->getCollectionID()) {
            $oldPage->delete();
        }

        // Create a new singlepage
        $this->createSinglePage('/dashboard/system/registration/automated_logout', 'Automated Logout', [
            'login',
            'logout',
            'user',
            'agent',
            'ip',
            'change',
            'security',
            'session',
            'invalidation',
            'invalid'
        ]);
    }
}
