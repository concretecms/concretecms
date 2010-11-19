<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<div id="blog-index">
	<?php  
	$isFirst = true; //So first item in list can have a different css class (e.g. no top border)
	$excerptBlocks = ($controller->truncateSummaries ? 1 : null); //1 is the number of blocks to include in the excerpt
	$truncateChars = ($controller->truncateSummaries ? $controller->truncateChars : 0);
	foreach ($cArray as $cobj):
		$title = $cobj->getCollectionName();
		$date = $cobj->getCollectionDatePublic('F j, Y');
		$author = $cobj->getVersionObject()->getVersionAuthorUserName();
		$link = $nh->getLinkToCollection($cobj);
		$firstClass = $isFirst ? 'first-entry' : '';
		
		$entryController = Loader::controller($cobj);
		$comments = $entryController->getCommentCountString('%s '.t('Comment'), '%s '.t('Comments'));
		
		$isFirst = false;
	?>
	<div class="entry <?php  echo $firstClass; ?>">
		<div class="title">
			<h3>
				<a href="<?php  echo $link; ?>"><?php  echo $title; ?></a>
			</h3>
			<h4>
				Posted by <?php  echo $author; ?> on <?php  echo $date; ?>
			</h4>
		</div>
		<div class="excerpt">
			<?php 
			$a = new Area('Main');
			$a->disableControls();
			$a->display($cobj);
			?>
		</div>
		<div class="ccm-spacer"></div>
		<div class="meta">
			<?php  echo $comments; ?> <a href="<?php  echo $link; ?>">Read full post &raquo;</a>
		</div>
	</div>
	<hr class="blog-entry-divider"/>
	<?php  endforeach; ?>
</div>

<div id="blog-index-foot">
	<?php  if(!$previewMode && $controller->rss):
		$btID = $b->getBlockTypeID();
		$bt = BlockType::getByID($btID);
		$uh = Loader::helper('concrete/urls');
		$rssUrl = $controller->getRssUrl($b, 'blog_rss');
		$rssIcon = $uh->getBlockTypeAssetsURL($bt, 'rss.png');
		$rssTitle = $controller->rssTitle;
	?>
		<div id="rss">
			<a href="<?php  echo $rssUrl; ?>" target="_blank"><?php  echo t('Subscribe to RSS Feed')?></a>
			<a href="<?php  echo $rssUrl; ?>" target="_blank"><img src="<?php  echo $rssIcon; ?>" width="14" height="14" /></a>
		</div>
		<link href="<?php  echo $rssUrl; ?>" rel="alternate" type="application/rss+xml" title="<?php  echo $rssTitle; ?>" />
	<?php  endif; ?>
	

	<?php  if ($paginate && $num > 0 && is_object($pl)): ?>
		<div id="pagination">
			<?php 
			$summary = $pl->getSummary();
			if ($summary->pages > 1):
				$paginator = $pl->getPagination();
			?>
				<span class="pagination-left"><?php  echo $paginator->getPrevious('&laquo; Newer Posts'); ?></span>
				<span class="pagination-right"><?php  echo $paginator->getNext('Older Posts &raquo;'); ?></span>
				<?php  echo $paginator->getPages(); ?>
			<?php  endif; ?>
		</div>
	<?php  endif; ?>
</div>
