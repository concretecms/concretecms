<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_PageNotFound extends RequestController {
	
	public $helpers = array('form');
	
	protected $requestViewPath = '/page_not_found';

	public function view() {
		header("HTTP/1.0 404 Not Found");
	}
	
}