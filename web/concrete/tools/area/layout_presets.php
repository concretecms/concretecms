<?
defined('C5_EXECUTE') or die("Access Denied.");
if (Loader::helper('validation/token')->validate('layout_presets')) { 





$presets = array('-1' => t('** New'));
	?>

<div class="ccm-ui">

<form method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/area/layout_presets" id="ccm-layout-save-preset-form">
	<?=Loader::helper('validation/token')->output('layout_presets')?>

	<div class="control-group">
		<label class="control-label" for="arLayoutPresetID"><?=t('Preset')?></label>
		<div class="controls">
			<?=Loader::helper('form')->select('arLayoutPresetID', $presets, array('style' => 'width: 300px'))?>
		</div>
	</div>

	<div class="control-group" id="ccm-layout-save-preset-name">
		<label class="control-label" for="arLayoutPresetName"><?=t('Name')?></label>
		<div class="controls">
			<input type="text" name="arLayoutPresetName" id="arLayoutPresetName" class="span4" />
		</div>
	</div>

	<div class="dialog-buttons">
		<button class="btn pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t("Cancel")?></button>
		<button class="btn btn-primary pull-right" onclick="$('#ccm-layout-save-preset-form').submit()"><?=t("Save")?></button>
	</div>
</form>

</div>

<?

}

