<?php

namespace Concrete\Controller\Workflow\Dialogs;

use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\Utility\Service\Validation\Numbers;
use Concrete\Core\Workflow\Progress\PageProgress;
use Concrete\Core\Workflow\Progress\Progress;
use Symfony\Component\HttpFoundation\Response;

defined('C5_EXECUTE') or die('Access Denied.');

class ApprovePagePreview extends Controller
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\Controller::$viewPath
     */
    protected $viewPath = 'backend/workflow/dialogs/approve_page_preview';

    public function view(): ?Response
    {
        $wp = $this->getWorkflowProgress();
        $requestedVersionPage = $this->getPage($wp);
        $requestedVersion = $requestedVersionPage->getVersionObject();
        $liveVersionID = (int) Page::getByID($requestedVersionPage->getCollectionID(), 'ACTIVE')->getVersionObject()->getVersionID();
        $recentVersionID = (int) Page::getByID($requestedVersionPage->getCollectionID(), 'RECENT')->getVersionObject()->getVersionID();
        $this->set('requestedVersion', $requestedVersion);
        $this->set('liveVersionID', $liveVersionID);
        $this->set('recentVersionID', $recentVersionID);
        $this->set('ui', $this->app->make(UserInterface::class));
        $this->set('resolverManager', $this->app->make(ResolverManagerInterface::class));

        return null;
    }

    protected function getWorkflowProgressID(): ?int
    {
        $wpID = $this->request->request->get('wpID', $this->request->query->get('wpID'));

        return $this->app->make(Numbers::class)->integer($wpID, 1) ? (int) $wpID : null;
    }

    protected function getWorkflowProgress(): PageProgress
    {
        $wpID = $this->getWorkflowProgressID();
        $wp = $wpID === null ? null : Progress::getByID($wpID);
        if (!$wp instanceof PageProgress) {
            throw new UserMessageException(t('Failed to find the requested workflow progress.'));
        }

        return $wp;
    }

    protected function getPage(PageProgress $wp): Page
    {
        $req = $wp->getWorkflowRequestObject();
        $requestedVersionPage = Page::getByID($req->getRequestedPageID(), $req->getRequestedVersionID());
        $rvcp = new Checker($requestedVersionPage);
        if (!$rvcp->canViewPageVersions()) {
            throw new UserMessageException(t('Access Denied.'));
        }

        return $requestedVersionPage;
    }
}
