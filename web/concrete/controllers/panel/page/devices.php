<?php
namespace Concrete\Controller\Panel\Page;
use \Concrete\Controller\Backend\UserInterface\Page as BackendInterfacePageController;
use Concrete\Core\View\View;

class Devices extends BackendInterfacePageController
{
    protected $viewPath = '/panels/page/devices';

    public function canAccess()
    {
        return $this->permissions->canViewPageVersions() || $this->permissions->canEditPageVersions();
    }

    public function view()
    {

    }

    public function detail()
    {
        $view = new View('/panels/details/page/devices');

        return $view;
    }

    public function preview()
    {
        $request = \Request::getInstance();
        $c = \Page::getByID($this->request->get('cID'));
        $cp = new \Permissions($c);
        if ($cp->canViewPageVersions()) {
            $c->loadVersionObject(\Core::make('helper/security')->sanitizeInt($_REQUEST['cvID']));

            $spoofed_request = \Request::createFromGlobals();

            if ($device_handle = $request->headers->get('x-device-handle')) {
                if ($device = \Core::make('device/manager')->get($device_handle)) {
                    if ($agent = $device->getUserAgent()) {
                        $spoofed_request->headers->set('User-Agent', $agent);
                    }
                }
            }

            $spoofed_request->setCustomRequestUser(-1);
            $spoofed_request->setCurrentPage($c);

            \Request::setInstance($spoofed_request);

            $controller = $c->getPageController();
            $controller->runTask('view', array());
            $view = $controller->getViewObject();
            $response = new \Response();
            $content = $view->render();

            // Reset just in case.
            \Request::setInstance($request);

            $response->setContent($content);
            $response->send();
            exit;
        }
    }

}
