<?
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getByID(Loader::helper('security')->sanitizeInt($_REQUEST['cID']));
if (is_object($c) && !$c->isError()) {
	$cp = new Permissions($c);
	if ($cp->canViewToolbar()) { ?>

		<section>
			<header><?=t('Page Settings')?></header>
			<menu>
				<? if ($cp->canEditPageProperties()) { 

					$pk = PermissionKey::getByHandle('edit_page_properties');
					$pk->setPermissionObject($c);
					$asl = $pk->getMyAssignment();
					if (is_object($asl)) {
						$allowedAKIDs = $asl->getAttributesAllowedArray();
					}
					if (is_object($asl) && ($asl->allowEditName() || $asl->allowEditDescription() || $asl->allowEditDateTime() || $asl->allowEditUserID())) { ?>
						<li><a href="#" data-launch-panel-detail="page/properties" data-panel-detail-transition="swap"><?=t('Properties')?></a></li>
					<? }
					if (is_array($allowedAKIDs) && count($allowedAKIDs) > 0) { ?>
						<li><a href=""><?=t('Attributes')?></a></li>
					<? } ?>
					<? if (is_object($asl) && ($asl->allowEditPaths())) { ?>
						<li><a href=""><?=t('Page Paths')?></a></li>
					<? } ?>
				<? } ?>
				<? if ($cp->canViewPageVersions()) { ?>
					<li><a href="#" data-panel-direction="right" data-swap-panel="page/versions"><?=t('Version History')?></a></li>
				<? } ?>
				<? if ($cp->canPreviewPageAsUser() && PERMISSIONS_MODEL == 'advanced') { ?>
					<li><a href="#"><?=t('View as User')?></a></li>
				<? } ?>
				<? if ($cp->canEditPageTheme() || $cp->canEditPageType()) { ?>
					<li><a href=""><?=t('Design')?></a></li>
				<? } ?>
				<? if ($cp->canEditPageSpeedSettings()) { ?>
					<li><a href=""><?=t('Page Caching')?></a></li>
				<? } ?>
				<? if ($cp->canEditPagePermissions()) { ?>
					<li><a href=""><?=t('Permissions')?></a></li>
				<? } ?>
				<? if ($cp->canMoveOrCopyPage() || $cp->canDeletePage()) { ?>
					<li><a href=""><?=t('Location')?></a></li>
				<? } ?>
			</menu>
		</section>


	<? }
}
?>