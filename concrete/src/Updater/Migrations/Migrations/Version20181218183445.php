<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181218183445 extends AbstractMigration implements RepeatableMigrationInterface
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

        $sp = Page::getByPath('/dashboard/system/registration/open');
        if (is_object($sp) && !$sp->isError()) {
            $sp->update([
                'cName' => 'Account Options',
            ]);
            if ($this->isAttributeHandleValid(PageCategory::class, 'meta_keywords')) {
                $sp->setAttribute('meta_keywords', 'signup, new user, community, public registration, public, registration');
            }
        }
    }

    public function downgradeSchema(Schema $schema)
    {
        $page = Page::getByPath('/dashboard/system/registration/password_requirements');

        if ($page && !$page->isError()) {
            $page->delete();
        }
    }
}
