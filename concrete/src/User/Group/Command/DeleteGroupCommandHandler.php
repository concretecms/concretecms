<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\User\GroupCreate;
use Concrete\Core\Entity\User\GroupSignup;
use Concrete\Core\Entity\User\GroupSignupRequest;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\User\Group\DeleteEvent;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\Events\EventDispatcher;
use Doctrine\ORM\EntityManager;

class DeleteGroupCommandHandler
{

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(
        GroupRepository $groupRepository,
        Connection $connection,
        EventDispatcher $dispatcher,
        EntityManager  $entityManager
    )
    {
        $this->groupRepository = $groupRepository;
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
    }

    public function __invoke(DeleteGroupCommand $command)
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

        if ($command->isOnlyIfEmpty()) {
            if ($this->connection->fetchOne('SELECT gID FROM UserGroups gID = ? LIMIT 1', [$groupID]) !== false) {
                return false;
            }
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

        $groupCreates = $this->entityManager->getRepository(GroupCreate::class)
            ->findBy(['gID' => $command->getGroupID()]);
        foreach ($groupCreates as $groupCreate) {
            $this->entityManager->remove($groupCreate);
        }
        $groupSignups = $this->entityManager->getRepository(GroupSignup::class)
            ->findBy(['gID' => $command->getGroupID()]);
        foreach ($groupSignups as $groupSignup) {
            $this->entityManager->remove($groupSignup);
        }
        $groupSignupRequests = $this->entityManager->getRepository(GroupSignupRequest::class)
            ->findBy(['gID' => $command->getGroupID()]);
        foreach ($groupSignupRequests as $groupSignupRequest) {
            $this->entityManager->remove($groupSignupRequest);
        }
        $this->entityManager->flush();

        $table = $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups');
        $this->connection->query('DELETE FROM UserGroups WHERE gID = ?', [intval($groupID)]);
        $this->connection->query('DELETE FROM ' . $table . ' WHERE gID = ?', [(int) $groupID]);
        $this->connection->query('delete from GroupSelectedRoles where gID = ?', [(int) $groupID]);
    }


}