<?php
namespace Concrete\Controller\Backend;

use Controller;
use PageType;
use Permissions;
use Loader;
use Redirect;
use Page as ConcretePage;
use User as ConcreteUser;
use Concrete\Core\Page\EditResponse as PageEditResponse;
use Core;

class Page extends Controller
{
    public function create($ptID, $parentID = false)
    {
        $pagetype = PageType::getByID(Loader::helper('security')->sanitizeInt($ptID));
        if (is_object($pagetype)) {
            $proceed = false;
            $parent = $parentID ? ConcretePage::getByID($parentID) : null;
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
                if ($parent !== null) {
                    $d->setPageDraftTargetParentPageID($parent->getCollectionID());
                }

                return Redirect::url(\Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID() . '&ctask=check-out-first&' . Loader::helper('validation/token')->getParameter());
            }
        }
    }

    public function exitEditMode($cID, $token)
    {
        if (Loader::helper('validation/token')->validate('', $token)) {
            $c = ConcretePage::getByID($cID);
            $cp = new Permissions($c);
            if ($cp->canViewToolbar()) {
                $u = new ConcreteUser();
                $u->unloadCollectionEdit();
            }

            return Redirect::page($c);
        }

        return new \Response(t('Access Denied'));
    }

    public function getJSON()
    {
        $h = \Core::make('helper/concrete/dashboard/sitemap');
        if ($h->canRead()) {
            $c = ConcretePage::getByID(intval($_POST['cID']));
            $cp = new Permissions($c);
            if ($cp->canViewPage()) {
                $r = new PageEditResponse();
                $r->setPage($c);
                $r->outputJSON();
            } else {
                Core::make('helper/ajax')->sendError(t('You are not allowed to access this page.'));
            }
        } else {
            Core::make('helper/ajax')->sendError(t('You do not have access to the sitemap.'));
        }
    }
}
