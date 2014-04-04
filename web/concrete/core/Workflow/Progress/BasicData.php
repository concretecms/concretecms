<?
namespace Concrete\Core\Workflow\Progress;
class BasicData   {
	
	public $_table = 'BasicWorkflowProgressData';
	
	public function __construct($wp) {
		parent::__construct();
		$this->load('wpID=' . $wp->getWorkflowProgressID());
	}
	
	public function getUserStartedID() {return $this->uIDStarted;}
	public function getUserCompletedID() {return $this->uIDCompleted;}
	public function getDateCompleted() {return $this->wpDateCompleted;}
	
	public function markCompleted($u) {
		$this->wpDateCompleted = Loader::helper('date')->getLocalDateTime();
		$this->uIDCompleted = $u->getUserID();
		$this->save();
	}
		
}