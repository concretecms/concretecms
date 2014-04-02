<?
namespace Concrete\Core\Workflow\Progress;
use \Concrete\Core\Foundation\Object;
class History extends Object {  

	public function getWorkflowProgressHistoryTimestamp() {return $this->timestamp;}
	public function getWorkflowProgressHistoryID() {return $this->wphID;}
	public function getWorkflowProgressID() {return $this->wpID;}
	public function getWorkflowProgressHistoryInnerObject() {return $this->object;}
	
	public function getWorkflowProgressHistoryDescription() {
		if ($this->object instanceof WorkflowRequest) {
			$d = $this->object->getWorkflowRequestDescriptionObject();
			$ui = UserInfo::getByID($this->object->getRequesterUserID());
			return $d->getDescription() . ' ' . t('Originally requested by %s.', $ui->getUserName());
		}
		if ($this->object instanceof WorkflowHistoryEntry) {
			$d = $this->object->getWorkflowProgressHistoryDescription();
			return $d;
		}
	}
	
	public static function getList(WorkflowProgress $wp) {
		$db = Loader::db();
		$r = $db->Execute('select wphID from WorkflowProgressHistory where wpID = ? order by timestamp desc', array($wp->getWorkflowProgressID()));
		$list = array();
		while ($row = $r->FetchRow()) {
			$obj = $wp->getWorkflowProgressHistoryObjectByID($row['wphID']);
			if (is_object($obj)) {
				$list[] = $obj;
			}
		}
		return $list;
	}
}