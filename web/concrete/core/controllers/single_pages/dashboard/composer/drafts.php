<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_Composer_Drafts extends DashboardController {

	public function on_start() {
		$this->set('drafts', Page::getDrafts());
	}
	
	public function draft_discarded() {
		$this->set('message', t('Draft deleted.'));
	}
	
}