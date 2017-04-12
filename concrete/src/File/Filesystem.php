<?php
namespace Concrete\Core\File;

use Concrete\Core\File\Search\ColumnSet\DefaultSet;
use Concrete\Core\File\Search\ColumnSet\FolderSet;
use Concrete\Core\File\Search\Result\Result;
use Concrete\Core\Permission\Access\Access;
use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Key\CategoryTreeNodeKey;
use Concrete\Core\File\FolderItemList;
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

    public function getFolder($folderID)
    {
        $node = Node::getByID($folderID);
        if ($node instanceof FileFolder) {
            return $node;
        }
    }

    public function getRootFolder()
    {
        $tree = FileManager::get();
        if (is_object($tree)) {
            return $tree->getRootTreeNodeObject();
        }
    }

    public function addFolder(FileFolder $folder, $name)
    {
        return $folder->add($name, $folder);
    }

}
