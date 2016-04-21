<?
	defined('C5_EXECUTE') or die("Access Denied.");

	$this->inc('form.php', array('b' => $b, 'a' => $a));

?>


<input type="hidden" name="arLayoutID" value="<?=$controller->arLayout->getAreaLayoutID()?>" />
<input type="hidden" name="arLayoutEdit" value="1" />

<div id="ccm-layouts-edit-mode" class="ccm-layouts-edit-mode-edit">

<? foreach($columns as $col) { ?>
	<? $i = $col->getAreaLayoutColumnIndex(); ?>
	<div class="<?=$col->getAreaLayoutColumnClass()?>" id="ccm-edit-layout-column-<?=$i?>" <? if ($iscustom) { ?>data-width="<?=$col->getAreaLayoutColumnWidth()?>" <? } ?>>
		<div class="ccm-layout-column-inner ccm-layout-column-highlight">
			<input type="hidden" name="width[<?=$i?>]" value="" id="ccm-edit-layout-column-width-<?=$i?>" />
			<? 
			$col->display(true);
			?>
		</div>
	</div>
<? } ?>

</div>
