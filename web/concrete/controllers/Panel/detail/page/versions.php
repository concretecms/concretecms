<?
namespace Concrete\Controller\Panel\Detail\Page;
use \Concrete\Controller\Backend\UI\Page as BackendInterfacePageController;
use Loader;

class Versions extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/details/page/versions';

	public function canAccess() {
		return $this->permissions->canViewPageVersions();
	}

	public function view() {
		$this->set('ih', Loader::helper('concrete/ui'));
	}


}

