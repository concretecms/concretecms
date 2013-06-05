<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Extend extends Controller {

	public function view() {
		$this->redirect('/dashboard/extend/install');
	}

	
}