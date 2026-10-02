<?php
namespace Concrete\Core\Permission\Registry\Entry\Object\Object;

use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Permission\Registry\Entry\Object\Object\ObjectInterface;

class BasicWorkflow implements ObjectInterface
{

    protected $wfName;

    public function __construct($wfName)
    {
        $this->wfName = $wfName;
    }

    public function getPermissionObject()
    {
        // getByName() returns an instance of the class corresponding to the type of the workflow with that name
        $workflow = \Concrete\Core\Workflow\BasicWorkflow::getByName($this->wfName);

        return $workflow instanceof \Concrete\Core\Permission\AssignableObjectInterface ? $workflow : null;
    }


}
