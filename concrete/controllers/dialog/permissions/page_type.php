<?php

namespace Concrete\Controller\Dialog\Permissions;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Utility\Service\Validation\Numbers;

defined('C5_EXECUTE') or die('Access Denied.');

class PageType extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/page_type';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $ch = Page::getByPath('/dashboard/pages/types');
        $chp = new Checker($ch);

        return $chp->canViewPage();
    }

    public function view()
    {
        $this->set('pageType', $this->getPageType());
    }

    protected function getPageType(): Type
    {
        $pageTypeID = $this->request->request->get('ptID', $this->request->query->get('ptID'));
        $pageType = $this->app->make(Numbers::class)->integer($pageTypeID, 1) ? Type::getByID((int) $pageTypeID) : null;
        if (!$pageType || $pageType->isError()) {
            throw new UserMessageException(t('Page Type not found'));
        }

        return $pageType;
    }
}
