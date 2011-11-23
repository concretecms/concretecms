<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardController extends Controller {

	public function view() {
		$c = Page::getByPath('/dashboard/home');
		$v = View::getInstance();
		$v->disableEditing();
		$v->render($c);
	}
	
}