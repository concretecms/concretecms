<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">

<form method="post" action="<?=$controller->action('submit', $arLayout->getAreaLayoutID())?>" data-dialog-form="save-area-layout-presets" >
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
	<button class="btn pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
	<button type="button" data-dialog-action="submit" class="btn btn-success pull-right"><?=t('Save Preset')?></button>
	</div>


	<? } ?>
</form>

</div>