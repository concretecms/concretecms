<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardComposerController extends Controller {

	public function view() {
		$this->redirect('/dashboard/composer/write');
	}
	
}