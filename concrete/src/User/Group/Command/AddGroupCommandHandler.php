<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Notification\GroupCreateNotification;
use Concrete\Core\Entity\User\GroupCreate;
use Concrete\Core\Notification\Type\GroupCreateType;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\User\Group\Event;
use Concrete\Core\User\Group\GroupRepository;
use Concrete\Core\User\User;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Tree\Node\Type\Group as GroupNode;
use Concrete\Core\Tree\Type\Group as GroupTree;

class AddGroupCommandHandler
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


    public function __construct(
        GroupRepository $groupRepository,
        Connection $connection,
        EventDispatcher $dispatcher
    )
    {
        $this->groupRepository = $groupRepository;
        $this->connection = $connection;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(AddGroupCommand $command)
    {
        $app = app();
        $user = $app->make(User::class);

        $data = [
            'gName' => $command->getName(),
            'gDescription' => $command->getDescription(),
            'pkgID' => (int) $command->getPackageID(),
            'gAuthorID' => (int) $user->getUserID()
        ];
        $newGroupID = $command->getForcedNewGroupID();
        if ($newGroupID !== null && $newGroupID > 0) {
            $data['gID'] = $newGroupID;
        } else {
            $newGroupID = null;
        }
        $this->connection->insert(
            $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups'),
            $data
        );

        if ($newGroupID === null) {
            $newGroupID = (int) $this->connection->lastInsertId();
        }
        $ng = $this->groupRepository->getGroupById($newGroupID);
        $node = null;
        if ($command->getParentGroupID()) {
            $node = GroupNode::getTreeNodeByGroupID($command->getParentGroupID());
        } else if ($command->getParentNodeID()) {
            $node = TreeNode::getByID($command->getParentNodeID());
        }
        if (!is_object($node)) {
            $tree = GroupTree::get();
            if (is_object($tree)) {
                $node = $tree->getRootTreeNodeObject();
            }
        }

        if (is_object($node)) {
            GroupNode::add($ng, $node);
        }
        $ng->rescanGroupPath();

        $ge = new Event($ng);
        $this->dispatcher->dispatch('on_group_add', $ge);

        if ($user->isRegistered()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $subject = new GroupCreate($ng, $user);
            /** @var GroupCreateType $type */
            $type = $app->make('manager/notification/types')->driver('group_create');
            $notifier = $type->getNotifier();
            if (method_exists($notifier, 'notify')) {
                $subscription = $type->getSubscription($subject);
                $users = $notifier->getUsersToNotify($subscription, $subject);
                $notification = new GroupCreateNotification($subject);
                $subject->getNotifications()->add($notification);
                $notifier->notify($users, $notification);
            }
        }

        return $ng;
    }


}