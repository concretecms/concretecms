<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_Page_Attributes extends BackendInterfacePageController {

	protected $viewPath = '/system/dialogs/page/attributes';

	protected function canAccess() {
		return $this->permissions->canEditPageProperties();
	}

	public function view() {
		$list = new PageAttributesPanelController();
		$list->setPageObject($this->page);
		$list->view();
		$this->set('menu', $list->getViewObject());

		$detail = new PageAttributesPanelDetailController();
		$detail->setPageObject($this->page);
		$detail->view();
		$this->set('detail', $detail->getViewObject());
	}

}

