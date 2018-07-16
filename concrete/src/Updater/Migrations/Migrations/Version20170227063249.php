<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

/**
 * Conversations Ratings Page Review Migration.
 */
class Version20170227063249 extends AbstractMigration implements RepeatableMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        // Install the core_conversations db_xml
        $this->refreshBlockType('core_conversation');
        $this->refreshDatabaseTables(['ConversationMessages']);
    }
}
