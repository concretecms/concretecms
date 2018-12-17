<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

class Version20181112001755 extends AbstractMigration implements RepeatableMigrationInterface
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

    public function downgradeSchema(Schema $schema)
    {
        $page = Page::getByPath('/dashboard/system/registration/password_requirements');

        if ($page && !$page->isError()) {
            $page->delete();
        }
    }

}
