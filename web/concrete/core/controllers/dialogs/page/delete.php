<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_Page_Delete extends FrontendEditPageController {

	protected $viewPath = '/system/dialogs/page/delete';

	protected function canAccess() {
		return $this->permissions->canDeletePage();
	}

	public function view() {
		$this->set('numChildren', $this->page->getNumChildren());
	}



}

