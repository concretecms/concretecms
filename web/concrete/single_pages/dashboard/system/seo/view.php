<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('System &amp; Settings'));?>
<form>
<?
foreach($categories as $cat) { ?>

	<div class="page-header">
	<h3><a href="<?=Loader::helper('navigation')->getLinkToCollection($cat)?>"><?=$cat->getCollectionName()?></a>
	<small><?=$cat->getCollectionDescription()?></small>
	</h3>
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
	
	<div class="clearfix">
	
	<? foreach($show as $subcat) { ?>
	
	<div class="span4">
		<a href="<?=Loader::helper('navigation')->getLinkToCollection($cat)?>"><?=$subcat->getCollectionName()?></a>
	</div>
	
	<? } ?>
	
	</div>
	
	<? } ?>

<? } ?>
</form>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
