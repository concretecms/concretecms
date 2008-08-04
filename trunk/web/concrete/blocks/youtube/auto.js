// JavaScript Document

var videoBlock ={

	validate:function(){
		var failed=0; 
		
		var urlF=$('#YouTubeVideoURL');
		var urlV=urlF.val();
		if(!urlV || urlV.length==0 || urlV.toLowerCase().indexOf('youtube')==-1  ){
			alert('Please enter a valid YouTube url.');
			urlF.focus();
			failed=1;
		}
		
		if(failed){
			ccm_isBlockError=1;
			return false;
		}
		return true;
	} 
}

ccmValidateBlockForm = function() { return videoBlock.validate(); }