<?php
namespace Concrete\Controller\Backend;

use Controller;
use PageType;
use Permissions;
use Redirect;
use Page as ConcretePage;
use User;
use Concrete\Core\Page\EditResponse as PageEditResponse;

class Page extends Controller
{
    public function create($ptID, $parentID = false)
    {
        $pagetype = PageType::getByID($this->app->make('helper/security')->sanitizeInt($ptID));
        $parent = null;
        if ($parentID) {
            $parent = ConcretePage::getByID($parentID);
        }
        if (is_object($pagetype)) {
            if (is_object($parent) && !$parent->isError()) {
                $pp = new Permissions($parent);
                $proceed = $pp->canAddSubCollection($pagetype);
            } else {
                $ptp = new Permissions($pagetype);
                $proceed = $ptp->canAddPageType();
                $parent = null;
            }
            if ($proceed) {
                $pt = $pagetype->getPageTypeDefaultPageTemplateObject();
                $d = $pagetype->createDraft($pt);
                if (is_object($parent)) {
                    $d->setPageDraftTargetParentPageID($parent->getCollectionID());
                }
                return Redirect::url(rtrim($this->app->make('url/canonical'), '/') . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID() . '&ctask=check-out-first&' . $this->app->make('helper/validation/token')->getParameter());
            }
        }
    }

    public function exitEditMode($cID, $token)
    {
        if ($this->app->make('helper/validation/token')->validate('', $token)) {
            $c = ConcretePage::getByID($cID);
            $cp = new Permissions($c);
            if ($cp->canViewToolbar()) {
                $u = new User();
                $u->unloadCollectionEdit();
            }
            return Redirect::page($c);
        }

        return new \Response(t('Access Denied'));
    }

    public function getJSON()
    {
        $h = $this->app->make('helper/concrete/dashboard/sitemap');
        if ($h->canRead()) {
            $c = ConcretePage::getByID(intval($_POST['cID']));
            $cp = new Permissions($c);
            if ($cp->canViewPage()) {
                $r = new PageEditResponse();
                $r->setPage($c);
                $r->outputJSON();
            } else {
                $this->app->make('helper/ajax')->sendError(t('You are not allowed to access this page.'));
            }
        } else {
            $this->app->make('helper/ajax')->sendError(t('You do not have access to the sitemap.'));
        }
    }
}