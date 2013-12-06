<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Page extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/page';
	public function canAccess() {
		return $this->permissions->canEditPageContents();
	}

	public function view() {}

}

