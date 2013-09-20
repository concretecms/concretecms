<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$draft = PageDraft::getByID($_REQUEST['pDraftID']);
$canEditPageTypePermissions = false;
if (is_object($draft)) {
	$pt = $draft->getPageTypeObject();
	if (is_object($pt)) {
		$p = new Permissions($pt);
		if ($p->canEditPageTypePermissions()) {
			$canEditPageTypePermissions = true;
		}
	}
}

if (!$canEditPageTypePermissions) {
	die(t("Access Denied."));
}

?>

<div class="ccm-ui" id="ccm-file-permissions-dialog-wrapper">
	<? Loader::element('permission/lists/page_draft', array('draft' => $draft)); ?>
</div>