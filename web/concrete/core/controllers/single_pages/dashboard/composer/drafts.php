<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('collection_types');

class Concrete5_Controller_Dashboard_Composer_Drafts extends Controller {

	public function on_start() {
		$this->set('disableThirdLevelNav', true);
		$this->set('drafts', ComposerPage::getMyDrafts());
	}
	
	public function draft_discarded() {
		$this->set('message', t('Draft deleted.'));
	}
	
}