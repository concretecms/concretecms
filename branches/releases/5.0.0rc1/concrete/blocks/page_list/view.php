<?php 

	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	
	if (count($cArray) > 0) { ?>
	<div class="ccm-page-list">
	
	<?php 
	for ($i = 0; $i < count($cArray); $i++ ) {
		$cobj = $cArray[$i]; 
		$title = $cobj->getCollectionName(); ?>
	
	<h3><a href="<?php echo $nh->getLinkToCollection($cobj)?>"><?php echo $title?></a></h3>
	<div class="ccm-page-list-description"><?php echo $cobj->getCollectionDescription()?></div>
	
<?php   } 
	if(!$previewMode && $controller->rss) { 
			$b = $controller->getBlockObject();
			$btID = $b->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$uh = Loader::helper('concrete/urls');
			$rssUrl = $controller->getRssUrl($b);
			?>
			<div class="rssIcon">
				<a href="<?php echo $rssUrl?>" target="_blank"><img src="<?php echo $uh->getBlockTypeAssetsURL($bt)?>/rss.png" width="14" height="14" /></a>
				
			</div>
			<link href="<?php echo $rssUrl?>" rel="alternate" type="application/rss+xml" title="<?php echo $controller->rssTitle?>" />
		<?php  
	} 
	?>
</div>
<?php  } ?>