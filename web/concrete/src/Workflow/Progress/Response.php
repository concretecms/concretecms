<?php
namespace Concrete\Core\Workflow\Progress;
use \Concrete\Core\Foundation\Object;
class Response extends Object {

	protected $wprURL = '';

	public function setWorkflowProgressResponseURL($wprURL) {
		$this->wprURL = $wprURL;
	}

	public function getWorkflowProgressResponseURL() {
		return $this->wprURL;
	}

}
