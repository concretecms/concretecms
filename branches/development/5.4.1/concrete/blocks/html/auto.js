// JavaScript Document

var HtmlBlock ={

	validate:function(){
			var failed=0; 
			
			/*
			var itemsF=$('#ccm_flickr_itemsToDisplay');
			var itemsV=itemsF.val();
			if( !itemsV || itemsV.length==0 || parseInt(itemsV)<1 ){
				alert(ccm_t('feed-num-items'));
				itemsF.focus();
				failed=1;
			}
			*/
			
			if(failed){
				ccm_isBlockError=1;
				return false;
			}
			return true;
	}
}

ccmValidateBlockForm = function() { return HtmlBlock.validate(); }