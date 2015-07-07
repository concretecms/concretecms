<?php
namespace Concrete\Controller\Panel\Page;

use Concrete\Core\Form\Service\Widget\DateTime;
use Controller;
use Loader;
use Page;
use Permissions;
use Request;
use UserInfo;
use View;
use Core;
use Config;

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
        if ($permissions->canPreviewPageAsUser() && $permissions->canRead() && Config::get('concrete.permissions.model') == 'advanced') {

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
            $request->setCustomRequestDateTime(Core::make('helper/form/date_time')->translate('preview_as_user_datetime', $request->request()));

            $controller = $page->getPageController();
            $view = $controller->getViewObject();

            $response = new \Response();
            $response->setContent($view->render());
            $response->send();
        }

    }

}
