<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Cookie\ResponseCookieJar;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\Validation\SanitizeService;
use Concrete\Core\Workflow\Request\ApprovePageRequest;
use Concrete\Core\Workflow\Request\UnapprovePageRequest;

class Page extends Controller
{
    public function create($ptID, $parentID = false)
    {
        $pagetype = PageType::getByID($this->app->make(SanitizeService::class)->sanitizeInt($ptID));
        if ($pagetype) {
            $proceed = false;
            $parent = $parentID ? ConcretePage::getByID($parentID) : null;
            if ($parent && !$parent->isError()) {
                $pp = new Checker($parent);
                $proceed = $pp->canAddSubCollection($pagetype);
            } else {
                $ptp = new Checker($pagetype);
                $proceed = $ptp->canAddPageType();
                $parent = null;
            }
            if ($proceed) {
                $pt = $pagetype->getPageTypeDefaultPageTemplateObject();
                $d = $pagetype->createDraft($pt);
                if ($parent !== null) {
                    $d->setPageDraftTargetParentPageID($parent->getCollectionID());
                }

                return $this->buildRedirect('/ccm/system/page/checkout/' . $d->getCollectionID() . '/first/' . $this->app->make('token')->generate());
            }
        }
    }

    /**
     * @param int $cID
     * @param string $flag use 'first' or 'add-block'
     * @param string $token
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkout($cID, $flag, $token)
    {
        $valt = $this->app->make('token');
        if (!$valt->validate('', $token)) {
            throw new UserMessageException($valt->getErrorMessage());
        }
        $c = ConcretePage::getByID($cID);
        if (!$c || $c->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $cp = new Checker($c);
        if (!$cp->canEditPageContents() && !$cp->canEditPageProperties() && !$cp->canApprovePageVersions()) {
            throw new UserMessageException(t('Access Denied'));
        }
        $u = $this->app->make(ConcreteUser::class);
        $u->loadCollectionEdit($c);

        $redirectUrl = $this->app->make(ResolverManagerInterface::class)->resolve([$c]);
        switch ($flag) {
            case 'first':
                $query = $redirectUrl->getQuery();
                $query->modify(['ccmCheckoutFirst' => '1']);
                $redirectUrl = $redirectUrl->setQuery($query);
                break;
            case 'add-block':
                $this->app->make(ResponseCookieJar::class)->addCookie('ccmLoadAddBlockWindow', '1', 0, $this->app->make('app_relative_path') . '/');
                break;
        }

        return $this->buildRedirect($redirectUrl);
    }

    public function exitEditMode($cID, $token)
    {
        $valt = $this->app->make('token');
        if (!$valt->validate('', $token)) {
            throw new UserMessageException(t('Access Denied'));
        }
        $c = ConcretePage::getByID($cID);
        $cp = new Checker($c);
        if ($cp->canViewToolbar()) {
            $u = $this->app->make(ConcreteUser::class);
            $u->unloadCollectionEdit();
        }

        return $this->buildRedirect([$c]);
    }

    public function approveRecent($cID, $token)
    {
        $valt = $this->app->make('token');
        if (!$valt->validate('', $token)) {
            throw new UserMessageException($valt->getErrorMessage());
        }
        $c = ConcretePage::getByID($cID, 'RECENT');
        if (!$c || $c->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $cp = new Checker($c);
        if (!$cp->canApprovePageVersions()) {
            throw new UserMessageException(t('Access Denied'));
        }

        $pkr = new ApprovePageRequest();
        $pkr->setRequestedPage($c);
        $v = $c->getVersionObject();
        $pkr->setRequestedVersionID($v->getVersionID());
        $u = $this->app->make(ConcreteUser::class);
        $pkr->setRequesterUserID($u->getUserID());
        $u->unloadCollectionEdit($c);
        $pkr->trigger();

        return $this->buildRedirect([$c]);
    }

    public function publishNow($cID, $token)
    {
        $valt = $this->app->make('token');
        if (!$valt->validate('', $token)) {
            throw new UserMessageException($valt->getErrorMessage());
        }
        $c = ConcretePage::getByID($cID, 'SCHEDULED');
        if (!$c || $c->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $cp = new Checker($c);
        if (!$cp->canApprovePageVersions()) {
            throw new UserMessageException(t('Access Denied'));
        }
        $v = $c->getVersionObject();
        $v->approve(false, null);

        return $this->buildRedirect([$c]);
    }

    public function cancelSchedule($cID, $token)
    {
        $valt = $this->app->make('token');
        if (!$valt->validate('', $token)) {
            throw new UserMessageException($valt->getErrorMessage());
        }
        $c = ConcretePage::getByID($cID, 'SCHEDULED');
        if (!$c || $c->isError()) {
            throw new UserMessageException(t('Unable to find the specified page'));
        }
        $cp = new Checker($c);
        if (!$cp->canApprovePageVersions()) {
            throw new UserMessageException(t('Access Denied'));
        }
        $u = $this->app->make(ConcreteUser::class);
        $pkr = new UnapprovePageRequest();
        $pkr->setRequestedPage($c);
        $v = $c->getVersionObject();
        $v->setPublishInterval(null, null);
        $pkr->setRequestedVersionID($v->getVersionID());
        $pkr->setRequesterUserID($u->getUserID());
        $pkr->trigger();

        return $this->buildRedirect([$c]);
    }

    public function getJSON()
    {
        $h = $this->app->make('helper/concrete/dashboard/sitemap');
        if (!$h->canRead()) {
            throw new UserMessageException(t('You do not have access to the sitemap.'));
        }
        $c = ConcretePage::getByID((int) $this->request->request->get('cID'));
        $cp = new Checker($c);
        if (!$cp->canViewPage()) {
            throw new UserMessageException(t('You are not allowed to access this page.'));
        }
        $r = new EditResponse();
        $r->setPage($c);

        return $this->app->make(ResponseFactoryInterface::class)->json($r);
    }
}
