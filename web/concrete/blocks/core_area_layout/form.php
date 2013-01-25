<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$minColumns = 1;
?>

<ul id="ccm-layouts-toolbar">
	<li data-grid-form-view="choosetype">
		<label for="useThemeGrid"><?=t("Grid Type")?></label>
		<select name="useThemeGrid" id="useThemeGrid" style="width: auto !important">
			<option value="1"><?=$themeGridName?></option>
			<option value="0"><?=t('Free-Form Layout')?></option>
		</select>
	</li>
	<li data-grid-form-view="choosetype" class="ccm-layouts-toolbar-separator"></li>
	<li data-grid-form-view="themegrid" class="ccm-page-theme-grid-framework">
		<label for="themeGridColumns"><?=t("Columns")?></label>
		
		<select name="themeGridColumns" id="themeGridColumns">
			<? for ($i = $minColumns; $i <= $themeGridMaxColumns; $i++) { ?>
				<option value="<?=$i?>" <? if ($columnsNum == $i) { ?> selected <? } ?>><?=$i?></option>
			<? } ?>
		</select>
	</li>
	<li data-grid-form-view="custom">
		<label for="columns"><?=t("Columns")?></label>
		<select name="columns" id="columns">
			<? for ($i = $minColumns; $i <= $maxColumns; $i++) { ?>
				<option value="<?=$i?>" <? if ($columnsNum == $i) { ?> selected <? } ?>><?=$i?></option>
			<? } ?>
		</select>
	</li>
	<li data-grid-form-view="custom" class="ccm-layouts-toolbar-separator"></li>
	<li data-grid-form-view="custom" >
		<label for="columns"><?=t("Spacing")?></label>
		<input name="spacing" id="spacing" type="text" style="width: 20px" value="<?=$spacing?>" />
	</li>
	<li data-grid-form-view="custom" class="ccm-layouts-toolbar-separator"></li>
	<li data-grid-form-view="custom" >
		<label><?=t("Automatic Widths")?></label>
		<input type="checkbox" value="1" name="isautomated" <? if (!$iscustom) { ?>checked="checked" <? } ?> />
	</li>

	<li class="ccm-layouts-toolbar-save ccm-ui">
		<button id="ccm-layouts-cancel-button" type="button" class="btn btn-mini"><?=t("Cancel")?></button>
		<div class="btn-group" id="ccm-layouts-save-button-group">
		  <button class="btn btn-primary btn-mini" type="button" id="ccm-layouts-save-button"><? if ($controller->getTask() == 'add') { ?><?=t('Add Layout')?><? } else { ?><?=t('Update Layout')?><? } ?></button>
		  <a class="btn btn-primary btn-mini dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
		  <ul class="dropdown-menu pull-right">
		    <li><a href="javascript:void(0)" onclick="CCMLayout.launchPresets()"><i class="icon-pencil"></i> <?=t("Save Settings as Preset")?></a></li>
		  </ul>
		</div>
	</li>

	<? if ($controller->getTask() == 'add') { ?>
		<input name="arLayoutMaxColumns" type="hidden" value="<?=$controller->getAreaObject()->getAreaGridColumnSpan()?>" />
	<? } ?>
</ul>

<script type="text/javascript">
<? 

if ($controller->getTask() == 'edit') {
	$editing = 'true';
} else {
	$editing = 'false';
}

if ($enableThemeGrid && $controller->getTask() == 'add') {
	$formview = 'choosetype';
} else if ($enableThemeGrid) {
	$formview = 'themegrid';
} else {
	$formview = 'custom';
}



?>

$(function() {
	$('#ccm-layouts-edit-mode').ccmlayout({
		'editing': <?=$editing?>,
		'formview': '<?=$formview?>',
		<? if ($enableThemeGrid) { ?>
		'rowstart':  '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkRowStartHTML())?>',
		'rowend': '<?=addslashes($themeGridFramework->getPageThemeGridFrameworkRowEndHTML())?>',
		<? if ($controller->getTask() == 'add') { ?>
		'maxcolumns': '<?=$controller->getAreaObject()->getAreaGridColumnSpan()?>',
		<? } else { ?>
		'maxcolumns': '<?=$themeGridMaxColumns?>',
		<? } ?>
		'gridColumnClasses': [
			<? $classes = $themeGridFramework->getPageThemeGridFrameworkColumnClasses();?>
			<? for ($i = 0; $i < count($classes); $i++) { 
				$class = $classes[$i];?>
				'<?=$class?>' <? if (($i + 1) < count($classes)) { ?>, <? } ?>

			<? } ?>
		]
		<? } ?>
	});
});


</script>

<div id="ccm-area-layout-active-control-bar" class="ccm-area-layout-control-bar ccm-area-layout-control-bar-<?=$controller->getTask()?>"></div>
