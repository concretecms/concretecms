<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	$textHelper = Loader::helper("text");
	$imgHelper = Loader::Helper('image');
	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	
	if (count($cArray) > 0) { ?>
	
	<?php 
	for ($i = 0; $i < count($cArray); $i++ ) {
		$cobj = $cArray[$i]; 
		$target = $cobj->getAttribute('nav_target');

		$title = $cobj->getCollectionName();
		$date = $cobj->getCollectionDatePublic('M j, Y'); ?>

	<div class="grid_4 main-content-thumb">
	<h4><?php echo "&#151; " . $date; ?></h4>
	<div class="image-link">
	<a <?php  if ($target != '') { ?> target="<?php echo $target?>" <?php  } ?> href="<?php echo $nh->getLinkToCollection($cobj)?>">
	<?php 
		$ts = $cobj->getBlocks('Thumbnail Image');
		if (is_object($ts[0])) {
			$tsb = $ts[0]->getInstance();
			$thumb = $tsb->getFileObject();
			if($thumb){
			$imgHelper->outputThumbnail($thumb, 220, 220, $title);
			}
		}
	?></a>
	</div>
	<h3><a <?php  if ($target != '') { ?> target="<?php echo $target?>" <?php  } ?> href="<?php echo $nh->getLinkToCollection($cobj)?>"><?php echo $title?></a></h3>
	<!-- <h3><a <?php  if ($target != '') { ?> target="<?php echo $target?>" <?php  } ?> href="<?php echo $nh->getLinkToCollection($cobj)?>"><?php echo $textHelper->wordSafeShortText($title,$controller->truncateChars);?></a></h3> -->
	<p>
		<?php 
		if(!$controller->truncateSummaries){
			echo $cobj->getCollectionDescription();
		}else{
			echo $textHelper->wordSafeShortText($cobj->getCollectionDescription(),$controller->truncateChars);
		}
		?>
	</p>
	
	</div>
	
<?php   } 
	if(!$previewMode && $controller->rss) { 
			$btID = $b->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$uh = Loader::helper('concrete/urls');
			$rssUrl = $controller->getRssUrl($b, 'blog_rss');
			?>
			<div class="ccm-page-list-rss-icon">
				<a href="<?php echo $rssUrl?>" target="_blank"><img src="<?php echo $uh->getBlockTypeAssetsURL($bt, 'rss.png')?>" width="14" height="14" /></a>
			</div>
			<link href="<?php echo BASE_URL . $rssUrl?>" rel="alternate" type="application/rss+xml" title="<?php echo $controller->rssTitle?>" />
		<?php  
	} 
	?>

<?php  }

	if ($paginate && $num > 0 && is_object($pl)): ?>
		<div id="pagination">
			<?php
			$summary = $pl->getSummary();
			if ($summary->pages > 1):
				$paginator = $pl->getPagination();
			?>
				<span class="pagination-left">&laquo; <?php echo $paginator->getPrevious(t('Newer Posts')); ?></span>
				<span class="pagination-right"><?php echo $paginator->getNext(t('Older Posts')); ?> &raquo;</span>
				<?php echo $paginator->getPages(); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
