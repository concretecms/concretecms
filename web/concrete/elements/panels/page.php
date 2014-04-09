<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
	use Concrete\Core\Attribute\Set as AttributeSet;
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
			<li><a href="#" data-launch-panel-detail="page-composer" data-panel-detail-url="<?=URL::to('/system/panels/details/page/composer')?>" data-panel-transition="swap"><?=t('Composer')?></a></li>
		<? } ?>
		<? if ($cp->canEditPageTheme() || $cp->canEditPageTemplate()) { ?>
			<li><a href="#" data-launch-sub-panel-url="<?=URL::to('/system/panels/page/design')?>" data-launch-panel-detail="page-design" data-panel-detail-url="<?=URL::to('/system/panels/details/page/preview')?>" data-panel-transition="fade" ><?=t('Design')?></a></li>
		<? }

		if ($cp->canEditPageProperties() && is_object($seoSet)) { ?>
			<li><a href="#" data-launch-panel-detail="page-seo" data-panel-detail-url="<?=URL::to('/system/panels/details/page/seo')?>" data-panel-transition="swap"><?=t('SEO')?></a></li>
		<? }
		if ($c->getCollectionID() != HOME_CID && is_object($asl) && ($asl->allowEditPaths())) { ?>
			<li><a href="#" data-launch-panel-detail="page-location" data-panel-detail-url="<?=URL::to('/system/panels/details/page/location')?>" data-panel-transition="swap"><?=t('Location')?></a></li>
		<? } ?>
	</menu>
	<menu>
		<? 
		if ($cp->canEditPageProperties()) {
			if (is_object($asl)) {
				$allowedAKIDs = $asl->getAttributesAllowedArray();
			}
			if (is_array($allowedAKIDs) && count($allowedAKIDs) > 0) { ?>
				<li><a href="#" data-launch-sub-panel-url="<?=URL::to('/system/panels/page/attributes')?>" data-launch-panel-detail="page-attributes" data-panel-detail-url="<?=URL::to('/system/panels/details/page/attributes')?>" data-panel-transition="fade"><?=t('Attributes')?></a></li>
			<? } ?>
		<? } ?>
		<? if ($cp->canEditPageSpeedSettings()) { ?>
				<li><a href="#" data-launch-panel-detail="page-caching" data-panel-detail-url="<?=URL::to('/system/panels/details/page/caching')?>" data-panel-transition="fade"><?=t('Caching')?></a></li>
		<? } ?>
		<? if ($cp->canEditPagePermissions()) { ?>
				<li><a href="#" data-launch-panel-detail="page-permissions" data-panel-detail-url="<?=URL::to('/system/panels/details/page/permissions')?>" data-panel-transition="fade"><?=t('Permissions')?></a></li>
		<? } ?>
		<? if ($cp->canViewPageVersions()) { ?>
			<li><a href="#" data-launch-sub-panel-url="<?=URL::to('/system/panels/page/versions')?>"><?=t('Versions')?></a></li>
		<? } ?>
		<? if ($cp->canPreviewPageAsUser() && PERMISSIONS_MODEL == 'advanced') { ?>
			<li><a href="#"><?=t('View as User')?></a></li>
		<? } ?>
		<? if ($cp->canDeletePage()) { ?>
			<li><a class="dialog-launch" href="<?=URL::to('/system/dialogs/page/delete')?>?cID=<?=$c->getCollectionID()?>" dialog-modal="true" dialog-title="<?=t('Delete Page')?>" dialog-width="400" dialog-height="250"><?=t('Delete Page')?></a></li>
		<? } ?>
	</menu>
</section>