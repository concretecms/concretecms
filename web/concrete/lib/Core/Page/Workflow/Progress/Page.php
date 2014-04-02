<?
namespace Concrete\Core\Page\Workflow\Progress;
class Page {

	public function __construct(Page $p, WorkflowProgress $wp) {
		$this->page = $p;
		$this->wp = $wp;
	}
	
	public function getPageObject() {return $this->page;}
	public function getWorkflowProgressObject() {return $this->wp;}
	
}