<?php

namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\EditResponse;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Permission\Checker;
use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\Validation\SanitizeService;

class Page extends Controller
{
    public function create($ptID, $parentID = false)
    {
        $pagetype = PageType::getByID($this->app->make(SanitizeService::class)->sanitizeInt($ptID));
        if (!$pagetype) {
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

                return $this->buildRedirect('/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID() . '&ctask=check-out-first&' . $this->app->make('token')->getParameter());
            }
        }
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
