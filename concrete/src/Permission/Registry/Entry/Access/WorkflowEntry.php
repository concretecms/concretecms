<?php
namespace Concrete\Core\Permission\Registry\Entry\Access;

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\EntityInterface;
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

    public function apply($mixed)
    {
        $workflow = Workflow::getByName($this->workflowName);
        $key = Key::getByHandle($this->pkHandle);
        $key->setPermissionObject($mixed->getPermissionObject());
        $assignment = $key->getPermissionAssignmentObject();
        $access = $assignment->getPermissionAccessObject();
        if (is_object($access)) {
            $access->attachWorkflow($workflow);
        }
    }

    public function remove($mixed)
    {
        $workflow = Workflow::getByName($this->workflowName);
        $key = Key::getByHandle($this->pkHandle);
        $key->setPermissionObject($mixed->getPermissionObject());
        $assignment = $key->getPermissionAssignmentObject();
        $access = $assignment->getPermissionAccessObject();
        if (is_object($access)) {
            $access->removeWorkflow($workflow);
        }
    }



}

