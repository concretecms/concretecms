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

if (Loader::helper('validation/token')->validate('layout_presets')) { 

	if ($_POST['submit']) {
		
		$cnt = new CoreAreaLayoutBlockController();
		$arLayout = $cnt->addFromPost($_POST);
		if (is_object($arLayout)) {
			if ($_POST['arLayoutPresetID'] == '-1') {
				$preset = AreaLayoutPreset::add($arLayout, $_POST['arLayoutPresetName']);
			} else {
				$existingPreset = AreaLayoutPreset::getByID($_POST['arLayoutPresetID']);
				if (is_object($existingPreset)) {
					$existingPreset->updateAreaLayoutObject($arLayout);
				}
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

	<div class="control-group">
		<label class="control-label" for="arLayoutPresetID"><?=t('Save as Preset')?></label>
		<div class="controls">
			<?=Loader::helper('form')->select('arLayoutPresetID', $presets, array('style' => 'width: 300px'))?>
		</div>
	</div>

	<div class="control-group" id="ccm-layout-save-preset-name">
		<label class="control-label" for="arLayoutPresetName"><?=t('New Preset Name')?></label>
		<div class="controls">
			<input type="text" name="arLayoutPresetName" id="arLayoutPresetName" class="span4" />
		</div>
	</div>

	<div class="alert alert-warning" id="ccm-layout-save-preset-override"><?=t('Note: this will override the selected preset with the new preset. It will not update any layouts already in use.')?></div>

	<div class="dialog-buttons">
		<button class="btn pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t("Cancel")?></button>
		<button class="btn btn-primary pull-right" onclick="$('#ccm-layout-save-preset-form').submit()"><?=t("Save")?></button>
	</div>
</form>

</div>

<?

}

