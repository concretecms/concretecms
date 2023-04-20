<?php
namespace Concrete\Controller\Panel\Page;

use Concrete\Core\Form\Service\Widget\UserSelector;
use Controller;
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
        $this->set('userSelector', $this->app->make(UserSelector::class));
        $this->setViewObject(new View('/panels/page/preview_as/form'));
    }

    public function frame_page()
    {
        $this->setViewObject(new View('/panels/page/preview_as/frame'));
    }

    public function preview_page()
    {
        $page = Page::getByID(intval($_REQUEST['cID'], 10), 'ACTIVE');
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
                $user_info = UserInfo::getByID($request->request('customUser'));
                if ($user_info && is_object($user_info) && !$user_info->isError()) {
                    $request->setCustomRequestUser($user_info);
                }
            }
            $customRequestDateTime = null;
            if ($request->query->has('customDate')) {
                $customRequestDateTime = $request->query->get('customDate');
                if ($request->query->has('customTime')) {
                    $customRequestDateTime .= ' ' . $request->query->get('customTime');
                }
                $dateTime = new \DateTime($customRequestDateTime);
                $request->setCustomRequestDateTime($dateTime->format("Y-m-d H:i:s"));
            }

            $controller = $page->getPageController();
            $controller->disableEditing();
            $view = $controller->getViewObject();

            $response = new \Response();
            $response->setContent($view->render());
            $response->send();
        }
    }
}
