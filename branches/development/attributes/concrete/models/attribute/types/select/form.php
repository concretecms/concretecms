<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?
$options = $this->controller->getOptions();
if ($akSelectAllowMultipleValues) { ?>


<? } else { ?>
	<select name="<?=$this->field('atSelectOptionID')?>[]">
	<? foreach($options as $opt) { ?>
		<option value="<?=$opt->getSelectAttributeOptionID()?>" <? if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) { ?> selected <? } ?>><?=$opt->getSelectAttributeOptionValue()?></option>	
	<? } ?>
	</select>

<? } ?>