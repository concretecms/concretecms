<?
defined('C5_EXECUTE') or die("Access Denied.");

$cp = FilePermissions::getGlobal();
if (!$cp->canAccessFileManager()) {
	die(t("Unable to access the file manager."));
}

Loader::model('file_list');
Loader::model('file_set');

$fileList = new FileList();
$fileList->enableStickySearchRequest();
$req = $fileList->getSearchRequest();
$form = Loader::helper('form');

$s1 = FileSet::getMySets();
if (count($s1) > 0) { ?>
		<div class="span4" style="width: 280px">
	<?=$form->label('fsID', t('In Set(s)'))?>
	<div class="input">
		<select multiple name="fsID[]" class="chosen-select">
			<? foreach($s1 as $s) { ?>
				<option value="<?=$s->getFileSetID()?>"><?=$s->getFileSetName()?></option>
			<? } ?>
			<optgroup label="<?=t('Other')?>">
				<option value="-1" <? if (is_array($req['fsID']) && in_array(-1, $req['fsID'])) { ?> selected="selected" <? } ?>><?=t('Files in no sets.')?></option>
			</optgroup>
		</select>
	</div>
</div>
<? } ?>