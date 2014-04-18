<?
defined('C5_EXECUTE') or die("Access Denied.");

if ($_GET['task'] == 'get_area_layout' && Loader::helper('validation/token')->validate()) {
	$existingPreset = AreaLayoutPreset::getByID($_GET['arLayoutPresetID']);
	if (is_object($existingPreset)) {
		$r = new stdClass;
		$arLayout = $existingPreset->getAreaLayoutObject();
		$r->arLayout = $arLayout;
		$r->arLayoutColumns = $arLayout->getAreaLayoutColumns();
		print Loader::helper('json')->encode($r);
		exit;
	}
}

$pk = PermissionKey::getByHandle('manage_layout_presets');
if (!$pk->validate()) {
	die(t('Access Denied'));
}



if (Loader::helper('validation/token')->validate('layout_presets')) { 

	if ($_REQUEST['task'] == 'submit_delete') {
		$preset = AreaLayoutPreset::getByID($_REQUEST['arLayoutPresetID']);
		if (is_object($preset)) {
			$preset->delete();
		}
	}

	if ($_REQUEST['task'] == 'get_list_json') {
		print Loader::helper('json')->encode(AreaLayoutPreset::getList());
		exit;
	}

	$arLayout = AreaLayout::getByID($_REQUEST['arLayoutID']);
	if (!is_object($arLayout)) {
		die(t('Invalid layout object.'));
	}

	if ($_POST['submit']) {
		
		if ($_POST['arLayoutPresetID'] == '-1') {
			$preset = AreaLayoutPreset::add($arLayout, $_POST['arLayoutPresetName']);
		} else {
			$existingPreset = AreaLayoutPreset::getByID($_POST['arLayoutPresetID']);
			if (is_object($existingPreset)) {
				$existingPreset->updateAreaLayoutObject($arLayout);
			}
		}

		print Loader::helper('json')->encode(AreaLayoutPreset::getList());
		exit;
	}

	$presetlist = AreaLayoutPreset::getList();
	$presets = array();
	$presets['-1'] = t('** New');
	foreach($presetlist as $preset) {
		$presets[$preset->getAreaLayoutPresetID()] = $preset->getAreaLayoutPresetName();
	}

?>

<div class="ccm-ui">

<form method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/area/layout_presets" id="ccm-layout-save-preset-form">
	<?=Loader::helper('validation/token')->output('layout_presets')?>
	<input type="hidden" value="<?=Loader::helper('security')->sanitizeInt($_REQUEST['arLayoutID'])?>" name="arLayoutID" />

	<? if ($_REQUEST['task'] == 'delete' || $_REQUEST['task'] == 'submit_delete') { ?>

	<? if (count($presetlist) > 0) { ?>
		<div class="alert alert-info"><?=t("Deleting a preset will not affect any layouts that have used that preset in the past.")?></div>

		<table class="table table-striped table-bordered">
		<? foreach($presetlist as $preset) { ?>
		<tr>
			<td style="width: 100%"><?=$preset->getAreaLayoutPresetName()?></td>
			<td><a href="javascript:void(0)" class="delete-area-layout-preset" data-area-layout-preset-id="<?=$preset->getAreaLayoutPresetID()?>"><i class="icon-trash"></i></a></td>
		</tr>
		<? } ?>
		</table>

	<? } else { ?>
		<p>You have no presets.</p>
	<? } ?>

	<? } else { ?>


	<div class="control-group">
		<label class="control-label" for="arLayoutPresetID"><?=t('Save as Preset')?></label>
		<div class="controls">
			<?=Loader::helper('form')->select('arLayoutPresetID', $presets, array('class' => 'span3'))?>
		</div>
	</div>

	<div class="control-group" id="ccm-layout-save-preset-name">
		<label class="control-label" for="arLayoutPresetName"><?=t('New Preset Name')?></label>
		<div class="controls">
			<input type="text" name="arLayoutPresetName" id="arLayoutPresetName" class="span3" />
		</div>
	</div>

	<div class="alert alert-warning" id="ccm-layout-save-preset-override"><?=t('Note: this will override the selected preset with the new preset. It will not update any layouts already in use.')?></div>

	<div class="dialog-buttons">
		<button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t("Cancel")?></button>
		<button class="btn btn-primary pull-right" onclick="$('#ccm-layout-save-preset-form').submit()"><?=t("Save")?></button>
	</div>

	<? } ?>
</form>

</div>

<?

}

