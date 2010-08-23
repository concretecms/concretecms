<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?
$options = $this->controller->getOptions();
$form = Loader::helper('form');
if ($akSelectAllowMultipleValues) { ?>

	<? foreach($options as $opt) { ?>
		<div>
			<?=$form->checkbox($this->field('atSelectOptionID') . '[]', $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptions)); ?>
			<?=$opt->getSelectAttributeOptionValue()?></div>
	<? } ?>
<? } else { 
	$opts = array('' => t('** None'));
	foreach($options as $opt) { 
		$opts[$opt->getSelectAttributeOptionID()] = $opt->getSelectAttributeOptionValue();
	}
	?>
	<?=$form->select($this->field('atSelectOptionID') . '[]', $opts, $selectedOptions[0]); ?>

<? } 

if ($akSelectAllowOtherValues) { ?>
	<div id="newAttrValueRows<?=$this->attributeKey->getAttributeKeyID()?>" class="newAttrValueRows"></div>
	<div><a href="javascript:void(0)" onclick="ccmAttributeTypeSelectHelper.add(<?=$this->attributeKey->getAttributeKeyID()?>, '<?=$this->field('atSelectNewOption')?>[]')">
		<?=t('Add Another Option')?></a>
	</div>
<? } ?>

<script type="text/javascript">
//<![CDATA[
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
//]]>
</script>