<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$minColumns = 1;
?>

<ul id="ccm-layouts-toolbar">
	<? if ($enableThemeGrid) { ?>
	<li>
		<label for="useThemeGrid"><?=t("Grid Type")?></label>
		<? if ($controller->getTask() == 'edit') { ?>
		<select name="useThemeGrid" id="useThemeGrid" style="width: auto !important" disabled="disabled">
			<option value="1"><?=$themeGridName?></option>
		</select>
		<? } else { ?>

		<select name="useThemeGrid" id="useThemeGrid" style="width: auto !important">
			<option value="1"><?=$themeGridName?></option>
			<option value="0"><?=t('Free-Form Layout')?></option>
		</select>

		<? } ?>

	</li>
	<li data-grid-control="page-theme" class="ccm-page-theme-grid-framework">
		<label for="themeGridColumns"><?=t("Columns")?></label>
		<? if ($controller->getTask() == 'edit') { ?>
		<select name="themeGridColumns" id="themeGridColumns" disabled="disabled">
			<option value="<?=$columnsNum?>"><?=$columnsNum?></option>
		</select>
		<? } else { ?>
		
		<select name="themeGridColumns" id="themeGridColumns">
			<? for ($i = $minColumns; $i <= $themeGridMaxColumns; $i++) { ?>
				<option value="<?=$i?>" <? if (is_array($columns) && (count($columns) == $i)) { ?> selected <? } ?>><?=$i?></option>
			<? } ?>
		</select>
		<? } ?>
	</li>
	<? } ?>
	<li data-grid-control="layout">
		<label for="columns"><?=t("Columns")?></label>

		<? if ($controller->getTask() == 'edit') { ?>
		<select name="columns" id="columns" disabled="disabled">
			<option value="<?=$columnsNum?>"><?=$columnsNum?></option>
		</select>
		<? } else { ?>
		<select name="columns" id="columns">
			<? for ($i = $minColumns; $i <= $maxColumns; $i++) { ?>
				<option value="<?=$i?>" <? if (is_array($columns) && (count($columns) == $i)) { ?> selected <? } ?>><?=$i?></option>
			<? } ?>
		</select>
		<? } ?>
	</li>
	<li data-grid-control="layout" class="ccm-layouts-toolbar-separator"></li>
	<li data-grid-control="layout" >
		<label for="columns"><?=t("Spacing")?></label>
		<input name="spacing" id="spacing" style="width: 30px" value="<?=$spacing?>" />
	</li>
	<li data-grid-control="layout" class="ccm-layouts-toolbar-separator"></li>
	<li data-grid-control="layout" >
		<label style="vertical-align: middle"><?=t("Automatic Widths")?>
		<input style="vertical-align: middle" type="checkbox" value="1" name="isautomated" <? if (!$iscustom) { ?>checked="checked" <? } ?> />
		</label>
	</li>

	<li class="ccm-layouts-toolbar-save ccm-ui">
		<button id="ccm-layouts-cancel-button" type="button" class="btn btn-mini"><?=t("Cancel")?></button>
		<button id="ccm-layouts-save-button" type="button" class="btn btn-primary btn-mini">Save</button></li>
	</li>
</ul>

<script type="text/javascript">
var ccm_themeGridSettings = {};
ccm_themeGridSettings.columnClasses = [];

<? if ($enableThemeGrid) { ?>

	ccm_themeGridSettings.rowStartHTML = '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkRowStartHTML())?>';
	ccm_themeGridSettings.maxColumns = '<?=$controller->getAreaObject()->getAreaGridColumnSpan()?>';
	ccm_themeGridSettings.rowEndHTML = '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkRowEndHTML())?>';
	<? foreach($themeGridFramework->getPageThemeGridFrameworkColumnClasses() as $col => $class) { ?>
		ccm_themeGridSettings.columnClasses[<?=$col?>] = '<?=$class?>';
	<? } ?>
<? } ?>

$(function() {
	ccm_initLayouts();
});
</script>

<div id="ccm-area-layout-active-control-bar" class="ccm-area-layout-control-bar ccm-area-layout-control-bar-<?=$controller->getTask()?>"></div>
