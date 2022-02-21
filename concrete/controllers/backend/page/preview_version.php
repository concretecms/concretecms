<?php

namespace Concrete\Controller\Backend\Page;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Ssddanbrown\HtmlDiff\Diff;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class PreviewVersion extends AbstractController
{
    public function view(): Response
    {
        $page = $this->getPage();
        $this->prepareRequest($page);
        $this->prepareConfig();

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

    protected function getCompareVersionID(): ?int
    {
        $versionID = $this->request->request->get('compareVersionID', $this->request->query->get('compareVersionID'));

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
        $compareVersionID = $this->getCompareVersionID();
        if ($compareVersionID) {
            $page->loadVersionObject($compareVersionID);
            $compareVersion = $page->getVersionObject();
            if (!$compareVersion || $compareVersion->isError()) {
                throw new UserMessageException(t('Unable to find the page version to compare'));
            }
        }

        return $page;
    }

    protected function prepareRequest(Page $page): void
    {
        $this->request->setCustomRequestUser(-1);
        $this->request->setCurrentPage($page);
    }

    protected function prepareConfig(): void
    {
        $this->app->make('config')->set('concrete.cache.pages', false);
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

        $compareVersionID = $this->getCompareVersionID();
        if ($compareVersionID) {
            $this->requireAsset('htmldiff');
            $previewPage = Page::getByID($page->getCollectionID(), $this->getVersionID());
            $this->preparePage($previewPage);
            $previewController = $previewPage->getPageController();
            $previewView = $previewController->getViewObject();
            $previewContent = $previewView->render();

            $diff = new Diff($content, $previewContent);
            $content = $diff->build();
        }

        return $this->app->make(ResponseFactoryInterface::class)->create($content);
    }
}
