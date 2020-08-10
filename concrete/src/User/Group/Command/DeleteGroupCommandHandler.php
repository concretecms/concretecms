<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\User\Group\DeleteEvent;
use Concrete\Core\User\Group\GroupRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteGroupCommandHandler
{

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;


    public function __construct(
        GroupRepository $groupRepository,
        Connection $connection,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->groupRepository = $groupRepository;
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
    }

    public function handle(DeleteGroupCommand $command)
    {
        $groupID = $command->getGroupID();
        // we will NOT let you delete the required groups
        if ($groupID == REGISTERED_GROUP_ID || $groupID == GUEST_GROUP_ID) {
            return false;
        }

        $group = $this->groupRepository->getGroupById($groupID);
        if (!$group) {
            return false;
        }

        // run any internal event we have for group deletion
        $ge = new DeleteEvent($group);
        $ge = $this->dispatcher->dispatch('on_group_delete', $ge);
        if (!$ge->proceed()) {
            return false;
        }

        $tree = GroupTree::get();
        $rootNode = $tree->getRootTreeNodeObject();
        $node = GroupTreeNode::getTreeNodeByGroupID($groupID);
        if (is_object($node) && is_object($rootNode)) {
            $node->populateDirectChildrenOnly();
            foreach ($node->getChildNodes() as $childnode) {
                $childnode->move($rootNode);
            }
            $node = GroupTreeNode::getTreeNodeByGroupID($groupID);
            $node->delete();
        }

        $table = $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups');
        $this->connection->query('DELETE FROM UserGroups WHERE gID = ?', [intval($groupID)]);
        $this->connection->query('DELETE FROM ' . $table . ' WHERE gID = ?', [(int) $groupID]);
    }


}