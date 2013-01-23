<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$this->inc('form.php');

?>

<input type="hidden" name="arLayoutID" value="<?=$controller->arLayout->getAreaLayoutID()?>" />

<div id="ccm-layouts-edit-mode">

<?=$themeGridFramework->getPageThemeGridFrameworkRowStartHTML()?>

<div id="ccm-theme-grid-edit-mode-row-wrapper">

<? foreach($columns as $col) { ?>
	<? $i = $col->getAreaLayoutColumnIndex(); ?>
	<? if ($col->getAreaLayoutColumnOffset() > 0) { ?>
		<div class="ccm-theme-grid-offset-column <?=$col->getAreaLayoutColumnOffsetEditClass()?>"></div>
	<? } ?>

	<div class="ccm-theme-grid-column ccm-theme-grid-column-edit-mode <?=$col->getAreaLayoutColumnClass()?>" id="ccm-edit-layout-column-<?=$i?>" data-offset="<?=$col->getAreaLayoutColumnOffset()?>" data-span="<?=$col->getAreaLayoutColumnSpan()?>">
		<div class="ccm-layout-column-inner ccm-layout-column-highlight">
			<input type="hidden" name="span[<?=$i?>]" value="<?=$col->getAreaLayoutColumnSpan()?>" id="ccm-edit-layout-column-span-<?=$i?>" />
			<input type="hidden" name="offset[<?=$i?>]" value="<?=$col->getAreaLayoutColumnOffset()?>" id="ccm-edit-layout-column-offset-<?=$i?>" />
			<? 
			$col->display(true);
			?>
		</div>
	</div>
<? } ?>

</div>

<?=$themeGridFramework->getPageThemeGridFrameworkRowEndHTML()?>

</div>