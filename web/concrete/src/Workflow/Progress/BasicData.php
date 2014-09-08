<?php
namespace Concrete\Core\Workflow\Progress;
use Loader;
class BasicData {

	public function __construct($wp) {
		$db = Loader::db();
		$r = $db->GetRow('select * from BasicWorkflowProgressData where wpID = ?', array($wp->getWorkflowProgressID()));
		if (is_array($r) && $r['wpID']) {
			$this->uIDStarted = $r['uIDStarted'];
			$this->uIDCompleted = $r['uIDCompleted'];
			$this->wpDateCompleted = $r['wpDateCompleted'];
			$this->wpID = $wp->getWorkflowProgressID();
		}
	}

	public function delete() {
		$db = Loader::db();
		$db->delete('BasicWorkflowProgressData', array('wpID' => $this->wpID));
	}

	public function getUserStartedID() {return $this->uIDStarted;}
	public function getUserCompletedID() {return $this->uIDCompleted;}
	public function getDateCompleted() {return $this->wpDateCompleted;}

	public function markCompleted($u) {
		$db = Loader::db();
		$this->wpDateCompleted = Loader::helper('date')->getOverridableNow();
		$this->uIDCompleted = $u->getUserID();
		$db->update('BasicWorkflowProgressData', array('wpDateCompleted' => $this->wpDateCompleted, 'uIDCompleted' => $this->uIDCompleted), array('wpID' => $this->wpID));
	}

}
