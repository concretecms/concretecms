<?php
namespace Concrete\Core\Updater\Migrations\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version571 extends AbstractMigration
{

    public function getName()
    {
        return '20141008000000';
    }

    public function up(Schema $schema)
    {
        /** @todo refresh CollectionVersionBlocks, CollectionVersionBlocksCacheSettings tables */
        /** @todo add permissions lines for edit_block_name and edit_block_cache_settings */
        /** @todo Add marketplace single pages */
        /** @todo Install the community authentication type */
        /** @todo delete customize page themes dashboard single page */
        /** @todo Add auth types ("handle|name") "twitter|Twitter" and "community|concrete5.org" */
        /** @remove exclude nav from flat view in dashboard */
    }

    public function down(Schema $schema)
    {
    }
}
