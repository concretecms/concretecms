<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
$options = $this->controller->getOptions();
if ($akSelectAllowMultipleValues) { ?>

	<? foreach($options as $opt) { ?>
		<label class="checkbox"><input type="checkbox" name="<?=$this->field('atSelectOptionID')?>[]" value="<?=$opt->getSelectAttributeOptionID()?>" <? if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) { ?> checked <? } ?> /><?=$opt->getSelectAttributeOptionValue()?></label>
	<? } ?>

<? } else { ?>
	<select name="<?=$this->field('atSelectOptionID')?>[]">
		<option value=""><?=t('** All')?></option>
	<? foreach($options as $opt) { ?>
		<option value="<?=$opt->getSelectAttributeOptionID()?>" <? if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) { ?> selected <? } ?>><?=$opt->getSelectAttributeOptionValue()?></option>	
	<? } ?>
	</select>

<? }