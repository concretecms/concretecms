<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$this->inc('form.php');

?>

<div id="ccm-area-layout-active-control-bar" class="ccm-area-layout-control-bar"></div>

<div id="ccm-layouts-edit-mode">

<? foreach($columns as $col) { ?>
	<? $i = $col->getAreaLayoutColumnIndex(); ?>
	<div class="ccm-layout-column" id="ccm-edit-layout-column-<?=$i?>" <? if ($iscustom) { ?>data-width="<?=$col->getAreaLayoutColumnWidth()?>" <? } ?>>
		<div class="ccm-layout-column-inner ccm-layout-column-highlight">
			<input type="hidden" name="width[<?=$i?>]" value="" id="ccm-edit-layout-column-width-<?=$i?>" />
			<? 
			$as = new SubArea($col->getAreaLayoutColumnIndex(), $a);
			$as->disableControls();
			$as->display($c);
			?>
		</div>
	</div>

<? } ?>

</div>