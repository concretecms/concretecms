<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
	$cp = new Permissions($c);
	$pk = PermissionKey::getByHandle('edit_page_properties');
	$pk->setPermissionObject($c);
	$asl = $pk->getMyAssignment();
	$seoSet = AttributeSet::getByHandle('seo');
?>
<section>
	<header><?=t('Page Settings')?></header>
	<menu class="ccm-panel-page-basics">
		<? 
		$pagetype = PageType::getByID($c->getPageTypeID());
		if (is_object($pagetype)) { ?>
			<li><a href="#" data-launch-panel-detail="page-composer" data-panel-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/panels/details/page/composer" data-panel-transition="swap"><?=t('Composer')?></a></li>
		<? } ?>
		<? if ($cp->canEditPageTheme() || $cp->canEditPageTemplate()) { ?>
			<li><a href="#" data-launch-sub-panel="page/design"><?=t('Design')?></a></li>
		<? }

		if ($cp->canEditPageProperties() && is_object($seoSet)) { ?>
			<li><a href="#" data-launch-panel-detail="page-seo" data-panel-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/panels/details/page/seo" data-panel-transition="swap"><?=t('SEO')?></a></li>
		<? }
		if (!$c->isPageDraft() && is_object($asl) && ($asl->allowEditPaths())) { ?>
			<li><a href=""><?=t('Location')?></a></li>
		<? } ?>
	</menu>
	<menu>
		<? 
		if ($cp->canEditPageProperties()) {
			if (is_object($asl)) {
				$allowedAKIDs = $asl->getAttributesAllowedArray();
			}
			if (is_array($allowedAKIDs) && count($allowedAKIDs) > 0) { ?>
				<li><a href=""><?=t('Attributes')?></a></li>
			<? } ?>
		<? } ?>
		<? if ($cp->canEditPageSpeedSettings()) { ?>
			<li><a href=""><?=t('Caching')?></a></li>
		<? } ?>
		<? if ($cp->canEditPagePermissions()) { ?>
			<li><a href=""><?=t('Permissions')?></a></li>
		<? } ?>
		<? if ($cp->canViewPageVersions()) { ?>
			<li><a href="#" data-launch-sub-panel="page/versions"><?=t('Versions')?></a></li>
		<? } ?>
		<? if ($cp->canPreviewPageAsUser() && PERMISSIONS_MODEL == 'advanced') { ?>
			<li><a href="#"><?=t('View as User')?></a></li>
		<? } ?>
		<? if ($cp->canMoveOrCopyPage() || $cp->canDeletePage()) { ?>
			<li><a href=""><?=t('Delete Page')?></a></li>
		<? } ?>
	</menu>
</section>