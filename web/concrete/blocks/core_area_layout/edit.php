<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$this->inc('form.php');

?>

<div id="ccm-layouts-edit-mode">

<? for ($i = 0; $i < $columns; $i++) { ?>

	<div class="ccm-layout-column" id="ccm-edit-layout-column-<?=$i?>">
		<div class="ccm-layout-column-inner ccm-layout-column-highlight">
			<input type="hidden" name="width[<?=$i?>]" value="" id="ccm-edit-layout-column-width-<?=$i?>" />
			<? 
			$arHandle = 'Column ' . ($i + 1);
			$as = new SubArea($arHandle, $a);
			$as->disableControls();
			$as->display($c);
			?>
		</div>
	</div>

<? } ?>

</div>
