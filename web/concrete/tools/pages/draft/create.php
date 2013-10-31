<?
defined('C5_EXECUTE') or die("Access Denied.");
$pagetype = PageType::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['ptID']));
if (is_object($pagetype)) {
	$ptp = new Permissions($pagetype);
	if ($ptp->canComposePageType()) {
		$pt = $pagetype->getPageTypeDefaultPageTemplateObject();
		$d = $pagetype->createDraft($pt);
		header('Location:' . BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $d->getCollectionID() . '&ctask=check-out-first&' . Loader::helper('validation/token')->getParameter());
		exit;
	}
}
