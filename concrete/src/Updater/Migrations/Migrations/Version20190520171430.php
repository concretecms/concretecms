<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;


class Version20190520171430 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/registration/password_requirements', 'Password Requirements', [
            'meta_keywords' => implode(', ', [
                'password',
                'requirements',
                'code',
                'key',
                'login',
                'registration',
                'security',
                'nist',
            ])
        ]);
    }

}
