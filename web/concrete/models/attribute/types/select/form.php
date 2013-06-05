<?php defined('C5_EXECUTE') or die("Access Denied."); 

$form = Loader::helper('form');
$json = Loader::helper('json');
if ($akSelectAllowMultipleValues && $akSelectAllowOtherValues) { // display autocomplete form
	$attrKeyID = $this->attributeKey->getAttributeKeyID();
	?>
	
<div class="ccm-attribute-type-select-autocomplete">

	<div id="selectedAttrValueRows_<?php echo $attrKeyID;?>">
		<?php 
		foreach($selectedOptions as $optID) { 
			$opt = SelectAttributeTypeOption::getByID($optID);
			
			?>
			<div class="existingAttrValue">
				<?=$form->hidden($this->field('atSelectOptionID') . '[]', $opt->getSelectAttributeOptionID(), array('style'=>'position:relative;')); ?>
				<?=$opt->getSelectAttributeOptionValue()?>
				<a href="javascript:void(0);" onclick="$(this).parent().remove()">x</a>	
			</div>
		<? } 
		
		// now we get items from the post
		$vals = $this->post('atSelectNewOption');
		if (is_array($vals)) {
			foreach($vals as $v) { ?>
				<div class="newAttrValue">
					<?=$form->hidden($this->field('atSelectNewOption') . '[]', $v)?>
					<?=$v?>
					<a onclick="ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.remove(this)" href="javascript:void(0)">x</a>
				</div>
			<? 
			}
		}
		
		?>
	</div>
	<span style="position: relative">
	
	<?php 
	echo $form->text('newAttrValueRows'.$attrKeyID, array('class' => 'ccm-attribute-type-select-autocomplete-text', 'style'=>'position:relative; width: 200px'));
	?>
	<input type="button" class="btn ccm-input-button" value="<?=t('Add')?>" onclick="ccmAttributeTypeSelectTagHelper<?=$attrKeyID?>.addButtonClick(); return false" />
	</span>
</div>

	<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var availableTags = <?=$json->encode($opt_values);?>;
		$("#newAttrValueRows<?php echo $attrKeyID?>").autocomplete({
			source: "<?=$this->action('load_autocomplete_values')?>",
			select: function( event, ui ) {
				ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.add(ui.item.value);
				$(this).val('');
				return false;
			}
		});

		$("#newAttrValueRows<?php echo $attrKeyID?>").bind("keydown", function(e) {
			if (e.keyCode == 13) { // comma or enter
				if($(this).val().length > 0) {
					ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.add($(this).val());
					$(this).val('');
					$("#newAttrValueRows<?php echo $this->attributeKey->getAttributeKeyID()?>").autocomplete( "close" );	
				}
				return false;
			}
		});
	});

	var ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>={  
			addButtonClick: function() {
				var valrow = $("input[name=newAttrValueRows<?=$attrKeyID?>]");
				ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.add(valrow.val());
				valrow.val('');
				$("#newAttrValueRows<?php echo $this->attributeKey->getAttributeKeyID()?>").autocomplete( "close" );
				return false;
			},
			add:function(value){
				var newRow=document.createElement('div');
				newRow.className='newAttrValue';
				newRow.innerHTML='<input name="<?=$this->field('atSelectNewOption')?>[]" type="hidden" value="'+value+'" /> ';
				newRow.innerHTML+=value;
				newRow.innerHTML+=' <a onclick="ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.remove(this)" href="javascript:void(0)">x</a>';
				$('#selectedAttrValueRows_<?php echo $attrKeyID;?>').append(newRow);				
			},
			remove:function(a){
				$(a.parentNode).remove();			
			}
		}
	//]]>
	</script>
	<?php
} else {

	$options = $this->controller->getOptions();

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
<?php } ?>