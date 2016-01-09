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
		if(confirm(ccmi18n.deleteAttributeValue)) {
			$('#akSelectValueWrap_'+val).remove();				
		}
	},
	
	editValue:function(val){ 
		if($('#akSelectValueDisplay_'+val).css('display')!='none'){
			$('#akSelectValueDisplay_'+val).css('display','none');
			$('#akSelectValueEdit_'+val).css('display','block').find('input[type="text"]').focus();	
		}else{
			$('#akSelectValueDisplay_'+val).css('display','block');
			$('#akSelectValueEdit_'+val).css('display','none');
			var txtValue =  $('#akSelectValueStatic_'+val).html();
			$('#akSelectValueField_'+val).val( $('<div/>').html(txtValue).text());
		}
	},
	
	changeValue:function(val){ 
		var txtValue = $('<div/>').text($('#akSelectValueField_'+val).val()).html();		
		$('#akSelectValueStatic_'+val).html( txtValue );
		this.editValue(val)
	},
	
	makeSortable: function() {
		$("div#attributeValuesWrap").sortable({
			cursor: 'move',
			opacity: 0.5
		});
	},
	
	saveNewOption:function(){
		var newValF=$('#akSelectValueFieldNew');
		var val = $('<div/>').text(newValF.val()).html();
		if(val=='') {
			return;
		}
		var ts = 't' + new Date().getTime();
		var template=document.getElementById('akSelectValueWrapTemplate'); 
		var newRowEl=document.createElement('div');
		newRowEl.innerHTML=template.innerHTML.replace(/template_clean/ig,ts).replace(/template/ig,val);
		newRowEl.id="akSelectValueWrap_"+ts;
		newRowEl.className='akSelectValueWrap akSelectValueWrapSortable';
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
	
	keydownHandler:function(event){
		var form = $("#ccm-attribute-key-form");
		switch (event.keyCode) {
			case 13: // enter
				event.preventDefault();
				if (event.currentTarget.id === 'akSelectValueFieldNew') { // if the event originates from the "add" input field, create the option
					ccmAttributesHelper.saveNewOption();
				} else { // otherwise just fire the existing option save
					ccmAttributesHelper.changeValue(event.currentTarget.getAttribute('data-select-value-id'));
				}
				break;
			case 38: // arrow up
			case 40: // arrow down
				ccmAttributesHelper.changeValue(event.currentTarget.getAttribute('data-select-value-id'));
				var find = (event.keyCode === 38) ? 'prev' : 'next';
				var $target = $(event.currentTarget).closest('.akSelectValueWrap')[find]();
				if ($target.length) {
					$target.find('.leftCol').click();
				} else if (find === 'next') {
					$('#akSelectValueFieldNew').focus();
				}
				break;
		}
	},

	// legacy stub method
	addEnterClick:function(){
		ccmAttributesHelper.keydownHandler.apply(this, arguments);
	}

}