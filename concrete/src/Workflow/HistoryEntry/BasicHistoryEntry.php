<?php
namespace Concrete\Core\Workflow\HistoryEntry;
use UserInfo;

class BasicHistoryEntry extends HistoryEntry {

	public function getWorkflowProgressHistoryDescription() {
		$uID = $this->getRequesterUserID();
		$ux = UserInfo::getByID($uID);
		switch($this->getAction()) {
			case 'approve':
				$d = t('Approved by %s', $ux->getUserName());
				break;
			case 'cancel':
				$d = t('Denied by %s', $ux->getUserName());
				break;
		}
		if ($this->getWorkflowStepComments()) {
			$d .= t(' with the comments "%s"', $this->getWorkflowStepComments());
		}

		return $d;
	}
}

