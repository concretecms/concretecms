<?php
namespace Concrete\Controller\Panel\Page;

use Controller;
use Loader;
use Page;
use Permissions;
use Request;
use UserInfo;
use View;

class PreviewAsUser extends Controller
{

    public function view()
    {
        $this->setViewObject(new View('/panels/page/preview_as/form'));
    }

    public function frame_page()
    {
        $this->setViewObject(new View('/panels/page/preview_as/frame'));
    }

    public function preview_page()
    {
        $page = Page::getByID(intval($_REQUEST['cID'], 10), 'RECENT');
        if (!is_object($page) || $page->isError()) {
            throw new \InvalidArgumentException('Invalid collection ID');
        }

        $permissions = new Permissions($page);
        if ($permissions->canPreviewPageAsUser() && $permissions->canRead() && PERMISSIONS_MODEL == 'advanced') {

            /** @var Request $request */
            $request = Request::getInstance();
            $request->setCustomRequestUser(false);
            $request->setCurrentPage($page);

            if ($request->request('customUser')) {
                $user_info = UserInfo::getByUserName($request->request('customUser'));
                if ($user_info && is_object($user_info) && !$user_info->isError()) {
                    $request->setCustomRequestUser($user_info);
                }
            }

            $datetime = Loader::helper('form/date_time');
            $date = $datetime->translate('onDate', $_REQUEST);
            $request->setCustomRequestDateTime($date);

            $controller = $page->getPageController();
            $view = $controller->getViewObject();

            $response = new \Response();
            $response->setContent($view->render());
            $response->send();
        }

    }

}
