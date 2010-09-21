<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="pageSection">
	<?php $ai = new Area('Blog Post Header'); $ai->display($c); ?>
</div>
<div class="pageSection">
	<?php $as = new Area('Blog Summary'); $as->display($c); ?>
</div>
<div class="pageSection">
	<?php $a = new Area('Main'); $a->display($c); ?>
</div>