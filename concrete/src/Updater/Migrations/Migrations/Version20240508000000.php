<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\Updater\Migrations\AbstractMigration;

final class Version20240508000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->refreshDatabaseTables(['Logs']);
        $page = Page::getByPath('/dashboard/extend/connect');
        if ($page && !$page->isError()) {
            $page->delete();
        }
        $this->createSinglePage('/dashboard/system/basics/marketplace', 'Marketplace',
            [
                'meta_description' => 'Connect to the Concrete CMS marketplace.',
                'meta_keywords' => 'concretecms.com, my account, purchase, extensions, marketplace',
            ]
        );
    }
}
