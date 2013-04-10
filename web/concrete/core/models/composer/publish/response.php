<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerPublishResponse extends Object {

	public $error = false;
	public $messages = array();
	public $outputControls = array();
	public $cmpDraftID;
	public $saveURL;
	public $redirectURL;
	public $publishURL;
	public $discardURL;

	public function __construct($e = false) {
		if ($e instanceof ValidationErrorHelper && $e->has()) {
			$this->error = true;
			$this->messages = $e->getList();
		}
		$this->time = date('F d, Y g:i A');
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

	public function setComposerDraft(ComposerDraft $cmpDraft) {
		$this->cmpDraftID = $cmpDraft->getComposerDraftID();
	}

	public function setSaveURL($saveURL) {
		$this->saveURL = $saveURL;
	}
	
	public function setDiscardURL($discardURL) {
		$this->discardURL = $discardURL;
	}
	
	public function setPublishURL($publishURL) {
		$this->publishURL = $publishURL;
	}



}