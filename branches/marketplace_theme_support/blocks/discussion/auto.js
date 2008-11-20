var discussionWrapper ={

	init:function(){
		this.blockForm=document.forms['ccm-block-form'];
		this.cParentIDRadios=this.blockForm.cParentID;
		for(var i=0;i<this.cParentIDRadios.length;i++){
			this.cParentIDRadios[i].onclick  = function(){ discussionWrapper.locationOtherShown(); }
			this.cParentIDRadios[i].onchange = function(){ discussionWrapper.locationOtherShown(); }			
		}
		
	},	

	locationOtherShown:function(){
		for(var i=0;i<this.cParentIDRadios.length;i++){
			if( this.cParentIDRadios[i].checked && this.cParentIDRadios[i].value=='OTHER' ){
				$('#ccm-discussion-selected-page-wrapper').css('display','block');
				return; 
			}				
		}
		$('#ccm-discussion-selected-page-wrapper').css('display','none');
	}

};

ccm_selectSitemapNode = function(cID, cName) {
	$("#ccm-discussion-underCName").html(cName);
	$("#ccm-discussion-cValueField").val(cID);
}

$(function() { discussionWrapper.init(); });