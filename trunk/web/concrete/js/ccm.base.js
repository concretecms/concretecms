var ccm_uiLoaded = false;
var ccm_siteActivated = true;
/* animated effects */
var ccm_animEffects = true;

ccm_deactivateSite = function(onDone) {
	if (ccm_siteActivated == false) {
		return false;
	}
	
	if (ccm_animEffects) {
				
		$("#ccm-overlay").fadeIn(100, function() {
			ccm_siteActivated = false;
			if (typeof onDone == 'function') {
				onDone();
			}
		});
	
	} else {
		$("#ccm-overlay").show();
		ccm_siteActivated = false;
		if (typeof onDone == 'function') {
			onDone();
		}
	}
}

ccm_activateSite = function() {
	if (ccm_animEffects) {
		$("#ccm-overlay").fadeOut(100);
	} else {
		$("#ccm-overlay").hide();
	}
	ccm_siteActivated = true;
}




if ($.browser.msie) {
	ccm_animEffects = false;
}
