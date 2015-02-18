<?php
	defined('C5_EXECUTE') or die("Access Denied.");
	$a = $b->getBlockAreaObject();
?>

<div class="ccm-layout-column-wrapper" id="ccm-layout-column-wrapper-<?=$bID?>">

<?php foreach($columns as $col) { ?>
	<div class="<?=$col->getAreaLayoutColumnClass()?>" id="ccm-layout-column-<?=$col->getAreaLayoutColumnID()?>">
		<div class="ccm-layout-column-inner">
			<?php
			$col->display();
			?>
		</div>
	</div>

<?php } ?>

</div>