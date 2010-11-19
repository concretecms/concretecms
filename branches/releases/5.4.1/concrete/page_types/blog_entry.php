<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
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