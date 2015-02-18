<?php
	defined('C5_EXECUTE') or die("Access Denied.");
	$a = $b->getBlockAreaObject();
?>

<?=$gf->getPageThemeGridFrameworkRowStartHTML()?>

<?php foreach($columns as $col) { ?>
	<?php if ($col->getAreaLayoutColumnOffset() > 0 && (!$gf->hasPageThemeGridFrameworkOffsetClasses())) { ?>
		<div class="<?=$col->getAreaLayoutColumnOffsetClass()?> ccm-theme-grid-offset-column"></div>
	<?php } ?>
	<div class="<?=$col->getAreaLayoutColumnClass()?><?php if ($gf->hasPageThemeGridFrameworkOffsetClasses() && $col->getAreaLayoutColumnOffset()) { ?> <?=$col->getAreaLayoutColumnOffsetClass()?><?php } ?>"><?php
		$col->display();
	?></div>

<?php } ?>

<?=$gf->getPageThemeGridFrameworkRowEndHTML()?>