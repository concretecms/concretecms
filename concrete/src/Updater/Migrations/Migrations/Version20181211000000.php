<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20181211000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        // Create the 'Page Changes' single page
        $this->createSinglePage('/dashboard/reports/page_changes', 'Page Changes', [
            'meta_keywords' => 'changes, csv, report'
        ]);
    }
}
