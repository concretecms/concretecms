<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20181101000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        // Create the new Security Options single page
        $this->createSinglePage('/dashboard/system/basics/security', 'Security Options', [
            'security options',
        ]);
    }
}
