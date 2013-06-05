var tags ={
	init:function(){		
		this.tabSetup();
		this.showHideDisplayType();
		
		$('input[name="displayMode"]').change(function() {
			tags.showHideDisplayType();
		});
		
	},	
	tabSetup: function(){
		$('ul#ccm-tags-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane = this.id.replace('ccm-tags-tab-','');
				tags.showPane(pane);
			}
		});		
	},
	showPane:function(pane){
		$('ul#ccm-tags-tabs li').each(function(num,el){ $(el).removeClass('ccm-nav-active') });
		$(document.getElementById('ccm-tags-tab-'+pane).parentNode).addClass('ccm-nav-active');
		$('div.ccm-tagsPane').each(function(num,el){ el.style.display='none'; });
		$('#ccm-tagsPane-'+pane).css('display','block');
	},
	
	validate:function(){
		return true;
	},
	
	showHideDisplayType:function() {
		if($('#displayMode1').attr('checked')) {
			$('#ccm-tags-display-cloud').hide();
			$('#ccm-tags-display-page').show();
		} else {
			$('#ccm-tags-display-page').hide();
			$('#ccm-tags-display-cloud').show();
		}
	}
}
$(function(){ tags.init(); });

ccmValidateBlockForm = function() { return tags.validate(); }