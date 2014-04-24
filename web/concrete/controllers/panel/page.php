<?
namespace Concrete\Controller\Panel;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
class Page extends BackendInterfacePageController {

	protected $viewPath = '/panels/page';
	public function canAccess() {
		return $this->permissions->canEditPageContents();
	}

	public function view() {}

}

