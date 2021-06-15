<?php

namespace Concrete\Controller\Backend\Page;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class PreviewVersion extends AbstractController
{
    public function view(): Response
    {
        $page = $this->getPage();
        $this->prepareRequest($page);

        return $this->preparePage($page) ?: $this->renderPage($page);
    }

    protected function getPageID(): ?int
    {
        $pageID = $this->request->request->get('cID', $this->request->query->get('cID'));

        return $this->app->make(Numbers::class)->integer($pageID, 1) ? (int) $pageID : null;
    }

    protected function getVersionID(): ?int
    {
        $versionID = $this->request->request->get('cvID', $this->request->query->get('cvID'));

        return $this->app->make(Numbers::class)->integer($versionID, 1) ? (int) $versionID : null;
    }

    /**
     * @throws \Concrete\Core\Error\UserMessageException
     */
    protected function getPage(): Page
    {
        $pageID = $this->getPageID();
        $versionID = $this->getVersionID();
        if ($pageID === null || $versionID === null) {
            throw new UserMessageException(t('Invalid parameters.'));
        }
        $page = Page::getByID($pageID, $versionID);
        if (!$page || $page->isError()) {
            throw new UserMessageException(t('Unable to find the page specified'));
        }
        $cp = new Checker($page);
        if (!$cp->canViewPageVersions()) {
            throw new UserMessageException(t('Access Denied.'));
        }
        $pageVersion = $page->getVersionObject();
        if (!$pageVersion || $pageVersion->isError()) {
            throw new UserMessageException(t('Unable to find the page version specified'));
        }

        return $page;
    }

    protected function prepareRequest(Page $page): void
    {
        $this->request->setCustomRequestUser(-1);
        $this->request->setCurrentPage($page);
        $this->app->singleton(User::class, function() {
            return new User(); // Not super thrilled about this. We need to clean up all setCustomRequest user code including this.
        });
    }

    protected function preparePage(Page $page): ?Response
    {
        $controller = $page->getPageController();
        $response = $controller->on_start();
        if ($response instanceof Response) {
            return $response;
        }
        $response = $controller->runAction('view');
        if ($response instanceof Response) {
            return $response;
        }
        $controller->on_before_render();

        return null;
    }

    protected function renderPage(Page $page): Response
    {
        $controller = $page->getPageController();
        $view = $controller->getViewObject();
        $content = $view->render();

        return $this->app->make(ResponseFactoryInterface::class)->create($content);
    }
}
