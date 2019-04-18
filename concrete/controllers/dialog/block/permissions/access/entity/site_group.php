<?php
namespace Concrete\Controller\Dialog\Permission\Access\Entity;

use Concrete\Controller\Backend\UserInterface as Controller;
use Concrete\Core\Permission\Access\Entity\Type;
use PortlandLabs\Liberta\User\Group\Service;

class SiteGroup extends Controller
{

    protected $groupService;
    protected $siteTypeService;

    public function __construct(Service $service, \Concrete\Core\Site\Type\Service $siteTypeService)
    {
        $this->groupService = $service;
        $this->siteTypeService = $siteTypeService;
        parent::__construct();
    }

    protected $viewPath = '/dialogs/permission/access/entity/site_group';

    public function canAccess()
    {
        $page = \Page::getByPath("/dashboard/system/sites/site_types");
        $p = new \Permissions($page);
        return $p->canViewPage();
    }

    public function view($pkCategoryHandle, $id)
    {
        $type = false;
        switch($pkCategoryHandle) {
            case 'page_type':
                $pageType = \Concrete\Core\Page\Type\Type::getByID($id);
                $type = $pageType->getSiteTypeObject();
                break;
            case 'page':
                $page = \Page::getByID($id);
                if (is_object($page) && !$page->isError()) {
                    $tree = $page->getSiteTreeObject();
                    if (is_object($tree)) {
                        $type = $tree->getSiteType();
                    }
                }
                break;
        }

        $groups = array();
        if (is_object($type)) {
            $groups = $this->groupService->getSiteTypeGroups($type);
        } else {
            // make a list of all of them
            $groups = array();
            foreach($this->siteTypeService->getList() as $type) {
                $siteGroups = $this->groupService->getSiteTypeGroups($type);
                $groups = array_merge($groups, $siteGroups);
            }
        }
        $accessEntityType = Type::getByHandle('site_group');
        $url = $accessEntityType->getAccessEntityTypeToolsURL();
        $this->set('groups', $groups);
        $this->set('url', $url);
    }


}
