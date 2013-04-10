<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_ComposerPublishResponse extends Object {

	public $error = false;
	public $messages = array();

	public function __construct($e = false) {
		if ($e instanceof ValidationErrorHelper && $e->has()) {
			$this->error = true;
			$this->messages = $e->getList();
		}
		$this->time = date('F d, Y g:i A');
	}
	
}