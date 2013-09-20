<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($_REQUEST['pDraftID'] > 0) {
	$draft = PageDraft::getByID($_REQUEST['pDraftID']);
	if (is_object($draft)) {
		$pagetype = $draft->getPageTypeObject();
		$p = new Permissions($pagetype);
		if ($p->canEditPageTypePermissions()) {
			Loader::element('permission/details/page_draft', array("draft" => $draft));
		}
	}
}
