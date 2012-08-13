<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Composer extends Controller {

	public function view() {
		Loader::model("composer_page");
		$drafts = ComposerPage::getMyDrafts();
		if (count($drafts) > 0) {
			$this->redirect('/dashboard/composer/drafts');
		} else {
			$this->redirect('/dashboard/composer/write');
		}
	}
	
}