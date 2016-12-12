<?php
namespace Concrete\Core\Workflow\Progress\Action;
class ApprovalAction extends Action {

	public function __construct() {
		$this->setWorkflowProgressActionLabel(t('Approve'));
		$this->setWorkflowProgressActionTask('approve');
	}

}
