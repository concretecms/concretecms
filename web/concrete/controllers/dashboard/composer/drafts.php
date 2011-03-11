<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('composer_page');
Loader::model('collection_types');

class DashboardComposerDraftsController extends Controller {

	public function on_start() {
		$this->set('disableThirdLevelNav', true);
		$this->set('drafts', ComposerPage::getMyDrafts());
	}
	
	public function draft_discarded() {
		$this->set('message', t('Draft deleted.'));
	}
	
}