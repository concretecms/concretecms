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
	ccm_topPaneDeactivated = false;
}

// called in versions popup
ccm_disableLinks = function() {
	$(document.body).append('<div style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; z-index: 1000"></div>');
}

if ($.browser.msie) {
	ccm_animEffects = false;
}

$(function() {
	$(document.body).append('<div id="ccm-overlay"></div>');
});