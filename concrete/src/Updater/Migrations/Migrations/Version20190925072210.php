<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * @see https://github.com/concrete5/concrete5/issues/8127
 */
class Version20190925072210 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgradeDatabase()
    {
        $this->createSinglePage('/dashboard/system/express/entities/order_entries', 'Update Entry Display Order', ['exclude_nav' => true, 'exclude_search_index' => true]);
    }
}
