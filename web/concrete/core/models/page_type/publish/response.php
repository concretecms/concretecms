<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_PageTypePublishResponse extends Object {

	public $error = false;
	public $messages = array();
	public $outputControls = array();
	public $cID;
	public $saveURL;
	public $redirectURL;
	public $saveStatus;
	public $discardURL;
	public $viewURL;
	public $time;
	
	public function __construct($e = false) {
		if ($e instanceof ValidationErrorHelper && $e->has()) {
			$this->error = true;
			$this->messages = $e->getList();
		}
		$this->time = date('F d, Y g:i A');
	}

	public function setSaveStatus($saveStatus) {
		$this->saveStatus = $saveStatus;
	}

	public function setRedirectURL($url) {
		$this->redirectURL = $url;
	}

	public function getRedirectURL() {
		return $this->redirectURL;
	}

	public function setOutputControls($outputControls) {
		$this->outputControls = $outputControls;
	}

	public function setPage(Page $page) {
		$this->cID = $page->getCollectionID();
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