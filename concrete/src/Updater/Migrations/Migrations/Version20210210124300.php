<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Entity\Search\SavedGroupSearch;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\User\Group\FolderManager;
use Doctrine\DBAL\Schema\Schema;

final class Version20210210124300 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            SavedGroupSearch::class
        ]);

        if (Category::getByHandle("group_folder") === null) {
            Category::add("group_folder");
        }

        if (Key::getByHandle("search_group_folder") === null) {
            Key::add('group_folder', 'search_group_folder', t("Search Group Folder"), t("Search Group Folder"), false, false);
            Key::add('group_folder', 'edit_group_folder', t("Edit Group Folder"), t("Edit Group Folder"), false, false);
            Key::add('group_folder', 'edit_group_folder_permissions', t("Edit Group Access"), t("Edit Group Access"), false, false);
            Key::add('group_folder', 'delete_group_folder', t("Delete Group Folder"), t("Delete Group Folder"), false, false);
            Key::add('group_folder', 'add_group', t("Add Group"), t("Add Group"), false, false);
        }

        $results = NodeType::getByHandle('group_folder');

        if (!is_object($results)) {
            NodeType::add('group_folder');
        }

        $folderManager = new FolderManager();
        $folderManager->create();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeSchema()
     */
    public function upgradeSchema(Schema $schema)
    {
        // Create the TreeFileFolderNodes table
        if (!$schema->hasTable('TreeGroupFolderNodes')) {
            $treeFileFolderNodesTable = $schema->createTable('TreeGroupFolderNodes');
            $treeFileFolderNodesTable->addColumn('treeNodeID', 'integer', ['notnull' => true, 'unsigned' => true, 'autoincrement' => true]);
            $treeFileFolderNodesTable->setPrimaryKey(['treeNodeID']);
        }
    }
}
