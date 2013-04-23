<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($_REQUEST['cmpDraftID'] > 0) {
	$draft = ComposerDraft::getByID($_REQUEST['cmpDraftID']);
	if (is_object($draft)) {
		$composer = $draft->getComposerObject();
		$p = new Permissions($composer);
		if ($p->canEditComposerPermissions()) {
			Loader::element('permission/details/composer_draft', array("draft" => $draft));
		}
	}
}
