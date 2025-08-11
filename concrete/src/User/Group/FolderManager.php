<?php

namespace Concrete\Core\User\Group;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\NodeType;
use Concrete\Core\Tree\Node\Type\GroupFolder;

class FolderManager
{

    /**
     * Creates everything necessary to store files in folders.
     *
     * @return \Concrete\Core\Tree\Type\Group
     * @throws \Doctrine\DBAL\Exception
     */
    public function create()
    {
        $type = NodeType::getByHandle('group_folder');

        if (!is_object($type)) {
            $type = NodeType::add('group_folder');
        }

        $groupTree = \Concrete\Core\Tree\Type\Group::get();

        // transform the parent group node to a group folder
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $db->executeQuery("UPDATE TreeNodes n SET n.treeNodeTypeID = ? WHERE n.treeNodeID = ?", [
            $type->getTreeNodeTypeID(),
            $groupTree->getRootTreeNodeID()
        ]);

        return $groupTree;
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
        if ($node instanceof GroupFolder) {
            return $node;
        }
    }

    /**
     * Get the root folder.
     *
     * @return \Concrete\Core\Tree\Node\Type\GroupFolder|null
     */
    public function getRootFolder()
    {
        $tree = \Concrete\Core\Tree\Type\Group::get();
        if ($tree !== null) {
            return $tree->getRootTreeNodeObject();
        }
    }

    /**
     * Create a new folder.
     *
     * @param GroupFolder $folder The parent folder
     * @param string $name The name of the new folder
     *
     * @return \Concrete\Core\Tree\Node\Type\GroupFolder
     */
    public function addFolder(GroupFolder $folder, $name)
    {
        return $folder->add($name, $folder);
    }
}
