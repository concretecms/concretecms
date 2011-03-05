<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardComposerController extends Controller {

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