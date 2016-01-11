<?php
namespace Concrete\Core\Workflow\Progress\Action;

class CancelAction extends Action
{
    public function __construct()
    {
        $this->setWorkflowProgressActionLabel(t('Cancel'));
        $this->setWorkflowProgressActionTask('cancel');
        $this->setWorkflowProgressActionStyleClass('btn-default');
    }
}
