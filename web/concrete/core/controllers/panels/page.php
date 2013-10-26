<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Page extends PanelController {

	protected $viewPath = '/system/panels/page';
	public function canViewPanel() {
		return $this->permissions->canEditPageContents();
	}

	public function view() {}
}

