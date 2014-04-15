<?php
namespace Concrete\Core\Application;
use Loader;
use stdClass;
abstract class EditResponse {

	public $time;
	public $message;
	public $redirectURL;
	protected $additionalData = array();
	public $error;

	public function setRedirectURL($url) {
		$this->redirectURL = $url;
	}

	public function getRedirectURL() {
		return $this->redirectURL;
	}

	public function __construct($e = false) {
		if ($e instanceof \Concrete\Core\Error\Error && $e->has()) {
			$this->error = $e;
		} else {
			$this->error = Loader::helper('validation/error');
		}
		$this->time = date('F d, Y g:i A');
	}

	public function setError($error) {
		$this->error = $error;
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
		$r = $this->getJSONObject();
		if ($this->error && $this->error->has()) {
			Loader::helper('ajax')->sendError($this->error);
		} else {
			Loader::helper('ajax')->sendResult($this->getJSONObject());
		}
	}

}