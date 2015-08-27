<?
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
$factors = array('' => t('Sitemap Pop-Up'), 'sitemap_in_page' => t('In-Page Sitemap'));
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $type->getPageTypePublishTargetTypeID()) {
	$configuredTarget = $pagetype->getPageTypePublishTargetObject();
	$selectorFormFactor = $configuredTarget->getSelectorFormFactor();
}
?>

<div class="control-group">
	<?=$form->label('selectorFormFactorAll', t('Selector Form Factor'))?>
	<div class="controls">
		<?=$form->select('selectorFormFactorAll', $factors, $selectorFormFactor)?>
	</div>
</div>