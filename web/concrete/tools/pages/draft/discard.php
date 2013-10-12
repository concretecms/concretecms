<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('composer', $_REQUEST['token'])) {
	$c = Page::getByID($_REQUEST['cID']);
	$cp = new Permissions($c);
	if ($cp->canEditPage() && $c->isPageDraft()) {
		$c->delete();
		$u = new User();
		$cID = $u->getPreviousFrontendPageID();
		$ptr = new PageTypePublishResponse();
		$ptr->setRedirectURL(DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID);
	}
}

if (!($ptr instanceof PageTypePublishResponse)) {
	$e = Loader::helper('validation/error');
	$e->add(t('Access Denied.'));
	$ptr = new PageTypePublishResponse($e);
}

Loader::helper('ajax')->sendResult($ptr);