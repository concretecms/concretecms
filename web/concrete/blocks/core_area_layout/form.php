<?
	defined('C5_EXECUTE') or die("Access Denied.");
	$maxColumns = 12;
	$minColumns = 1;
?>

<ul id="ccm-layouts-toolbar">
	<li>
		<label for="columns"><?=t("Columns")?></label>
		<select name="columns" id="columns" <? if ($controller->getTask() == 'edit') { ?> disabled="disabled" <? } ?>>
			<? for ($i = $minColumns; $i <= $maxColumns; $i++) { ?>
				<option value="<?=$i?>" <? if (is_array($columns) && (count($columns) == $i)) { ?> selected <? } ?>><?=$i?></option>
			<? } ?>
		</select>
	</li>
	<li class="ccm-layouts-toolbar-separator"></li>
	<li>
		<label for="columns"><?=t("Spacing")?></label>
		<input name="spacing" id="spacing" style="width: 30px" value="<?=$spacing?>" />
	</li>
	<li class="ccm-layouts-toolbar-separator"></li>
	<li>
		<label style="vertical-align: middle"><?=t("Automatic Widths")?>
		<input style="vertical-align: middle" type="checkbox" value="1" name="isautomated" <? if (!$iscustom) { ?>checked="checked" <? } ?> />
		</label>
	</li>

	<li class="ccm-layouts-toolbar-save ccm-ui">
		<button id="ccm-layouts-cancel-button" type="button" class="btn btn-mini"><?=t("Cancel")?></button>
		<button id="ccm-layouts-save-button" type="button" class="btn btn-primary btn-mini">Save</button></li>
	</li>
</ul>