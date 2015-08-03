<?
namespace Concrete\Controller\Panel\Detail\Page;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use PageEditResponse;
use PageCache;
use Request;
use Response;
use Config;

class Caching extends BackendInterfacePageController {

	protected $viewPath = '/panels/details/page/caching';

	protected function canAccess() {
		return $this->permissions->canEditPageSpeedSettings();
	}

	public function view() {

	}

	public function preview() {
        $req = Request::getInstance();
        $req->setCurrentPage($this->page);
        $controller = $this->page->getPageController();
        $view = $controller->getViewObject();
        $req->setCustomRequestUser(-1);

        Config::set('concrete.cache.preview', true);
        
        $response = new Response();
        $content = $view->render();
        $response->setContent($content);
        return $response;
	}
}