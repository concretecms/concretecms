<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\User\GroupCreate;
use Concrete\Core\Entity\User\GroupSignup;
use Concrete\Core\Entity\User\GroupSignupRequest;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Tree\Node\Type\Group as GroupTreeNode;
use Concrete\Core\Tree\Type\Group as GroupTree;
use Concrete\Core\User\Group\DeleteEvent;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupRepository;
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

    /**
     * @var \Concrete\Core\Cache\Level\RequestCache
     */
    protected $requestCache;

    public function __construct(
        GroupRepository $groupRepository,
        Connection $connection,
        EventDispatcher $dispatcher,
        EntityManager $entityManager,
        RequestCache $requestCache
    ) {
        $this->groupRepository = $groupRepository;
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
        $this->requestCache = $requestCache;
    }

    /**
     * @return \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result|bool
     */
    public function __invoke(DeleteGroupCommand $command)
    {
        $result = new DeleteGroupCommand\Result();
        $groupID = $command->getGroupID();
        $group = $this->checkDeletableGroup($groupID, $command->isOnlyIfEmpty(), $result);
        if ($group === null) {
            return $command->isExtendedResults() ? $result : false;
        }
        $groupNode = GroupTreeNode::getTreeNodeByGroupID($groupID);
        if ($groupNode !== null) {
            if ($this->processChildGroups($command, $group, $groupNode, $result) === false) {
                return $command->isExtendedResults() ? $result : false;
            }
            $groupNode->delete();
        }
        $this->cleanupReferences($groupID);
        $result->addDeletedGroup($groupID);

        return $command->isExtendedResults() ? $result : null;
    }

    /**
     * @return \Concrete\Core\User\Group\Group|null returns NULL if the group can't be deleted (the reason will be added to $result)
     */
    private function checkDeletableGroup(int $groupID, bool $mustBeWithoutMembers, DeleteGroupCommand\Result $result): ?Group
    {
        if ($groupID === REGISTERED_GROUP_ID) {
            $result->addUndeletableGrup($groupID, t("It's not possible to delete the Registered Users group"));

            return null;
        }

        if ($groupID == GUEST_GROUP_ID) {
            $result->addUndeletableGrup($groupID, t("It's not possible to delete the Guest group"));

            return null;
        }

        $group = $this->groupRepository->getGroupById($groupID);
        if (!$group) {
            $result->addUndeletableGrup($groupID, t('Unable to find the group with ID %s', $groupID));

            return null;
        }

        if ($mustBeWithoutMembers) {
            $numMembers = (int) $this->connection->fetchOne('SELECT COUNT(gID) FROM UserGroups WHERE gID = ?', [$groupID]);
            if ($numMembers !== 0) {
                $result->addUndeletableGrup(
                    $groupID,
                    t2(
                        "The group \"%2\$s\" can't be deleted because it contains %1\$s member",
                        "The group \"%2\$s\" can't be deleted because it contains %1\$s members",
                        $numMembers,
                        $group->getGroupDisplayName(false)
                    )
                );

                return null;
            }
        }

        $ge = new DeleteEvent($group);
        $ge = $this->dispatcher->dispatch('on_group_delete', $ge);
        if (!$ge->proceed()) {
            $result->addUndeletableGrup(
                $groupID,
                t(
                    'The removal of the group "%1$s" has been cancelled by the "%2s" event',
                    $group->getGroupDisplayName(false),
                    'on_group_delete'
                )
            );

            return null;
        }

        return $group;
    }

    private function processChildGroups(DeleteGroupCommand $command, Group $group, GroupTreeNode $groupNode, DeleteGroupCommand\Result $result): bool
    {
        $groupNode->populateDirectChildrenOnly();
        switch ($command->getOnChildGroups()) {
            case DeleteGroupCommand::ONCHILDGROUPS_MOVETOROOT:
                $groupTree = GroupTree::get();
                $rootNode = $groupTree->getRootTreeNodeObject();
                if ($rootNode === null) {
                    $result->addUndeletableGrup(
                        $group->getGroupID(),
                        t("The group \"%s\" can't be deleted because we couldn't find the root tree node", $group->getGroupDisplayName(false))
                    );

                    return false;
                }
                foreach ($groupNode->getChildNodes() as $childnode) {
                    $childnode->move($rootNode);
                }
                $groupNode->clearLoadedChildren();
                break;
            case DeleteGroupCommand::ONCHILDGROUPS_MOVETOPARENT:
                $parentNode = $groupNode->getTreeNodeParentObject();
                if ($parentNode === null) {
                    $groupTree = GroupTree::get();
                    $parentNode = $groupTree->getRootTreeNodeObject();
                    if ($parentNode === null) {
                        $result->addUndeletableGrup(
                            $group->getGroupID(),
                            t("The group \"%s\" can't be deleted because we couldn't find the root tree node", $group->getGroupDisplayName(false))
                        );

                        return false;
                    }
                }
                foreach ($groupNode->getChildNodes() as $childnode) {
                    $childnode->move($parentNode);
                }
                $groupNode->clearLoadedChildren();
                break;
            case DeleteGroupCommand::ONCHILDGROUPS_ABORT:
                $numChildGroups = count($groupNode->getChildNodes());
                if ($numChildGroups !== 0) {
                    $result->addUndeletableGrup(
                        $group->getGroupID(),
                        t2(
                            "The group \"%2\$s\" can't be deleted because it contains %1\$s sub-group",
                            "The group \"%2\$s\" can't be deleted because it contains %1\$s sub-groups",
                            $numChildGroups,
                            $group->getGroupDisplayName(false)
                        )
                    );

                    return false;
                }
                break;
            case DeleteGroupCommand::ONCHILDGROUPS_DELETE:
                $childGroupCommand = clone $command;
                $childGroupCommand->setExtendedResults(true);
                foreach ($groupNode->getChildNodes() as $childGroupNode) {
                    /** @var \Concrete\Core\Tree\Node\Type\Group $childGroupNode */
                    $childGroupCommand->setGroupID($childGroupNode->getTreeNodeGroupID());
                    $result->merge($this->__invoke($childGroupCommand));
                }
                $numUndeletableGroups = $result->getNumberOfUndeletableGroups();
                if ($numUndeletableGroups !== 0) {
                    $result->addUndeletableGrup(
                        $group->getGroupID(),
                        t("The group \"%s\" can't be deleted because we couldn't delete all its child groups", $group->getGroupDisplayName(false))
                    );

                    return false;
                }
                break;
            default:
                $result->addUndeletableGrup(
                    $group->getGroupID(),
                    t("The group \"%s\" can't be deleted because we don't know how to handle its child groups", $group->getGroupDisplayName(false))
                );

                return false;
        }

        return true;
    }

    private function cleanupReferences(int $groupID): void
    {
        $groupCreates = $this->entityManager->getRepository(GroupCreate::class)->findBy(['gID' => $groupID]);
        foreach ($groupCreates as $groupCreate) {
            $this->entityManager->remove($groupCreate);
        }
        $groupSignups = $this->entityManager->getRepository(GroupSignup::class)->findBy(['gID' => $groupID]);
        foreach ($groupSignups as $groupSignup) {
            $this->entityManager->remove($groupSignup);
        }
        $groupSignupRequests = $this->entityManager->getRepository(GroupSignupRequest::class)->findBy(['gID' => $groupID]);
        foreach ($groupSignupRequests as $groupSignupRequest) {
            $this->entityManager->remove($groupSignupRequest);
        }
        $this->entityManager->flush();
        $table = $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups');
        $this->connection->query('DELETE FROM UserGroups WHERE gID = ?', [$groupID]);
        $this->connection->query('DELETE FROM ' . $table . ' WHERE gID = ?', [$groupID]);
        $this->connection->query('delete from GroupSelectedRoles where gID = ?', [$groupID]);
        $this->requestCache->delete('tree/node');
    }
}
