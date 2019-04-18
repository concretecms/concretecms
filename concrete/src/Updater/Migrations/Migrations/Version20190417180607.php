<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Page\Page;

class Version20190417180607 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/users/groups/message', 'Send Message to Group', [
            'meta_keywords' => implode(', ', [
                'user',
                'group',
                'people',
                'messages',
            ])
        ]);
    }

    /**
     * @param Schema $schema
     */
    public function downgradeSchema(Schema $schema)
    {
        $page = Page::getByPath('/dashboard/users/groups/message');

        if ($page && !$page->isError()) {
            $page->delete();
        }

        $this->refreshEntities();

    }
}
