<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20190625177710 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->createSinglePage(
            '/dashboard/system/environment/database_charset',
            'Database Character Set',
            [
                'meta_keywords' => 'database, character set, charset, collation, utf8',
            ]
        );
    }
}
