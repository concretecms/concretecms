<?
namespace Concrete\Controller\Backend;
use \Concrete\Core\Controller\Controller;
class Page extends Controller {

	public function create($ptID) {
		$pagetype = PageType::getByID(Loader::helper('security')->sanitizeInt($ptID));
		if (is_object($pagetype)) {
			$ptp = new Permissions($pagetype);
			if ($ptp->canComposePageType()) {
				$pt = $pagetype->getPageTypeDefaultPageTemplateObject();
				$d = $pagetype->createDraft($pt);
				return Redirect::url(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID() . '&ctask=check-out-first&' . Loader::helper('validation/token')->getParameter());
			}
		}
	}
}

