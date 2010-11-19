<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	$textHelper = Loader::helper("text"); 
	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	
	if (count($cArray) > 0) { ?>
	<div class="ccm-page-list">
	
	<?php 
	for ($i = 0; $i < count($cArray); $i++ ) {
		$cobj = $cArray[$i]; 
		$target = $cobj->getAttribute('nav_target');

		$title = $cobj->getCollectionName(); ?>
	
	<h3 class="ccm-page-list-title"><a <?php  if ($target != '') { ?> target="<?php echo $target?>" <?php  } ?> href="<?php echo $nh->getLinkToCollection($cobj)?>"><?php echo $title?></a></h3>
	<div class="ccm-page-list-description">
		<?php 
		if(!$controller->truncateSummaries){
			echo $cobj->getCollectionDescription();
		}else{
			echo $textHelper->shorten($cobj->getCollectionDescription(),$controller->truncateChars);
		}
		?>
	</div>
	
<?php   } 
	if(!$previewMode && $controller->rss) { 
			$btID = $b->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$uh = Loader::helper('concrete/urls');
			$rssUrl = $controller->getRssUrl($b);
			?>
			<div class="ccm-page-list-rss-icon">
				<a href="<?php echo $rssUrl?>" target="_blank"><img src="<?php echo $uh->getBlockTypeAssetsURL($bt, 'rss.png')?>" width="14" height="14" /></a>
			</div>
			<link href="<?php echo BASE_URL . $rssUrl?>" rel="alternate" type="application/rss+xml" title="<?php echo $controller->rssTitle?>" />
		<?php  
	} 
	?>
</div>
<?php  } 
	
	if ($paginate && $num > 0 && is_object($pl)) {
		$pl->displayPaging();
	}
	
?>