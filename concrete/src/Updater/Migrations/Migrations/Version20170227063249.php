<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface;

/**
 * Conversations Ratings Page Review Migration.
 */
class Version20170227063249 extends AbstractMigration implements DirectSchemaUpgraderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\DirectSchemaUpgraderInterface::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // Install the core_conversations db_xml
        $this->refreshBlockType('core_conversation');
        $this->refreshDatabaseTables(['ConversationMessages']);
    }
}
