<script>
var ccmAttributesHelper={   
	valuesBoxDisabled:function(typeSelect){
		var attrValsInterface=document.getElementById('attributeValuesInterface')
		var requiredVals=document.getElementById('reqValues');
		var allowOther=document.getElementById('allowOtherValuesWrap');
		var offMsg=document.getElementById('attributeValuesOffMsg');
		if (typeSelect.value == 'SELECT' || typeSelect.value == 'SELECT_MULTIPLE') {
			attrValsInterface.style.display='block';
			requiredVals.style.display='inline'; 
			if(allowOther) allowOther.style.display='block';
			offMsg.style.display='none';			
		} else {  
			requiredVals.style.display='none'; 
			attrValsInterface.style.display='none';
			if(allowOther) allowOther.style.display='none';
			offMsg.style.display='block'; 
		}	
	},  
	
	deleteValue:function(val){
		if(!confirm('<?php echo t("Are you sure you want to remove this value?")?>'))
			return false;  
		$('#akValueWrap_'+val).remove();				
	},
	
	editValue:function(val){ 
		if($('#akValueDisplay_'+val).css('display')!='none'){
			$('#akValueDisplay_'+val).css('display','none');
			$('#akValueEdit_'+val).css('display','block');		
		}else{
			$('#akValueDisplay_'+val).css('display','block');
			$('#akValueEdit_'+val).css('display','none');
			$('#akValueField_'+val).val( $('#akValueStatic_'+val).html() )
		}
	},
	
	changeValue:function(val){ 
		$('#akValueStatic_'+val).html( $('#akValueField_'+val).val() );
		this.editValue(val)
	},
	
	saveNewOption:function(){
		var newValF=$('#akValueFieldNew');
		var myRegxp = /^([a-zA-Z0-9_-]+)$/; 
		var val=newValF.val();
		var val_clean=val.replace(/[^a-zA-Z0-9\-]/g,'');				
		if(val=='' || val=="<?php echo $defaultNewOptionNm?>"){
			alert("<?php echo t('Please first type an option.')?>");
			return;
		}		
		var template=document.getElementById('akValueWrapTemplate'); 
		var newRowEl=document.createElement('div');
		newRowEl.innerHTML=template.innerHTML.replace(/template_clean/ig,val_clean).replace(/template/ig,val);
		newRowEl.id="akValueWrap_"+val_clean;
		newRowEl.className='akValueWrap';
		$('#attributeValuesWrap').append(newRowEl);				
		newValF.val(''); 
	},
	
	clrInitTxt:function(field,initText,removeClass,blurred){
		if(blurred && field.value==''){
			field.value=initText;
			$(field).addClass(removeClass);
			return;	
		}
		if(field.value==initText) field.value='';
		if($(field).hasClass(removeClass)) $(field).removeClass(removeClass);
	},
	
	doSubmit: true,
	
	addEnterClick:function(e,fn){
		ccmAttributesHelper.doSubmit = false;
		var keyCode = (e.keyCode ? e.keyCode : e.which);
		if(keyCode == 13 && typeof(fn)=='function' ) {
			fn();
			setTimeout(function() { ccmAttributesHelper.doSubmit = true; }, 100);
		}
		
	}
}


</script>

<style>
#attributeValuesWrap{margin-top:4px; width:400px}
#attributeValuesWrap .akValueWrap{ margin-bottom:2px; border:1px solid #eee; padding:2px;}
#attributeValuesWrap .akValueWrap:hover{border:1px solid #ddd; }
#attributeValuesWrap .akValueWrap .leftCol{float:left; }
#attributeValuesWrap .akValueWrap .rightCol{float:right; text-align:right }

#addAttributeValueWrap{ margin-top:8px }
#addAttributeValueWrap input.faint{ color:#999 }
#addAttributeValueWrap a{cursor:pointer}

#allowOtherValuesWrap{margin-top:16px}
</style>

<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
	<td class="subheader"><?php echo t('Type')?> <span class="required">*</span></td>
	<?php  if(!$noSearchable){?>
	<td class="subheader"><?php echo t('Searchable?')?> <span class="required">*</span></td>
	<?php  } ?>
</tr>	
<tr>
	<td style="width: <?php echo ($noSearchable)?'50':'33'?>%"><input type="text" name="akHandle" style="width: 100%" value="<?php echo $akHandle?>" /></td>
	<td style="width: <?php echo ($noSearchable)?'50':'33'?>%"><select name="akType" style="width: 100%" onchange="ccmAttributesHelper.valuesBoxDisabled(this)">
		<option value="TEXT"<?php  if ($akType == 'TEXT') { ?> selected<?php  } ?>><?php echo t('Text Box')?></option>
		<option value="BOOLEAN"<?php  if ($akType == 'BOOLEAN') { ?> selected<?php  } ?>><?php echo t('Check Box')?></option>
		<option value="SELECT"<?php  if ($akType == 'SELECT') { ?> selected<?php  } ?>><?php echo t('Select Menu')?></option>
		<option value="SELECT_MULTIPLE"<?php  if ($akType == 'SELECT_MULTIPLE') { ?> selected<?php  } ?>><?php echo t('Select Multiple')?></option>
		<option value="NUMBER"<?php  if ($akType == 'NUMBER') { ?> selected<?php  } ?>><?php echo t('Number')?></option>
		<option value="DATE"<?php  if ($akType == 'DATE') { ?> selected <?php  } ?>><?php echo t('Date')?></option>
		<?php  if ($attributeType == 'page') { ?><option value="IMAGE_FILE"<?php  if ($akType == 'IMAGE_FILE') { ?> selected <?php  } ?>><?php echo t('Image/File')?></option><?php  } ?>
		<option value="RATING"<?php  if ($akType == 'RATING') { ?> selected <?php  } ?>><?php echo t('Rating')?></option>
	</select></td>
	<?php  if(!$noSearchable){?>
	<td style="width: 33%"><input type="checkbox" name="akSearchable" style="vertical-align: middle" <?php  if ($akSearchable) { ?> checked <?php  } ?> /> <?php echo t('Yes, include this field in the search index.')?></td>
	<?php  } ?>
</tr>
<tr>
	<td class="subheader" colspan="3"><?php echo t('Name')?> <span class="required">*</span></td>
</tr>
<tr>
	<td colspan="3"><input type="text" name="akName" style="width: 100%" value="<?php echo $akName?>" /></td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?php echo t('Values')?> <span class="required" id="reqValues" <?php  if ($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE') { ?> style="display: none"<?php  } ?>>*</span></td>
</tr>
<tr>
	<td colspan="3"> 		
	<?php  Loader::element('dashboard/attribute_values', array('attributeType' => $attributeType, 'akValues'=>$akValues, 'akType'=>$akType, 'akAllowOtherValues'=>$akAllowOtherValues, 'defaultNewOptionNm'=>$defaultNewOptionNm) ); ?>
	</td>
</tr>
<tr>
	<td colspan="3" class="header">
	<a href="<?php echo $this->url($cancelURL)?>" class="ccm-button-left"><span><?php echo t('Cancel')?></span></a>
	<a href="javascript:void(0)" onclick="$('#<?php echo $formId?>').get(0).submit()" class="ccm-button-right"><span><?php echo $submitBtnTxt?></span></a>
	</td>
</tr>
</table>
</div>


<?php  /*
<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?php echo t('Handle')?> <span class="required">*</span></td>
	<td class="subheader"><?php echo t('Type')?> <span class="required">*</span></td>
	<td class="subheader"><?php echo t('Searchable?')?> <span class="required">*</span></td>
</tr>	
<tr>
	<td style="width: 33%"><input type="text" name="akHandle" style="width: 100%" value="<?php echo $_POST['akHandle']?>" /></td>
	<td style="width: 33%"><select name="akType" style="width: 100%" onchange="ccmAttributesHelper.valuesBoxDisabled(this)">
		<option value="TEXT"<?php  if ($_POST['akType'] == 'TEXT') { ?> selected<?php  } ?>><?php echo t('Text Box')?></option>
		<option value="BOOLEAN"<?php  if ($_POST['akType'] == 'BOOLEAN') { ?> selected<?php  } ?>><?php echo t('Check Box')?></option>
		<option value="SELECT"<?php  if ($_POST['akType'] == 'SELECT') { ?> selected<?php  } ?>><?php echo t('Select Menu')?></option>
		<option value="SELECT_MULTIPLE"<?php  if ($_POST['akType'] == 'SELECT_MULTIPLE') { ?> selected<?php  } ?>><?php echo t('Select Multiple')?></option>
		<option value="DATE"<?php  if ($_POST['akType'] == 'DATE') { ?> selected <?php  } ?>><?php echo t('Date')?></option>
		<option value="IMAGE_FILE"<?php  if ($_POST['akType'] == 'IMAGE_FILE') { ?> selected <?php  } ?>><?php echo t('Image/File')?></option>
	</select></td>
	<td style="width: 33%"><input type="checkbox" name="akSearchable" style="vertical-align: middle" <?php  if ($_POST['akSearchable']) { ?> checked <?php  } ?> /> <?php echo t('Yes, include this field in the search index.')?></td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?php echo t('Name')?> <span class="required">*</span></td>
</tr>
<tr>
	<td colspan="3"><input type="text" name="akName" style="width: 100%" value="<?php echo $_POST['akName']?>" /></td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?php echo t('Values')?> <span class="required" id="reqValues" <?php  if ($_POST['akType'] != 'SELECT' && $akType != 'SELECT_MULTIPLE') { ?> style="display: none"<?php  } ?>>*</span></td>
</tr>
<tr>
	<td colspan="3"> 			
		<?php  Loader::element('collection_attribute_values', array('akValues'=>$_POST['akValues'], 'akType'=>$akType, 'akAllowOtherValues'=>$_POST['akValues'], 'defaultNewOptionNm'=>$defaultNewOptionNm) ); ?>
	</td>
</tr>
<tr>
	<td colspan="3" class="header">

	<a href="<?php echo $this->url('/dashboard/pages/types')?>" class="ccm-button-left"><span><?php echo t('Cancel')?></span></a>
	<a href="javascript:void(0)" onclick="$('#ccm-add-attribute').get(0).submit()" class="ccm-button-right"><span><?php echo t('Add')?></span></a>
	
	</td>
</tr>
</table>
</div>
*/ ?>