<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20170313000000 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // Move all stacks to the root. Putting them in a site was a mistake.
        $this->connection->executeQuery('
            update Pages
            left join PageTypes on Pages.ptID = PageTypes.ptID
            set Pages.siteTreeID = 0
            where PageTypes.ptHandle in (?, ?)
            or Pages.cFilename = ?',
            [STACK_CATEGORY_PAGE_TYPE, STACKS_PAGE_TYPE, '/!stacks/view.php']
        );
    }
}
