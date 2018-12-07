<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20181207085100 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->createSinglePage(
            '/dashboard/system/files/export_options',
            'Export Options',
            [
                'meta_keywords' => 'files, export, csv, bom, encoding',
            ]
        );
    }
}
