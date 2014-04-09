<?
namespace Concrete\Controller\Panel;
use \Concrete\Controller\Backend\UI\Page as BackendInterfacePageController;
class Page extends BackendInterfacePageController {

	protected $viewPath = '/system/panels/page';
	public function canAccess() {
		return $this->permissions->canEditPageContents();
	}

	public function view() {}

}

