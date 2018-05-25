<?php

namespace Concrete\Core\File;

use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Key\CategoryTreeNodeKey;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\FileManager;
use Concrete\Core\User\Group\Group;

class Filesystem
{
    /**
     * Creates everything necessary to store files in folders.
     *
     * @return \Concrete\Core\Tree\Type\FileManager
     */
    public function create()
    {
        $type = NodeType::getByHandle('file');
        if (!is_object($type)) {
            NodeType::add('file');
        }
        $type = NodeType::getByHandle('file_folder');
        if (!is_object($type)) {
            NodeType::add('file_folder');
        }
        $type = NodeType::getByHandle('search_preset');
        if (!is_object($type)) {
            NodeType::add('search_preset');
        }
        $type = TreeType::getByHandle('file_manager');
        if (!is_object($type)) {
            TreeType::add('file_manager');
        }

        $manager = FileManager::get();
        if (!is_object($manager)) {
            $manager = FileManager::add();
        }

        return $manager;
    }

    public function setDefaultPermissions(FileManager $tree)
    {
        $rootNode = $tree->getRootTreeNodeObject();
        $adminGroupEntity = GroupEntity::getOrCreate(Group::getByID(ADMIN_GROUP_ID));
        $pk = CategoryTreeNodeKey::getByHandle('view_category_tree_node');
        $pk->setPermissionObject($rootNode);
        $pa = Access::create($pk);
        $pa->addListItem($adminGroupEntity);
        $pt = $pk->getPermissionAssignmentObject();
        $pt->assignPermissionAccess($pa);
    }

    /**
     * Get a folder given its ID.
     *
     * @param mixed $folderID
     *
     * @return \Concrete\Core\Tree\Node\Type\FileFolder|null
     */
    public function getFolder($folderID)
    {
        $node = Node::getByID($folderID);
        if ($node instanceof FileFolder) {
            return $node;
        }
    }

    /**
     * Get the root folder.
     *
     * @return \Concrete\Core\Tree\Node\Type\FileFolder|null
     */
    public function getRootFolder()
    {
        $tree = FileManager::get();
        if ($tree !== null) {
            return $tree->getRootTreeNodeObject();
        }
    }

    /**
     * Create a new folder.
     *
     * @param FileFolder $folder The parent folder
     * @param string $name The name of the new folder
     *
     * @return \Concrete\Core\Tree\Node\Type\FileFolder
     */
    public function addFolder(FileFolder $folder, $name)
    {
        return $folder->add($name, $folder);
    }
}
