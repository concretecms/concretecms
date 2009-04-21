// cannot rely on jQuery being loaded here

var ccm_uiLoaded = false;
var ccm_siteActivated = true;
var ccm_animEffects = true;

ccm_parseJSON = function(resp, onNoError) {
	if (resp.error) {
		alert(resp.message);	
	} else {
		onNoError();
	}
}

ccm_deactivateSite = function(onDone) {
	if (ccm_siteActivated == false) {
		return false;
	}
	
	if ($("#ccm-overlay").length < 1) {
		$(document.body).append('<div id="ccm-overlay"></div>');
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
	td = document.createElement("DIV");
	td.style.position = "absolute";
	td.style.top = "0px";
	td.style.left = "0px";
	td.style.width = "100%";
	td.style.height = "100%";
	td.style.zIndex = "1000";
	document.body.appendChild(td);
}