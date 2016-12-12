<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$options = $this->controller->getOptions();
if ($akSelectAllowMultipleValues) { ?>

	<? foreach($options as $opt) { ?>
		<div class="checkbox"><label><input type="checkbox" name="<?=$this->field('atSelectOptionID')?>[]" value="<?=$opt->getSelectAttributeOptionID()?>" <? if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) { ?> checked <? } ?> /><?=$opt->getSelectAttributeOptionDisplayValue()?></label></div>
	<? } ?>

<? } else { ?>
	<select class="form-control" name="<?=$this->field('atSelectOptionID')?>[]">
		<option value=""><?=t('** All')?></option>
	<? foreach($options as $opt) { ?>
		<option value="<?=$opt->getSelectAttributeOptionID()?>" <? if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) { ?> selected <? } ?>><?=$opt->getSelectAttributeOptionDisplayValue()?></option>	
	<? } ?>
	</select>

<? }