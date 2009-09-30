<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php 
$options = $this->controller->getOptions();
$form = Loader::helper('form');
if ($akSelectAllowMultipleValues) { ?>

	<?php  foreach($options as $opt) { ?>
		<div>
			<?php echo $form->checkbox($this->field('atSelectOptionID') . '[]', $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptions)); ?>
			<?php echo $opt->getSelectAttributeOptionValue()?></div>
			
	<?php  } ?>

<?php  } else { 
	$opts = array('' => t('** None'));
	foreach($options as $opt) { 
		$opts[$opt->getSelectAttributeOptionID()] = $opt->getSelectAttributeOptionValue();
	}
	?>
	<?php echo $form->select($this->field('atSelectOptionID') . '[]', $opts, $selectedOptions[0]); ?>

<?php  } 

if ($akSelectAllowOtherValues) { ?>
	<div id="newAttrValueRows<?php echo $this->attributeKey->getAttributeKeyID()?>" class="newAttrValueRows"></div>
	<div><a href="javascript:void(0)" onclick="ccmAttributeTypeSelectHelper.add(<?php echo $this->attributeKey->getAttributeKeyID()?>, '<?php echo $this->field('atSelectNewOption')?>[]')">
		<?php echo t('Add Another Option')?></a>
	</div>
<?php  } ?>

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