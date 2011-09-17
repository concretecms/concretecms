<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<h1><?=t('Manage concrete5')?></h1>

<ul class="ccm-tile-menu clearfix">

<?php

$page = Page::getByPath('/dashboard');
$children = $page->getCollectionChildrenArray(true);

foreach($children as $ch) {
	$page = Page::getByID($ch);
	?>
	
	<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($page)?>"><span><?=$page->getCollectionName()?></span>
		<?=$page->getCollectionDescription()?>
	</a></li>
	
<? } ?>

</ul>