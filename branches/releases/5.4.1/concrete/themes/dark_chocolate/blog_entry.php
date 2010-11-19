<?php  defined('C5_EXECUTE') or die("Access Denied.");
$this->inc('elements/header.php'); ?>

	<div id="central">
		<div id="sidebar">
			<?php 
			$as = new Area('Sidebar');
			$as->display($c);
			?>		
		</div>
		
		<div id="body">
			<div class="pageSection">
				<?php  $ai = new Area('Blog Post Header'); $ai->display($c); ?>
			</div>
			<div class="pageSection">
				<h1><?php  echo $c->getCollectionName(); ?></h1>
				<p class="meta"><?php  echo t('Posted by')?> <?php  echo $c->getVersionObject()->getVersionAuthorUserName(); ?> on <?php  echo $c->getCollectionDatePublic('F j, Y'); ?></p>		
			</div>
			<div class="pageSection">
				<?php  $as = new Area('Main'); $as->display($c); ?>
			</div>
			<div class="pageSection">
				<?php  $a = new Area('Blog Post More'); $a->display($c); ?>
			</div>
			<div class="pageSection">
				<?php  $ai = new Area('Blog Post Footer'); $ai->display($c); ?>
			</div>
		</div>
		
		<div class="spacer">&nbsp;</div>		
	</div>

<?php  $this->inc('elements/footer.php'); ?>