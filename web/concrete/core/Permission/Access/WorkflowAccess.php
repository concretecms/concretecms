<?
namespace Concrete\Core\Permission\Access;
class WorkflowAccess extends Access {
	
	public function setWorkflowProgressObject(WorkflowProgress $wp) {
		$this->wp = $wp;
	}
	
	public function getWorkflowProgressObject() {
		return $this->wp;
	}
	
}