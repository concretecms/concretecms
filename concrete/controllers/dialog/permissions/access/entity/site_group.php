<?php

namespace Concrete\Controller\Dialog\Permissions\Access\Entity;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Access\Entity\Type;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Site\Type\Service as SiteTypeService;
use Concrete\Core\Site\User\Group\Service as GroupService;
use Concrete\Core\Validation\CSRF\Token;

defined('C5_EXECUTE') or die('Access Denied.');

class SiteGroup extends UserInterface
{
    /**
     * @var \Concrete\Core\Site\User\Group\Service
     */
    protected $groupService;

    /**
     * @var \Concrete\Core\Site\Type\Service
     */
    protected $siteTypeService;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/access/entity/site_group';

    public function __construct(GroupService $service, SiteTypeService $siteTypeService)
    {
        $this->groupService = $service;
        $this->siteTypeService = $siteTypeService;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $page = Page::getByPath('/dashboard/system/sites/site_types');
        $p = new Checker($page);

        return $p->canViewPage();
    }

    public function view()
    {
        $groups = [];
        foreach ($this->siteTypeService->getList() as $type) {
            $siteGroups = $this->groupService->getSiteTypeGroups($type);
            $groups = array_merge($groups, $siteGroups);
        }

        $accessEntityType = Type::getByHandle('site_group');
        $url = $accessEntityType->getControllerUrl();
        $this->set('groups', $groups);
        $this->set('url', $url);
        $this->set('token', $this->app->make(Token::class));
    }
}
