<?php
namespace Concrete\Core\Workflow\Progress\Action;
use \Concrete\Core\Foundation\Object;
class CancelAction extends Action {

	public function __construct() {
		$this->setWorkflowProgressActionLabel(t('Cancel'));
		$this->setWorkflowProgressActionTask('cancel');
		$this->setWorkflowProgressActionStyleClass('btn-default');
	}

}
