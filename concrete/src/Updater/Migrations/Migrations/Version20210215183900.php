<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Search\SavedGroupSearch;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\User\Group\FolderManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;

final class Version20210215183900 extends AbstractMigration implements RepeatableMigrationInterface
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

        $this->createSinglePage("/dashboard/users/group_types", t("Group Types"));

        // Create the default group type + role

        try {
            /** @var Connection $db */
            $db = $this->app->make(Connection::class);
            $db->executeQuery('insert into GroupTypes (gtID, gtName, gtDefaultRoleID) values (?,?, ?)', [DEFAULT_GROUP_TYPE_ID, t("Group"), DEFAULT_GROUP_ROLE_ID]);
            $db->executeQuery('insert into GroupRoles (grID, grName) values (?,?)', [DEFAULT_GROUP_ROLE_ID, t("Member")]);
            $db->executeQuery('insert into GroupTypeSelectedRoles (gtID, grID) values (?,?)', [DEFAULT_GROUP_TYPE_ID, DEFAULT_GROUP_ROLE_ID]);
            $db->executeQuery('update Groups set gtID = ?, gDefaultRoleID = ?', [DEFAULT_GROUP_TYPE_ID, DEFAULT_GROUP_ROLE_ID]);
            $db->executeQuery('update UserGroups set grID = ?', [DEFAULT_GROUP_ROLE_ID]);
        } catch (Exception $e) {
            // The group type + role was already created
        }
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
            $treeFileFolderNodesTable->addColumn('contains', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $treeFileFolderNodesTable->setPrimaryKey(['treeNodeID']);
        }

        // Create the TreeFileFolderNodes table
        if (!$schema->hasTable('TreeGroupFolderNodeSelectedGroupTypes')) {
            $treeGroupFolderNodeSelectedGroupTypesTable = $schema->createTable('TreeGroupFolderNodeSelectedGroupTypes');
            $treeGroupFolderNodeSelectedGroupTypesTable->addColumn('id', 'integer', ['notnull' => true, 'unsigned' => true, 'autoincrement' => true]);
            $treeGroupFolderNodeSelectedGroupTypesTable->addColumn('treeNodeID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $treeGroupFolderNodeSelectedGroupTypesTable->addColumn('gtID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $treeGroupFolderNodeSelectedGroupTypesTable->setPrimaryKey(['id']);
        }

        // Create the GroupTypes table
        if (!$schema->hasTable('GroupTypes')) {
            $groupTypesTable = $schema->createTable('GroupTypes');
            $groupTypesTable->addColumn('gtID', 'integer', ['notnull' => true, 'unsigned' => true, 'autoincrement' => true]);
            $groupTypesTable->addColumn('gtName', 'string', ['notnull' => true, 'default' => '', 'length' => 128]);
            $groupTypesTable->addColumn('gtPetitionForPublicEntry', 'boolean', ['notnull' => true, 'default' => '0']);
            $groupTypesTable->addColumn('gtDefaultRoleID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $groupTypesTable->setPrimaryKey(['gtID']);
        }

        // Create the GroupRoles table
        if (!$schema->hasTable('GroupRoles')) {
            $groupRolesTable = $schema->createTable('GroupRoles');
            $groupRolesTable->addColumn('grID', 'integer', ['notnull' => true, 'unsigned' => true, 'autoincrement' => true]);
            $groupRolesTable->addColumn('grName', 'string', ['notnull' => true, 'default' => '', 'length' => 128]);
            $groupRolesTable->addColumn('grIsManager', 'boolean', ['notnull' => true, 'default' => '0']);
            $groupRolesTable->setPrimaryKey(['grID']);
        }

        // Create the GroupSelectedRoles table
        if (!$schema->hasTable('GroupSelectedRoles')) {
            $groupSelectedRolesTable = $schema->createTable('GroupSelectedRoles');
            $groupSelectedRolesTable->addColumn('gID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $groupSelectedRolesTable->addColumn('grID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $groupSelectedRolesTable->setPrimaryKey(['gID', 'grID']);
        }

        // Create the GroupSelectedRoles table
        if (!$schema->hasTable('GroupTypeSelectedRoles')) {
            $groupTypeSelectedRolesTable = $schema->createTable('GroupTypeSelectedRoles');
            $groupTypeSelectedRolesTable->addColumn('gtID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $groupTypeSelectedRolesTable->addColumn('grID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $groupTypeSelectedRolesTable->setPrimaryKey(['gtID', 'grID']);
        }

        // Create the GroupJoinRequests table
        if (!$schema->hasTable('GroupJoinRequests')) {
            $groupJoinRequestsTable = $schema->createTable('GroupJoinRequests');
            $groupJoinRequestsTable->addColumn('uID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $groupJoinRequestsTable->addColumn('gID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
            $groupJoinRequestsTable->addColumn('gjrRequested', 'datetime', ['notnull' => false]);
            $groupJoinRequestsTable->setPrimaryKey(['uID', 'gID']);
        }

        // Extend the Groups table
        $groupsTable = $schema->getTable('Groups');

        if (!$groupsTable->hasColumn('gThumbnailFID')) {
            $groupsTable->addColumn('gThumbnailFID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
        }

        if (!$groupsTable->hasColumn('gtID')) {
            $groupsTable->addColumn('gtID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
        }

        if (!$groupsTable->hasColumn('gPetitionForPublicEntry')) {
            $groupsTable->addColumn('gPetitionForPublicEntry', 'boolean', ['notnull' => true, 'default' => '0']);
        }

        if (!$groupsTable->hasColumn('gOverrideGroupTypeSettings')) {
            $groupsTable->addColumn('gOverrideGroupTypeSettings', 'boolean', ['notnull' => true, 'default' => '0']);
        }

        if (!$groupsTable->hasColumn('gDefaultRoleID')) {
            $groupsTable->addColumn('gDefaultRoleID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
        }

        if (!$groupsTable->hasColumn('gAuthorID')) {
            $groupsTable->addColumn('gAuthorID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
        }

        // Extend the User Groups table
        $userGroupsTable = $schema->getTable('UserGroups');

        if (!$userGroupsTable->hasColumn('grID')) {
            $userGroupsTable->addColumn('grID', 'integer', ['notnull' => true, 'unsigned' => true, 'default' => '0']);
        }
    }
}
