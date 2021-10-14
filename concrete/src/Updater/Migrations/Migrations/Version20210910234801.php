<?php

declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Permission\Category;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Updater\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210910234801 extends AbstractMigration implements RepeatableMigrationInterface
{

    public function upgradeDatabase()
    {
        // Remove the gathering.
        $bt = BlockType::getByHandle('core_gathering');
        if ($bt) {
            $bt->delete();
        }
        $bt = BlockType::getByHandle('core_gathering_display');
        if ($bt) {
            $bt->delete();
        }

        $this->connection->executeQuery('DROP TABLE IF EXISTS Gatherings');
        $this->connection->executeQuery('DROP TABLE IF EXISTS GatheringItemSelectedTemplates');
        $this->connection->executeQuery('DROP TABLE IF EXISTS GatheringItems');
        $this->connection->executeQuery('DROP TABLE IF EXISTS GatheringPermissionAssignments');
        $this->connection->executeQuery('DROP TABLE IF EXISTS GatheringItemFeatureAssignments');
        $this->connection->executeQuery('DROP TABLE IF EXISTS GatheringItemTemplateTypes');
        $this->connection->executeQuery('DROP TABLE IF EXISTS GatheringItemTemplates');
        $this->connection->executeQuery('DROP TABLE IF EXISTS GatheringItemTemplateFeatures');
        $this->connection->executeQuery('DROP TABLE IF EXISTS GatheringDataSources');
        $this->connection->executeQuery('DROP TABLE IF EXISTS GatheringConfiguredDataSources');

        $this->connection->executeQuery("delete from PermissionKeyCategories where pkCategoryHandle = 'gathering'");
        $this->connection->executeQuery("delete from PermissionKeys where pkHandle in ('edit_gatherings', 'edit_gathering_items')");

        // Drop features
        // Note: other tables related to this functionality were dropped in a previous migration.
        $this->connection->executeQuery('DROP TABLE IF EXISTS Features');
        $this->connection->executeQuery('DROP TABLE IF EXISTS FeatureCategories');
        $this->connection->executeQuery('DROP TABLE IF EXISTS FeatureAssignments');
        $this->connection->executeQuery('DROP TABLE IF EXISTS gaPage');

        // Other Old Things
        $this->connection->executeQuery('DROP TABLE IF EXISTS ConversationDiscussions');
    }
}
