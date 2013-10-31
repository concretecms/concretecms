<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageTypePublishResponse extends PageEditResponse {

	public $outputControls = array();
	public $saveURL;
	public $discardURL;
	public $viewURL;
	
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

	public function getJSONObject() {
		$o = parent::getBaseJSONObject();
		$o->discardURL = $this->discardURL;
		$o->saveURL = $this->saveURL;
		$o->viewURL = $this->viewURL;
		$o->saveStatus = $this->message;
		return $o;
	}
	

}