<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_News extends Controller {

	public $helpers = array('form'); 
	
	public function view() {
		$c = Page::getByPath('/dashboard/home');
		$v = View::getInstance();
		$v->disableEditing();
		$v->render($c);
	}

}