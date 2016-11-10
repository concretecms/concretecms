<?php
namespace Concrete\Core\Permission\Registry\Entry;

use Concrete\Core\Permission\Access\Entity\GroupEntity;
use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Workflow\Workflow;

class WorkflowEntry implements EntryInterface
{

    protected $workflowName;
    protected $pkHandle;

    public function __construct($workflowName, $pkHandle)
    {
        $this->pkHandle = $pkHandle;
        $this->workflowName = $workflowName;
    }

    public function apply(AssignableObjectInterface $object)
    {
        $key = Key::getByHandle($this->pkHandle);
        $key->setPermissionObject($object);
        $workflow = Workflow::getByName($this->workflowName);
        $assignment = $key->getPermissionAssignmentObject();
        $access = $assignment->getPermissionAccessObject();
        if (is_object($access)) {
            $access->attachWorkflow($workflow);
        }
    }

}
