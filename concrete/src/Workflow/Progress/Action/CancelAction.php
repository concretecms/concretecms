<?php
namespace Concrete\Core\Workflow\Progress\Action;

class CancelAction extends Action
{
    public function __construct()
    {
        $this->setWorkflowProgressActionLabel(t('Deny'));
        $this->setWorkflowProgressActionTask('cancel');
    }
}
