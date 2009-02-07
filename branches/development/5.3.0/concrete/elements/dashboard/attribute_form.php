<script>
var ccmAttributesHelper={   
	valuesBoxDisabled:function(typeSelect){
		if (typeSelect.value == 'SELECT' || typeSelect.value == 'SELECT_MULTIPLE') {
			document.getElementById('attributeValuesInterface').style.display='block';
			document.getElementById('reqValues').style.display='inline'; 
			document.getElementById('allowOtherValuesWrap').style.display='block';
			document.getElementById('attributeValuesOffMsg').style.display='none';			
		} else {  
			document.getElementById('reqValues').style.display='none'; 
			document.getElementById('attributeValuesInterface').style.display='none';
			document.getElementById('allowOtherValuesWrap').style.display='none';
			document.getElementById('attributeValuesOffMsg').style.display='block'; 
		}	
	},  
	
	deleteValue:function(val){
		if(!confirm('<?=t("Are you sure you want to remove this value?")?>'))
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
		if(val=='' || val=="<?=$defaultNewOptionNm?>"){
			alert("<?=t('Please first type an option.')?>");
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

#allowOtherValuesWrap{margin-top:16px}
</style>

<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?=t('Handle')?> <span class="required">*</span></td>
	<td class="subheader"><?=t('Type')?> <span class="required">*</span></td>
	<? if(!$noSearchable){?>
	<td class="subheader"><?=t('Searchable?')?> <span class="required">*</span></td>
	<? } ?>
</tr>	
<tr>
	<td style="width: <?=($noSearchable)?'50':'33'?>%"><input type="text" name="akHandle" style="width: 100%" value="<?=$akHandle?>" /></td>
	<td style="width: <?=($noSearchable)?'50':'33'?>%"><select name="akType" style="width: 100%" onchange="ccmAttributesHelper.valuesBoxDisabled(this)">
		<option value="TEXT"<? if ($akType == 'TEXT') { ?> selected<? } ?>><?=t('Text Box')?></option>
		<option value="BOOLEAN"<? if ($akType == 'BOOLEAN') { ?> selected<? } ?>><?=t('Check Box')?></option>
		<option value="SELECT"<? if ($akType == 'SELECT') { ?> selected<? } ?>><?=t('Select Menu')?></option>
		<option value="SELECT_MULTIPLE"<? if ($akType == 'SELECT_MULTIPLE') { ?> selected<? } ?>><?=t('Select Multiple')?></option>
		<option value="NUMBER"<? if ($akType == 'NUMBER') { ?> selected<? } ?>><?=t('Number')?></option>
		<option value="DATE"<? if ($akType == 'DATE') { ?> selected <? } ?>><?=t('Date')?></option>
		<option value="IMAGE_FILE"<? if ($akType == 'IMAGE_FILE') { ?> selected <? } ?>><?=t('Image/File')?></option>
	</select></td>
	<? if(!$noSearchable){?>
	<td style="width: 33%"><input type="checkbox" name="akSearchable" style="vertical-align: middle" <? if ($akSearchable) { ?> checked <? } ?> /> <?=t('Yes, include this field in the search index.')?></td>
	<? } ?>
</tr>
<tr>
	<td class="subheader" colspan="3"><?=t('Name')?> <span class="required">*</span></td>
</tr>
<tr>
	<td colspan="3"><input type="text" name="akName" style="width: 100%" value="<?=$akName?>" /></td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?=t('Values')?> <span class="required" id="reqValues" <? if ($akType != 'SELECT' && $akType != 'SELECT_MULTIPLE') { ?> style="display: none"<? } ?>>*</span></td>
</tr>
<tr>
	<td colspan="3"> 		
	<? Loader::element('collection_attribute_values', array('akValues'=>$akValues, 'akType'=>$akType, 'akAllowOtherValues'=>$akAllowOtherValues, 'defaultNewOptionNm'=>$defaultNewOptionNm) ); ?>
	</td>
</tr>
<tr>
	<td colspan="3" class="header">
	<a href="<?=$this->url($cancelURL)?>" class="ccm-button-left"><span><?=t('Cancel')?></span></a>
	<a href="javascript:void(0)" onclick="$('#<?=$formId?>').get(0).submit()" class="ccm-button-right"><span><?=$submitBtnTxt?></span></a>
	</td>
</tr>
</table>
</div>


<? /*
<div style="margin:0px; padding:0px; width:100%; height:auto" >	
<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
<tr>
	<td class="subheader"><?=t('Handle')?> <span class="required">*</span></td>
	<td class="subheader"><?=t('Type')?> <span class="required">*</span></td>
	<td class="subheader"><?=t('Searchable?')?> <span class="required">*</span></td>
</tr>	
<tr>
	<td style="width: 33%"><input type="text" name="akHandle" style="width: 100%" value="<?=$_POST['akHandle']?>" /></td>
	<td style="width: 33%"><select name="akType" style="width: 100%" onchange="ccmAttributesHelper.valuesBoxDisabled(this)">
		<option value="TEXT"<? if ($_POST['akType'] == 'TEXT') { ?> selected<? } ?>><?=t('Text Box')?></option>
		<option value="BOOLEAN"<? if ($_POST['akType'] == 'BOOLEAN') { ?> selected<? } ?>><?=t('Check Box')?></option>
		<option value="SELECT"<? if ($_POST['akType'] == 'SELECT') { ?> selected<? } ?>><?=t('Select Menu')?></option>
		<option value="SELECT_MULTIPLE"<? if ($_POST['akType'] == 'SELECT_MULTIPLE') { ?> selected<? } ?>><?=t('Select Multiple')?></option>
		<option value="DATE"<? if ($_POST['akType'] == 'DATE') { ?> selected <? } ?>><?=t('Date')?></option>
		<option value="IMAGE_FILE"<? if ($_POST['akType'] == 'IMAGE_FILE') { ?> selected <? } ?>><?=t('Image/File')?></option>
	</select></td>
	<td style="width: 33%"><input type="checkbox" name="akSearchable" style="vertical-align: middle" <? if ($_POST['akSearchable']) { ?> checked <? } ?> /> <?=t('Yes, include this field in the search index.')?></td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?=t('Name')?> <span class="required">*</span></td>
</tr>
<tr>
	<td colspan="3"><input type="text" name="akName" style="width: 100%" value="<?=$_POST['akName']?>" /></td>
</tr>
<tr>
	<td class="subheader" colspan="3"><?=t('Values')?> <span class="required" id="reqValues" <? if ($_POST['akType'] != 'SELECT' && $akType != 'SELECT_MULTIPLE') { ?> style="display: none"<? } ?>>*</span></td>
</tr>
<tr>
	<td colspan="3"> 			
		<? Loader::element('collection_attribute_values', array('akValues'=>$_POST['akValues'], 'akType'=>$akType, 'akAllowOtherValues'=>$_POST['akValues'], 'defaultNewOptionNm'=>$defaultNewOptionNm) ); ?>
	</td>
</tr>
<tr>
	<td colspan="3" class="header">

	<a href="<?=$this->url('/dashboard/pages/types')?>" class="ccm-button-left"><span><?=t('Cancel')?></span></a>
	<a href="javascript:void(0)" onclick="$('#ccm-add-attribute').get(0).submit()" class="ccm-button-right"><span><?=t('Add')?></span></a>
	
	</td>
</tr>
</table>
</div>
*/ ?>