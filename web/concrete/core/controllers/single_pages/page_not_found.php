<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_PageNotFound extends Controller {
	
	public $helpers = array('form');
	
	public function view() {
		header("HTTP/1.0 404 Not Found");
	}
	
}