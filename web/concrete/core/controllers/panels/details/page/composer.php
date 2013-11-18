<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Details_Page_Composer extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/details/page/composer';

	protected function canAccess() {
		return $this->permissions->canEditPageContents();
	}

	public function view() {
		$this->requireAsset('core/composer');
		$pagetype = PageType::getByID($this->page->getPageTypeID());
		$id = $this->page->getCollectionID();
		$saveURL = View::url('/dashboard/composer/write', 'save', 'draft', $id);
		$viewURL = View::url('/dashboard/composer/write', 'draft', $id);
		$this->set('saveURL', $saveURL);
		$this->set('viewURL', $viewURL);
		$this->set('pagetype', $pagetype);
	}

}