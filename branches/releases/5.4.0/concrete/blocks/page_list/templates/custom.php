<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$textHelper = Loader::helper("text"); 
	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	
	if (count($cArray) > 0) { ?>
	<div class="ccm-page-list">
	
	<?php 
	for ($i = 0; $i < count($cArray); $i++ ) {
		$cobj = $cArray[$i]; 
		$title = $cobj->getCollectionName(); ?>
	
	<h3 class="ccm-page-list-title"><a href="<?php echo $nh->getLinkToCollection($cobj)?>"><?php echo $title?></a></h3>
	<?php  if ($cobj->getCollectionTypeHandle()=="Press Release") { ?>
		<h4><?php  echo $cobj->getCollectionAttributeValue('Press_Release_Type'); ?> - for release on <?php  echo strftime("%x %l:%M%p",strtotime($cobj->getCollectionAttributeValue('Release_Date'))); ?></h4>
	<?php  } ?>
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
			<div class="rssIcon">
				<a href="<?php echo $rssUrl?>" target="_blank"><img src="<?php echo $uh->getBlockTypeAssetsURL($bt, 'rss.png')?>" width="14" height="14" /></a>
				
			</div>
			<link href="<?php echo $rssUrl?>" rel="alternate" type="application/rss+xml" title="<?php echo $controller->rssTitle?>" />
		<?php  
	} 
	?>
</div>
<?php  } 
	
	if ($paginate && $num > 0 && is_object($pl)) {
		$pl->displayPaging();
	}
	
?>