<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$ctArray = PageType::getList();
$types = array('' => t('** Choose a page type'));
foreach($ctArray as $cta) {
    $types[$cta->getPageTypeID()] = $cta->getPageTypeDisplayName();
}
$ptID = 0;
$factors = array('' => t('Select Menu'), 'sitemap_in_page' => t('In-Page Sitemap'));

if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $type->getPageTypePublishTargetTypeID()) {
	$configuredTarget = $pagetype->getPageTypePublishTargetObject();
	$ptID = $configuredTarget->getPageTypeID();
	$selectorFormFactor = $configuredTarget->getSelectorFormFactor();
}
?>
	<div class="control-group">
		<?=$form->label('ptID', t('Publish Beneath Pages of Type'))?>
		<div class="controls">
			<?=$form->select('ptID', $types, $ptID)?>
		</div>
	</div>

<div class="control-group">
	<?=$form->label('selectorFormFactorPageType', t('Selector Form Factor'))?>
	<div class="controls">
		<?=$form->select('selectorFormFactorPageType', $factors, $selectorFormFactor)?>
	</div>
</div>