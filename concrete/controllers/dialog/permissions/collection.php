<?php

namespace Concrete\Controller\Dialog\Permissions;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\Utility\Service\Validation\Numbers;

defined('C5_EXECUTE') or die('Access Denied.');

class Collection extends UserInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = '/dialogs/permissions/collection';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Controller\Backend\UserInterface::canAccess()
     */
    public function canAccess()
    {
        $pc = new Checker($this->getPage());

        return $pc->canEditPagePermissions();
    }

    public function view()
    {
        $this->set('page', $this->getPage());
        $this->set('close', !empty($this->request->query->get('close')));
        $this->set('user', $this->app->make(ConcreteUser::class));
    }

    protected function getPage(): Page
    {
        $pageID = $this->request->request->get('cID', $this->request->query->get('cID'));
        $pageID = $this->app->make(Numbers::class)->integer($pageID, 1) ? (int) $pageID : null;
        $page = $pageID === null ? null : Page::getByID($pageID);
        if ($page === null || $page->isError()) {
            throw new UserMessageException(t('Page not found'));
        }

        return $page;
    }
}
