<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Details_Page_Location extends FrontendEditPageController {

	protected $viewPath = '/system/panels/details/page/location';

	protected function canAccess() {
		return (!$this->page->isPageDraft() && is_object($this->asl) && $this->asl->allowEditPaths());
	}

	public function __construct() {
		parent::__construct();
		$pk = PermissionKey::getByHandle('edit_page_properties');
		$pk->setPermissionObject($c);
		$this->asl = $pk->getMyAssignment();
	}

	public function submit() {
		if ($this->validateAction()) {

		}
	}

}