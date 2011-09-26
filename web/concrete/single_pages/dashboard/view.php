<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="row">
<div class="span14 offset1 columns">
<div class="ccm-dashboard-pane">

	<div class="ccm-dashboard-pane-header"><h3><?=t('Dashboard Home')?></h3></div>
	<div class="ccm-dashboard-pane-body clearfix">

<ul class="clearfix">

<?php

$page = Page::getByPath('/dashboard');
$children = $page->getCollectionChildrenArray(true);

foreach($children as $ch) {
	$page = Page::getByID($ch);
	?>
	
	<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><span><?=$page->getCollectionName()?></span>
	</a></li>
	
<? } ?>

</ul>

</div>
</div>

</div>
</div>
</div>