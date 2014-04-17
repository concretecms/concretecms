<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?
for ($i = 0; $i < count($categories); $i++) {
	$cat = $categories[$i];
	?>

	<h4><?=t($cat->getCollectionName())?></h4>

	
	<?
	$show = array();
	$subcats = $cat->getCollectionChildrenArray(true);
	foreach($subcats as $catID) {
		$subcat = Page::getByID($catID, 'ACTIVE');
		$catp = new Permissions($subcat);
		if ($catp->canRead()) { 
			$show[] = $subcat;
		}
	}
	
	if (count($show) > 0) { ?>
	
	
	<ol class="breadcrumb">

	<? foreach($show as $subcat) { ?>
	
	<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subcat, false, true)?>"><i class="<?=$subcat->getAttribute('icon_dashboard')?>"></i> <?=t($subcat->getCollectionName())?></a></li>
	
	<? } ?>
		
	<? } else { ?>

	<li><a href="<?=Loader::helper('navigation')->getLinkToCollection($cat, false, true)?>"><i class="<?=$cat->getAttribute('icon_dashboard')?>"></i> <?=t('Home')?></a</li>
			
	<? } ?>
	
	</ol>

	<br/>
<? } ?>