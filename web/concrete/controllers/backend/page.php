<?php
namespace Concrete\Controller\Backend;
use Controller;
use PageType, Permissions, Loader, Redirect;
use Page as ConcretePage;
use User;
use Concrete\Core\Page\EditResponse as PageEditResponse;

class Page extends Controller {

	public function create($ptID, $parentID = false) {
		$pagetype = PageType::getByID(Loader::helper('security')->sanitizeInt($ptID));
        if ($parentID) {
            $parent = ConcretePage::getByID($parentID);
        }
		if (is_object($pagetype)) {
            $proceed = false;
            if (is_object($parent) && !$parent->isError()) {
                $pp = new Permissions($parent);
                $proceed = $pp->canAddSubCollection($pagetype);
            } else {
                $ptp = new Permissions($pagetype);
                $proceed = $ptp->canAddPageType();
                if (isset($parent)) {
                    unset($parent);
                }
            }
            if ($proceed) {
				$pt = $pagetype->getPageTypeDefaultPageTemplateObject();
				$d = $pagetype->createDraft($pt);
                if (is_object($parent)) {
                    $d->setPageDraftTargetParentPageID($parent->getCollectionID());
                }
				return Redirect::url(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID() . '&ctask=check-out-first&' . Loader::helper('validation/token')->getParameter());
			}
		}
	}

    public function exitEditMode($cID, $token)
    {
        if (Loader::helper('validation/token')->validate('', $token)) {
            $c = ConcretePage::getByID($cID);
            $cp = new Permissions($c);
            if ($cp->canViewToolbar()) {
                $u = new User();
                $u->unloadCollectionEdit();
            }
            return Redirect::page($c);
        }

        return new Response(t('Access Denied'));
    }

	public function getJSON() {
        $h = \Core::make('helper/concrete/dashboard/sitemap');
        if ($h->canRead()) {
            $c = ConcretePage::getByID(intval($_POST['cID']));
            $cp = new Permissions($c);
            if ($cp->canViewPage()) {
                $r = new PageEditResponse();
                $r->setPage($c);
                $r->outputJSON();
            }
        }
	}

}

