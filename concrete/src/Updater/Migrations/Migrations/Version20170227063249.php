<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Package\Package;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Conversations Ratings Page Review Migration
 */
class Version20170227063249 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Install the core_conversations db_xml
        $this->refreshBlockType('core_conversation');
        $this->refreshDatabaseTables(['ConversationMessages']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // This migration doesn't migrate down
    }
}
