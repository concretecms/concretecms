ccmValidateBlockForm = function() {
	
	var hasImage = false;
	var hasUrl = false;
	var hasCode = false;
		
	if($('#ccm-ad-source').val() == 'new') {  // validate the new ad posting form 
	
		if($('#ccm-ad-name').val() == '') {
			ccm_addError('Enter a name to for this ad\n');
		}
		
		if ($("#ccm-ad-image-value").val() == '' || $("#ccm-ad-image-value").val() == 0) { 
			hasImage = false;
		} else {
			hasImage = true;	
		}
		
		if($('#ccm-ad-url').val() == '') {
			hasUrl = false;
		} else {
			hasUrl = true;	
		}
	
		if($('#ccm-ad-html').val() == '') {
			hasCode = false;
		} else {
			hasCode = true;	
		}
		
		// if they didn't enter code require the image & url
		if(!hasCode) {
			if(!hasImage){
				ccm_addError('You must choose an Image\n');		
			}
			if(!hasUrl) {
				ccm_addError('You must enter a url\n');		
			}
		}
	
	} else { // validate the existing ad form
	
		
	
	
	}
	return false;
}


var Advertisement ={
	init:function(){
		this.blockForm=document.forms['ccm-block-form'];
		this.tabSetup();
		this.radioSetup();
	},	
	
	tabSetup: function(){
		$('ul#ccm-ad-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane=this.id.replace('ccm-ad-tab-','');
				Advertisement.showPane(pane);
			}
		});		
	},
	showPane:function(pane){
		$('ul#ccm-ad-tabs li').each(function(num,el){ $(el).removeClass('ccm-nav-active') });
		$(document.getElementById('ccm-ad-tab-'+pane).parentNode).addClass('ccm-nav-active');
		$('div.ccm-adPane').each(function(num,el){ el.style.display='none'; });
		$('#ccm-adPane-'+pane).css('display','block');
		if(pane == 'existing') {
			$('#ccm-ad-source').val('existing');
		} else {
			$('#ccm-ad-source').val('new');	
		}
	},
	
	radioSetup: function() {
		$('.ccm-ad-sourceSelect').change(function(e){
			if($(this).is(':checked')) {
				if($(this).val() == "single") {
					$('#ccm-ad-group-source').hide();
					$('#ccm-ad-single-source').show();
				} else {
					$('#ccm-ad-single-source').hide();
					$('#ccm-ad-group-source').show();
				}
			}									 
		});	
		
		if($('.ccm-ad-sourceSelect:checked').val() == "single") {
			$('#ccm-ad-group-source').hide();
			$('#ccm-ad-single-source').show();
		} else {
			$('#ccm-ad-single-source').hide();
			$('#ccm-ad-group-source').show();
		}
	}
};

$(function(){ Advertisement.init(); });
