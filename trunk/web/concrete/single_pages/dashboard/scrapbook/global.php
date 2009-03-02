<?
$globalScrapbookArea = new Area('Global Scrapbook');
$ih = Loader::helper('concrete/interface'); 
?>

<h1><span><?=t('Global Scrapbook')?></span></h1>

<div class="ccm-dashboard-inner">
	
	<script>
	var ccm_areaScrapbookObj6 = new Object();
	ccm_areaScrapbookObj6.type = "AREA";	
	ccm_areaScrapbookObj6.aID = <?=intval($globalScrapbookArea->getAreaID()) ?>;
	ccm_areaScrapbookObj6.arHandle = "<?=$globalScrapbookArea->getAreaHandle() ?>";	
	addGlobalBlock = function(e){  
		ccm_showAreaMenu(ccm_areaScrapbookObj6,e); 
	}
	</script>
	
	<?= $ih->button_js( t('Add Block to Scrapbook'), 'addGlobalBlock(event)','left'); ?>
	
	<div class="ccm-spacer"></div>
	
	<div id="globalBlocksList">
		<? 
		$globalScrapbookArea->display($c);
		?>	
	</div> 	

</div>