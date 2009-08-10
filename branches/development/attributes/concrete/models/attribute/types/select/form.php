<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?
$options = $this->controller->getOptions();
if ($akSelectAllowMultipleValues) { ?>

	<? foreach($options as $opt) { ?>
		<div><input type="checkbox" name="<?=$this->field('atSelectOptionID')?>[]" value="<?=$opt->getSelectAttributeOptionID()?>" <? if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) { ?> checked <? } ?> /><?=$opt->getSelectAttributeOptionValue()?></div>
	<? } ?>

<? } else { ?>
	<select name="<?=$this->field('atSelectOptionID')?>[]">
	<? foreach($options as $opt) { ?>
		<option value="<?=$opt->getSelectAttributeOptionID()?>" <? if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) { ?> selected <? } ?>><?=$opt->getSelectAttributeOptionValue()?></option>	
	<? } ?>
	</select>

<? } 

if ($akSelectAllowOtherValues) { ?>
	<div id="newAttrValueRows<?=$this->attributeKey->getAttributeKeyID()?>" class="newAttrValueRows"></div>
	<div><a href="javascript:void(0)" onclick="ccmAttributeTypeSelectHelper.add(<?=$this->attributeKey->getAttributeKeyID()?>, '<?=$this->field('atSelectNewOption')?>[]')">
		<?=t('Add Another Option')?></a>
	</div>
<? } ?>

<script type="text/javascript">

var ccmAttributeTypeSelectHelper={  
	add:function(akID, field){
		var newRow=document.createElement('div');
		newRow.className='newAttrValueRow';
		newRow.innerHTML='<input name="' + field + '" type="text" value="" /> ';
		newRow.innerHTML+='<a onclick="ccmAttributeTypeSelectHelper.remove(this)" href="javascript:void(0)">[X]</a>';
		$('#newAttrValueRows'+akID).append(newRow);				
	},
	remove:function(a){
		$(a.parentNode).remove();			
	}
}


</script>