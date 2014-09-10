<?
namespace Concrete\Controller\Backend;
use Controller;
use PageType, Permissions, Loader, Redirect;
use Page as ConcretePage;

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
}

