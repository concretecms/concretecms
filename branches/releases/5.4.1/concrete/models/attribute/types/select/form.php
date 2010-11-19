<?php  defined('C5_EXECUTE') or die("Access Denied."); 

$options = $this->controller->getOptions();
$form = Loader::helper('form');

if ($akSelectAllowMultipleValues && $akSelectAllowOtherValues) { // display autocomplete form
	$attrKeyID = $this->attributeKey->getAttributeKeyID();
	?>
	<div id="selectedAttrValueRows_<?php  echo $attrKeyID;?>">
		<?php  
		foreach($options as $opt) { 
			if(in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) { ?>
			<div class="existingAttrValue">
				<?php echo $form->hidden($this->field('atSelectOptionID') . '[]', $opt->getSelectAttributeOptionID(), array('style'=>'position:relative;')); ?>
				<?php echo $opt->getSelectAttributeOptionValue()?>
				<a href="javascript:void(0);" onclick="$(this).parent().remove()">x</a>	
			</div>
		<?php 	} 
		} ?>
	</div>
	<?php  
	echo $form->text('newAttrValueRows'.$attrKeyID, array('style'=>'position:relative; width: 200px; z-index: 260;'));
	?>
	<input type="button" value="<?php echo t('Add')?>" onclick="ccmAttributeTypeSelectTagHelper<?php echo $attrKeyID?>.addButtonClick(); return false" />
	<?php 
	
	foreach($options as $op) {
			$opt_values[] = (string) $op;	
	};?>
	<script type="text/javascript">
	//<![CDATA[
	$(function() {
		var availableTags = <?php  echo json_encode($opt_values);?>;
		$("#newAttrValueRows<?php  echo $attrKeyID?>").autocomplete({
			source: availableTags,
			select: function( event, ui ) {
				ccmAttributeTypeSelectTagHelper<?php  echo $attrKeyID?>.add(ui.item.value);
				$(this).val('');
				return false;
			}
		});

		$("#newAttrValueRows<?php  echo $attrKeyID?>").bind("keydown", function(e) {
			if (e.keyCode == 13 || e.keyCode == 188) { // comma or enter
				if($(this).val().length > 0) {
					ccmAttributeTypeSelectTagHelper<?php  echo $attrKeyID?>.add($(this).val());
					$(this).val('');
					$("#newAttrValueRows<?php  echo $this->attributeKey->getAttributeKeyID()?>").autocomplete( "close" );	
				}
				return false;
			}
		});
	});

	var ccmAttributeTypeSelectTagHelper<?php  echo $attrKeyID?>={  
			addButtonClick: function() {
				var valrow = $("input[name=newAttrValueRows<?php echo $attrKeyID?>]");
				ccmAttributeTypeSelectTagHelper<?php  echo $attrKeyID?>.add(valrow.val());
				valrow.val('');
				$("#newAttrValueRows<?php  echo $this->attributeKey->getAttributeKeyID()?>").autocomplete( "close" );
				return false;
			},
			add:function(value){
				var newRow=document.createElement('div');
				newRow.className='newAttrValue';
				newRow.innerHTML='<input name="<?php echo $this->field('atSelectNewOption')?>[]" type="hidden" value="'+value+'" /> ';
				newRow.innerHTML+=value;
				newRow.innerHTML+=' <a onclick="ccmAttributeTypeSelectTagHelper<?php  echo $attrKeyID?>.remove(this)" href="javascript:void(0)">x</a>';
				$('#selectedAttrValueRows_<?php  echo $attrKeyID;?>').append(newRow);				
			},
			remove:function(a){
				$(a.parentNode).remove();			
			}
		}
	//]]>
	</script>
	<?php 
} else {
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
<?php  } ?>