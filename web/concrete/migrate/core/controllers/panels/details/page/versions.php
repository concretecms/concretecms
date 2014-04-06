<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Panel_Details_Page_Versions extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/details/page/versions';

	public function canAccess() {
		return $this->permissions->canViewPageVersions();
	}

	public function view() {
		$this->set('ih', Loader::helper('concrete/ui'));
	}


}

