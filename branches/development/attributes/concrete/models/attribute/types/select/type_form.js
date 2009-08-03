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
		if(!confirm('<?=t("Are you sure you want to remove this value?")?>'))
			return false;  
		$('#akSelectValueWrap_'+val).remove();				
	},
	
	editValue:function(val){ 
		if($('#akSelectValueDisplay_'+val).css('display')!='none'){
			$('#akSelectValueDisplay_'+val).css('display','none');
			$('#akSelectValueEdit_'+val).css('display','block');		
		}else{
			$('#akSelectValueDisplay_'+val).css('display','block');
			$('#akSelectValueEdit_'+val).css('display','none');
			$('#akSelectValueField_'+val).val( $('#akSelectValueStatic_'+val).html() )
		}
	},
	
	changeValue:function(val){ 
		$('#akSelectValueStatic_'+val).html( $('#akSelectValueField_'+val).val() );
		this.editValue(val)
	},
	
	saveNewOption:function(){
		var newValF=$('#akSelectValueFieldNew');
		var myRegxp = /^([a-zA-Z0-9_-]+)$/; 
		var val=newValF.val();
		var val_clean=val.replace(/[^a-zA-Z0-9\-]/g,'');				
		if(val=='' || val=="<?=$defaultNewOptionNm?>"){
			alert("<?=t('Please first type an option.')?>");
			return;
		}		
		var template=document.getElementById('akSelectValueWrapTemplate'); 
		var newRowEl=document.createElement('div');
		newRowEl.innerHTML=template.innerHTML.replace(/template_clean/ig,val_clean).replace(/template/ig,val);
		newRowEl.id="akSelectValueWrap_"+val_clean;
		newRowEl.className='akSelectValueWrap';
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
	
	addEnterClick:function(e,fn){
		var form = $("#ccm-attribute-key-form");
		form.submit(function() {return false;});
		var keyCode = (e.keyCode ? e.keyCode : e.which);
		if(keyCode == 13 && typeof(fn)=='function' ) {
			fn();
			setTimeout(function() { 
				form.unbind();
			}, 100);
		}
		
	}
}
