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
	
	$("embed,object").each(function() {
		$(this).attr('ccm-style-old-visibility', $(this).css('visibility'));
		$(this).css('visibility', 'hidden');
	});
	
	if (ccm_animEffects) {				
		$("#ccm-overlay").fadeIn(60, function() {
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
		$("#ccm-overlay").fadeOut(60);
	} else {
		$("#ccm-overlay").hide();
	}

	$("embed,object").each(function() {
		$(this).css('visibility', $(this).attr('ccm-style-old-visibility'));
	});

	ccm_siteActivated = true;
	ccm_topPaneDeactivated = false;
}

ccm_addHeaderItem = function(item, type) {
	var doLoad = true;
	if (type == 'CSS') {
		for (i = 0; i < document.styleSheets.length; i++) {
			ss = document.styleSheets[i];			
			if (ss.href == item) {
				doLoad = false;
				break;
			}
		}
	} else if (type == 'JAVASCRIPT') {
		$("script").each(function(i, obj) {
			var src = $(obj).attr('src');
			if (src == item) {
				doLoad = false;
			}
		});
	}
	if (doLoad) {
		switch(type) {
			case 'CSS':
				$('head').append('<link rel="stylesheet" type="text/css" href="' + item + '?ts=' + new Date().getTime() + '" />');
				break;
			case 'JAVASCRIPT':
				$('head').append('<script type="text/javascript" src="' + item + '?ts=' + new Date().getTime() + '"></script>');
				break;
		}
	}
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