<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$a = $b->getBlockAreaObject();
?>

<div class="ccm-layout-column-wrapper" id="ccm-layout-column-wrapper-<?=$bID?>">

<? foreach($columns as $col) { ?>
	<div class="<?=$col->getAreaLayoutColumnClass()?>" id="ccm-layout-column-<?=$col->getAreaLayoutColumnID()?>">
		<div class="ccm-layout-column-inner">
			<? 
			$col->display();
			?>
		</div>
	</div>

<? } ?>

</div>