// JavaScript Document

var rssDisplayer ={

	validate:function(){
			var failed=0; 
			
			var urlF=$('#ccm_rss_displayer_url');
			var urlV=urlF.val();
			if(!urlV || urlV.length==0 || urlV.indexOf('://')==-1 ){
				alert(ccm_t('feed-address'));
				urlF.focus();
				failed=1;
			}
			
			var itemsF=$('#ccm_rss_displayer_itemsToDisplay');
			var itemsV=itemsF.val();
			if( !itemsV || itemsV.length==0 || parseInt(itemsV)<1 ){
				alert(ccm_t('feed-num-items'));
				itemsF.focus();
				failed=1;
			}
			
			if(failed){
				ccm_isBlockError=1;
				return false;
			}
			return true;
	}
}

ccmValidateBlockForm = function() { return rssDisplayer.validate(); }