// JavaScript Document

var rssDisplayer ={

	validate:function(){
		var urlF=$('#ccm_rss_displayer_url');
		var urlV=urlF.val();
		if(!urlV || urlV.length==0 || urlV.indexOf('://')==-1 ){
			ccm_addError(ccm_t('feed-address'));
			urlF.focus();
		}
		
		var itemsF=$('#ccm_rss_displayer_itemsToDisplay');
		var itemsV=itemsF.val();
		if( !itemsV || itemsV.length==0 || parseInt(itemsV)<1 ){
			ccm_addError(ccm_t('feed-num-items'));
			itemsF.focus();
		}
			
	}
}

ccmValidateBlockForm = function() { return rssDisplayer.validate(); }