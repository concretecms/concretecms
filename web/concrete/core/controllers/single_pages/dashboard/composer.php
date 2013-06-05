<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Composer extends Controller {

	public function view() {
		$this->redirect('/dashboard/composer/drafts');
	}
	
}