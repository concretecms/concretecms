<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$draft = ComposerDraft::getByID($_REQUEST['cmpDraftID']);
$canEditComposerPermissions = false;
if (is_object($draft)) {
	$cmp = $draft->getComposerObject();
	if (is_object($cmp)) {
		$p = new Permissions($cmp);
		if ($p->canEditComposerPermissions()) {
			$canEditComposerPermissions = true;
		}
	}
}

if (!$canEditComposerPermissions) {
	die(t("Access Denied."));
}

?>

<div class="ccm-ui" id="ccm-file-permissions-dialog-wrapper">
	<? Loader::element('permission/lists/composer_draft', array('draft' => $draft)); ?>
</div>