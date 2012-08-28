<?
$upToPage = Page::getByPath("/dashboard");
?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('System &amp; Settings'), false, false, true, -1, $upToPage); ?>

<?
for ($i = 0; $i < count($categories); $i++) {
	$cat = $categories[$i];
	?>

	<div class="dashboard-icon-list">
	<div class="well" style="visibility: hidden">


	<ul class="nav nav-list">
	<li class="nav-header"><?=t($cat->getCollectionName())?></li>

	
	<?
	$show = array();
	$subcats = $cat->getCollectionChildrenArray(true);
	foreach($subcats as $catID) {
		$subcat = Page::getByID($catID, 'ACTIVE');
		$catp = new Permissions($subcat);
		if ($catp->canRead()) { 
			$show[] = $subcat;
		}
	}
	
	if (count($show) > 0) { ?>
	
	
	<? foreach($show as $subcat) { ?>
	
	<li>
	<a href="<?=Loader::helper('navigation')->getLinkToCollection($subcat, false, true)?>"><i class="<?=$subcat->getAttribute('icon_dashboard')?>"></i> <?=t($subcat->getCollectionName())?></a>
	</li>
	
	<? } ?>
	
	
	<? } else { ?>
	
	<li>
		<a href="<?=Loader::helper('navigation')->getLinkToCollection($cat, false, true)?>"><i class="<?=$cat->getAttribute('icon_dashboard')?>"></i> <?=t('Home')?></a>
	</li>
			
	<? } ?>
	
	</ul>

	</div>
	</div>
	
<? } ?>


	<div class="clearfix">
	</div>

<script type="text/javascript">
$(function() {
	ccm_dashboardEqualizeMenus();
	$(window).resize(function() {
		ccm_dashboardEqualizeMenus();
	});
});
</script>


<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
