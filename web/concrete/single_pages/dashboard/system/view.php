
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('System &amp; Settings'));?>
<?
foreach($categories as $cat) { ?>

	<div class="ccm-dashboard-system-category">
	<h3><a href="<?=Loader::helper('navigation')->getLinkToCollection($cat)?>"><?=$cat->getCollectionName()?></a></h3>
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
	
	<div class="span4">
		<a href="<?=Loader::helper('navigation')->getLinkToCollection($subcat)?>"><?=$subcat->getCollectionName()?></a>
	</div>
	
	<? } ?>
	
	</div>
	
	<? } ?>

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
