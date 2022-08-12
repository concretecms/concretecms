<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Page\Page;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

final class Version20220812035034 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        $integrations = Page::getByPath('/dashboard/system/api/integrations', 'RECENT');
        if (is_object($integrations) && !$integrations->isError()) {
            $integrations->setAttribute('exclude_nav', false);
            $integrations->setAttribute('exclude_search_index', false);
        }
    }
}
