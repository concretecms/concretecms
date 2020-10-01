<?php

namespace Concrete\Core\User\Group\Command;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\User\Group\Command\Traits\ParentNodeRetrieverTrait;
use Concrete\Core\User\Group\Event;
use Concrete\Core\User\Group\GroupRepository;
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

    public function handle(AddGroupCommand $command)
    {
        $data = [
            'gName' => $command->getName(),
            'gDescription' => $command->getDescription(),
            'pkgID' => (int) $command->getPackageID(),
        ];
        $this->connection->insert(
            $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups'),
            $data
        );

        $ng = $this->groupRepository->getGroupById($this->connection->lastInsertId());
        $node = null;
        if ($command->getParentGroupID()) {
            $node = GroupNode::getTreeNodeByGroupID($command->getParentGroupID());
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

        $ge = new Event($ng);
        $this->dispatcher->dispatch('on_group_add', $ge);

        $ng->rescanGroupPath();

        return $ng;
    }


}