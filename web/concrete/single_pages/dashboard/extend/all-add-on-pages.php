<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('All Add-On Dashboard Pages')); ?>

<?
print '<div class="row">';
for ($i = 0; $i < count($pages); $i++) {
	$cat = $pages[$i];
	?>

	
	<? if ($i % 2 == 0) { ?>
		</div>
		<div class="row">
	<? } ?>
	
	<div class="span-pane-half">
	


	<div class="ccm-dashboard-system-category">
	<h3><a href="<?=Loader::helper('navigation')->getLinkToCollection($cat, false, true)?>"><?=t($cat->getCollectionName())?></a></h3>
	</div>
	
	<?
	$show = array();
	$subcats = $cat->getCollectionChildrenArray(true);
	foreach($subcats as $catID) {
		$subcat = Page::getByID($catID, 'ACTIVE');
		$catp = new Permissions($subcat);
		if ($catp->canRead() && $subcat->getAttribute('exclude_nav') != 1) { 
			$show[] = $subcat;
		}
	}
	
	if (count($show) > 0) { ?>
	
	<div class="ccm-dashboard-system-category-inner">
	
	<? foreach($show as $subcat) { ?>
	
	<div>
	<a href="<?=Loader::helper('navigation')->getLinkToCollection($subcat, false, true)?>"><?=t($subcat->getCollectionName())?></a>
	</div>
	
	<? } ?>
	
	</div>
	
	<? } ?>
	
	</div>
	
	
<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
