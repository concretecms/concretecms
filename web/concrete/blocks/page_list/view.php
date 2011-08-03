<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$textHelper = Loader::helper("text"); 
	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	
	if (count($cArray) > 0) { ?>
	<div class="ccm-page-list">
	
	<?
	for ($i = 0; $i < count($cArray); $i++ ) {
		$cobj = $cArray[$i]; 
		$target = $cobj->getAttribute('nav_target');

		if ($cobj->getCollectionPointerExternalLink() != '') {
			if ($cobj->openCollectionPointerExternalLinkInNewWindow()) {
				$target = "_blank";
			}
		}

		$title = $textHelper->entities($cobj->getCollectionName()); ?>
	
	<h3 class="ccm-page-list-title"><a <? if ($target != '') { ?> target="<?=$target?>" <? } ?> href="<?=$nh->getLinkToCollection($cobj)?>"><?=$title?></a></h3>
	<div class="ccm-page-list-description">
		<?
		if(!$controller->truncateSummaries){
			echo $textHelper->entities($cobj->getCollectionDescription());
		}else{
			echo $textHelper->entities($textHelper->shorten($cobj->getCollectionDescription(),$controller->truncateChars));
		}
		?>
	</div>
	
<?  } 
	if(!$previewMode && $controller->rss) { 
			$btID = $b->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$uh = Loader::helper('concrete/urls');
			$rssUrl = $controller->getRssUrl($b);
			?>
			<div class="ccm-page-list-rss-icon">
				<a href="<?=$rssUrl?>" target="_blank"><img src="<?=$uh->getBlockTypeAssetsURL($bt, 'rss.png')?>" width="14" height="14" alt="<?php echo t('RSS Icon')?>" title="<?php echo t('RSS Feed')?>" /></a>
			</div>
			<link href="<?=BASE_URL . $rssUrl?>" rel="alternate" type="application/rss+xml" title="<?=$textHelper->entities($controller->rssTitle)?>" />
		<? 
	} 
	?>
</div>
<? } 
	
	if ($paginate && $num > 0 && is_object($pl)) {
		$pl->displayPaging();
	}
	
?>