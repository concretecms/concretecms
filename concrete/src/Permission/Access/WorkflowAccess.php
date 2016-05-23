<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Workflow\Progress\Progress;

class WorkflowAccess extends Access
{
    public function setWorkflowProgressObject(Progress $wp)
    {
        $this->wp = $wp;
    }

    public function getWorkflowProgressObject()
    {
        return $this->wp;
    }
}
