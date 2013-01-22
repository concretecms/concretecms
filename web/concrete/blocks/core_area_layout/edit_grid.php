<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$this->inc('form.php');

?>

<input type="hidden" name="arLayoutID" value="<?=$controller->arLayout->getAreaLayoutID()?>" />

<?=$themeGridFramework->getPageThemeGridFrameworkRowStartHTML()?>

<div id="ccm-layouts-edit-mode">

<? foreach($columns as $col) { ?>
	<? $i = $col->getAreaLayoutColumnIndex(); ?>
	<div class="ccm-theme-grid-column ccm-theme-grid-column-edit-mode <?=$col->getAreaLayoutColumnClass()?>" id="ccm-edit-layout-column-<?=$i?>" data-span="<?=$col->getAreaLayoutColumnWidth()?>">
		<div class="ccm-layout-column-inner ccm-layout-column-highlight">
			<input type="hidden" name="width[<?=$i?>]" value="<?=$col->getAreaLayoutColumnWidth()?>" id="ccm-edit-layout-column-width-<?=$i?>" />
			<? 
			$col->display(true);
			?>
		</div>
	</div>
<? } ?>

</div>

<?=$themeGridFramework->getPageThemeGridFrameworkRowEndHTML()?>
