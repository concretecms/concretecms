<?php declare(strict_types=1);

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Notification\GroupCreateNotification;
use Concrete\Core\Entity\Notification\GroupRoleChangeNotification;
use Concrete\Core\Entity\Notification\GroupSignupNotification;
use Concrete\Core\Entity\Notification\GroupSignupRequestAcceptNotification;
use Concrete\Core\Entity\Notification\GroupSignupRequestDeclineNotification;
use Concrete\Core\Entity\Notification\GroupSignupRequestNotification;
use Concrete\Core\Entity\Search\SavedGroupSearch;
use Concrete\Core\Entity\User\GroupCreate;
use Concrete\Core\Entity\User\GroupRoleChange;
use Concrete\Core\Entity\User\GroupSignup;
use Concrete\Core\Entity\User\GroupSignupRequest;
use Concrete\Core\Entity\User\GroupSignupRequestAccept;
use Concrete\Core\Entity\User\GroupSignupRequestDecline;
use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Category;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;
use Concrete\Core\User\Group\FolderManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;

final class Version20210216184000 extends AbstractMigration implements RepeatableMigrationInterface
{
    public function upgradeDatabase()
    {
        $this->refreshEntities([
            SavedGroupSearch::class,
            GroupRoleChangeNotification::class,
            GroupSignupNotification::class,
            GroupSignupRequestNotification::class,
            GroupRoleChange::class,
            GroupSignup::class,
            GroupSignupRequest::class,
            GroupSignupRequestDeclineNotification::class,
            GroupSignupRequestDecline::class,
            GroupSignupRequestAcceptNotification::class,
            GroupSignupRequestAccept::class,
            GroupCreateNotification::class,
            GroupCreate::class,
        ]);

        if (Category::getByHandle("group_folder") === null) {
            $groupFolderCategory = Category::add("group_folder");

            // add permission access entity types to the category
            $groupFolderCategory->associateAccessEntityType(Type::getByHandle('group'));
            $groupFolderCategory->associateAccessEntityType(Type::getByHandle('user'));
            $groupFolderCategory->associateAccessEntityType(Type::getByHandle('group_set'));
            $groupFolderCategory->associateAccessEntityType(Type::getByHandle('group_combination'));
        }

        $key = Key::getByHandle('search_group_folder');
        if (!$key) {
            Key::add(
                'group_folder',
                'search_group_folder',
                t("Search Group Folder"),
                t("Search Group Folder"),
                false,
                false
            );
        }
        $key = Key::getByHandle('edit_group_folder');
        if (!$key) {
            Key::add('group_folder', 'edit_group_folder', t("Edit Group Folder"), t("Edit Group Folder"), false, false);
        }

        $key = Key::getByHandle('edit_group_folder_permissions');
        if (!$key) {
            Key::add('group_folder', 'edit_group_folder_permissions', t("Edit Group Access"), t("Edit Group Access"), false, false);
        }

        $key = Key::getByHandle('delete_group_folder');
        if (!$key) {
            Key::add('group_folder', 'delete_group_folder', t("Delete Group Folder"), t("Delete Group Folder"), false, false);
        }

        $key = Key::getByHandle('add_group');
        if (!$key) {
            Key::add('group_folder', 'add_group', t("Add Group"), t("Add Group"), false, false);
        }

        $key = Key::getByHandle('add_group_folder');
        if (!$key) {
            Key::add('group_folder', 'add_group_folder', t("Add Group Folder"), t("Add Group Folder"), false, false);
        }
        $key = Key::getByHandle('assign_groups');
        if (!$key) {
            Key::add('group_folder', 'assign_groups', t("Assign Groups"), t("Can assign the groups within this folder."), false, false);
        }

        $results = NodeType::getByHandle('group_folder');

        if (!is_object($results)) {
            NodeType::add('group_folder');
        }

        $folderManager = new FolderManager();
        $groupTree = $folderManager->create();

        $this->createSinglePage("/dashboard/users/group_types", t("Group Types"));

        // Create the default group type + role

        try {
            /** @var Connection $db */
            $db = $this->app->make(Connection::class);
            $db->executeQuery('insert into GroupTypes (gtID, gtName, gtDefaultRoleID) values (?,?, ?)', [DEFAULT_GROUP_TYPE_ID, t("Group"), DEFAULT_GROUP_ROLE_ID]);
            $db->executeQuery('insert into GroupRoles (grID, grName) values (?,?)', [DEFAULT_GROUP_ROLE_ID, t("Member")]);
            $db->executeQuery('insert into GroupTypeSelectedRoles (gtID, grID) values (?,?)', [DEFAULT_GROUP_TYPE_ID, DEFAULT_GROUP_ROLE_ID]);
            $db->executeQuery('update `Groups` set gtID = ?, gDefaultRoleID = ?', [DEFAULT_GROUP_TYPE_ID, DEFAULT_GROUP_ROLE_ID]);
            $db->executeQuery('update UserGroups set grID = ?', [DEFAULT_GROUP_ROLE_ID]);
        } catch (Exception $e) {
            // The group type + role was already created
        }

        // Transform permissions if necessary
        $groupTreeRootNode = $groupTree->getRootTreeNodeObject();
        if ($groupTreeRootNode) {
            $this->transformPermissionsFromGroupToGroupFolder($groupTreeRootNode, 'search_users_in_group', 'search_group_folder');
            $this->transformPermissionsFromGroupToGroupFolder($groupTreeRootNode, 'edit_group', 'edit_group_folder');
            $this->transformPermissionsFromGroupToGroupFolder($groupTreeRootNode, 'assign_group', 'assign_groups');
            $this->transformPermissionsFromGroupToGroupFolder($groupTreeRootNode, 'add_sub_group', 'add_group');
            $this->transformPermissionsFromGroupToGroupFolder($groupTreeRootNode, 'edit_group_permissions', 'edit_group_folder_permissions');
            // Now copy two other permissions that don't directly map from the edit_group permissions permission
            $this->insertGroupFolderPermissions($groupTreeRootNode, 'edit_group_folder_permissions', 'delete_group_folder');
            $this->insertGroupFolderPermissions($groupTreeRootNode, 'edit_group_folder_permissions', 'add_group_folder');
        }
    }

    private function transformPermissionsFromGroupToGroupFolder(Node $groupTreeNode, string $oldKeyHandle, string $newKeyHandle)
    {
        $db = $this->connection;
        $db->beginTransaction();
        $oldKey = Key::getByHandle($oldKeyHandle);
        $newKey = Key::getByHandle($newKeyHandle);
        if ($oldKey && $newKey) {
            $paID = $db->fetchOne('select paID from TreeNodePermissionAssignments tn where tn.pkID = ? and tn.treeNodeID = ?', [$oldKey->getPermissionKeyID(), $groupTreeNode->getTreeNodeID()]);
            if ($paID) {
                $db->update('TreeNodePermissionAssignments', ['pkID' => $newKey->getPermissionKeyID()], ['treeNodeID' => $groupTreeNode->getTreeNodeID(), 'pkID' => $oldKey->getPermissionKeyID()]);
                $this->output(t('Transforming old permission key %s to new permission key %s for tree node ID %s', $oldKeyHandle, $newKeyHandle, $groupTreeNode->getTreeNodeID()));
            } else {
                $this->output(t('Checking old permission key %s to new permission key %s for tree node ID %s, but no match found. Skipping...', $oldKeyHandle, $newKeyHandle, $groupTreeNode->getTreeNodeID()));
            }
        }
        $db->commit();
    }

    private function insertGroupFolderPermissions(Node $groupTreeNode, string $oldKeyHandle, string $newKeyHandle)
    {
        $db = $this->connection;
        $db->beginTransaction();
        $oldKey = Key::getByHandle($oldKeyHandle);
        $newKey = Key::getByHandle($newKeyHandle);
        if ($oldKey && $newKey) {
            $paID = $db->fetchOne('select paID from TreeNodePermissionAssignments tn where tn.pkID = ? and tn.treeNodeID = ?', [$oldKey->getPermissionKeyID(), $groupTreeNode->getTreeNodeID()]);
            $existingPAID = $db->fetchOne('select paID from TreeNodePermissionAssignments tn where tn.pkID = ? and tn.treeNodeID = ?', [$newKey->getPermissionKeyID(), $groupTreeNode->getTreeNodeID()]);
            if (!$existingPAID) {
                $db->insert('TreeNodePermissionAssignments', ['pkID' => $newKey->getPermissionKeyID(), 'paID' => $paID, 'treeNodeID' => $groupTreeNode->getTreeNodeID()]);
                $this->output(t('Copying permissions based on permission key %s to permission key %s for tree node ID %s', $oldKeyHandle, $newKeyHandle, $groupTreeNode->getTreeNodeID()));
            } else {
                $this->output(t('Checking permissions for permission key %s for tree node ID %s, but permissions already present. Skipping...', $newKeyHandle, $groupTreeNode->getTreeNodeID()));
            }
        }
        $db->commit();
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
