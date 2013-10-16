<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageTypePublishResponse extends PageEditResponse {

	public $outputControls = array();
	public $saveURL;
	public $saveStatus;
	public $discardURL;
	public $viewURL;
	
	public function setSaveStatus($saveStatus) {
		$this->saveStatus = $saveStatus;
	}

	public function setOutputControls($outputControls) {
		$this->outputControls = $outputControls;
	}


	public function setSaveURL($saveURL) {
		$this->saveURL = $saveURL;
	}
	
	public function setDiscardURL($discardURL) {
		$this->discardURL = $discardURL;
	}

	public function setViewURL($viewURL) {
		$this->viewURL = $viewURL;
	}
	

}