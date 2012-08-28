// JavaScript Document

var searchBlock ={
	
	init:function(){
		$('#ccm-searchBlock-externalTarget').click(function(){  searchBlock.showResultsURL(this);  });
	},
	
	showResultsURL:function(cb){
		if(cb.checked) $('#ccm-searchBlock-resultsURL-wrap').css('display','block');
		else $('#ccm-searchBlock-resultsURL-wrap').css('display','none');
	},
	
	pathSelector:function(el){
		var f=$('#ccm-block-form').get(0);
		var isOther=0;
		for( var i=0; i<f.baseSearchPath.length; i++ ){
			if( f.baseSearchPath[i].id=='baseSearchPathOther' && f.baseSearchPath[i].checked ){
				isOther=1;
				break;
			}
		}
		if( isOther ) 
			 $('#basePathSelector').css('display','block');
		else $('#basePathSelector').css('display','none');
	}
}
$(function(){ searchBlock.init(); });

ccm_selectSitemapNode = function(cID, cName) {
	$("#searchUnderCID").val(cID);	
}