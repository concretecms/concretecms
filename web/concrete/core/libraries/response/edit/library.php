<?php defined('C5_EXECUTE') or die("Access Denied.");
abstract class Concrete5_Library_EditResponse {

	public $error = false;
	public $errors = array();
	public $time;
	public $message;
	public $redirectURL;
	protected $additionalData = array();

	public function setRedirectURL($url) {
		$this->redirectURL = $url;
	}

	public function getRedirectURL() {
		return $this->redirectURL;
	}

	public function __construct($e = false) {
		if ($e instanceof ValidationErrorHelper && $e->has()) {
			$this->error = $e;
			$this->errors = $e->getList();
		}
		$this->time = date('F d, Y g:i A');
	}

	public function setMessage($message) {
		$this->message = $message; 
	}

	public function getMessage() {
		return $this->message;
	}

	public function getJSON() {
		return Loader::helper('json')->encode($this->getJSONObject());
	}

	public function setAdditionalDataAttribute($key, $value) {
		$this->additionalData[$key] = $value;
	}

	abstract public function getJSONObject();

	public function getBaseJSONObject() {
		$o = new stdClass;
		$o->message = $this->message;
		$o->time = $this->time;
		$o->redirectURL = $this->redirectURL;
		foreach($this->additionalData as $key => $value) {
			$o->{$key} = $value;
		}
		return $o;
	}

	public function outputJSON() {
		if ($this->error && $this->error->has()) {
			Loader::helper('ajax')->sendError($this->error);
		} else {
			Loader::helper('ajax')->sendResult($this->getJSONObject());
		}
	}

}