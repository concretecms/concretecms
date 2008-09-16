var pageList ={
	servicesDir: $("input[name=pageListToolsDir]").val(),
	init:function(){
		this.blockForm=document.forms['ccm-block-form'];
		this.cParentIDRadios=this.blockForm.cParentID;
		for(var i=0;i<this.cParentIDRadios.length;i++){
			this.cParentIDRadios[i].onclick  = function(){ pageList.locationOtherShown(); }
			this.cParentIDRadios[i].onchange = function(){ pageList.locationOtherShown(); }			
		}
		
		this.rss=document.forms['ccm-block-form'].rss;
		for(var i=0;i<this.rss.length;i++){
			this.rss[i].onclick  = function(){ pageList.rssInfoShown(); }
			this.rss[i].onchange = function(){ pageList.rssInfoShown(); }			
		}
		
		this.tabSetup();
		this.loadPreview();
	},	
	tabSetup: function(){
		$('ul#ccm-pagelist-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane=this.id.replace('ccm-pagelist-tab-','');
				pageList.showPane(pane);
			}
		});		
	},
	showPane:function(pane){
		$('ul#ccm-pagelist-tabs li').each(function(num,el){ $(el).removeClass('ccm-nav-active') });
		$(document.getElementById('ccm-pagelist-tab-'+pane).parentNode).addClass('ccm-nav-active');
		$('div.ccm-pagelistPane').each(function(num,el){ el.style.display='none'; });
		$('#ccm-pagelistPane-'+pane).css('display','block');
		if(pane=='preview') this.loadPreview();
	},
	locationOtherShown:function(){
		for(var i=0;i<this.cParentIDRadios.length;i++){
			if( this.cParentIDRadios[i].checked && this.cParentIDRadios[i].value=='OTHER' ){
				$('#ccm-summary-selected-page-wrapper').css('display','block');
				return; 
			}				
		}
		$('#ccm-summary-selected-page-wrapper').css('display','none');
	},
	rssInfoShown:function(){
		for(var i=0;i<this.rss.length;i++){
			if( this.rss[i].checked && this.rss[i].value=='1' ){
				$('#ccm-pagelist-rssDetails').css('display','block');
				return; 
			}				
		}
		$('#ccm-pagelist-rssDetails').css('display','none');
	},
	loadPreview:function(){
		var loaderHTML = '<div style="padding: 20px; text-align: center"><img src="' + CCM_IMAGE_PATH + '/throbber_white_32.gif"></div>';
		$('#ccm-pagelistPane-preview').html(loaderHTML);
		var qStr=$(this.blockForm).formSerialize();
		$.ajax({ 
			url: this.servicesDir+'preview_pane.php?'+qStr,
			success: function(msg){ $('#ccm-pagelistPane-preview').html(msg); }
		});
	},
	validate:function(){
			var failed=0;
			
			var rssOn=$('#ccm-pagelist-rssSelectorOn');
			var rssTitle=$('#ccm-pagelist-rssTitle');
			if( rssOn && rssOn.attr('checked') && rssTitle && rssTitle.val().length==0 ){
				alert('Please give your RSS Feed a name.');
				rssTitle.focus();
				failed=1;
			}
			
			if(failed){
				ccm_isBlockError=1;
				return false;
			}
			return true;
	}	
}
$(function(){ pageList.init(); });

ccm_selectSitemapNode = function(cID, cName) {
	$("#ccm-pageList-underCName").html(cName);
	$("#ccm-pageList-cValueField").val(cID);
}
ccmValidateBlockForm = function() { return pageList.validate(); }