<?php
namespace Concrete\Core\Workflow\Progress;

use Concrete\Core\Foundation\Object as ConcreteObject;

class Response extends ConcreteObject
{
    protected $wprURL = '';

    public function setWorkflowProgressResponseURL($wprURL)
    {
        $this->wprURL = $wprURL;
    }

    public function getWorkflowProgressResponseURL()
    {
        return $this->wprURL;
    }
}
