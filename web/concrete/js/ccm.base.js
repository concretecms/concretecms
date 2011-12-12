// cannot rely on jQuery being loaded here

var ccm_uiLoaded = false;
var ccm_siteActivated = true;
var ccm_animEffects = false;

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
		$("#ccm-overlay").fadeIn(100);
	} else {
		$("#ccm-overlay").show();
	}
	
	ccm_siteActivated = false;
	if (typeof onDone == 'function') {
		onDone();
	}
}

ccm_activateSite = function() {
	if (ccm_animEffects) {
		$("#ccm-overlay").fadeOut(100);
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
	// "item" might already have a "?v=", so avoid invalid query string.
	var qschar = (item.indexOf('?') != -1 ? '' : '?ts=');
	if (type == 'CSS') {
		if (navigator.userAgent.indexOf('MSIE') != -1) {
			// Most reliable way found to force IE to apply dynamically inserted stylesheet across jQuery versions
			var ss = document.createElement('link'), hd = document.getElementsByTagName('head')[0];
			ss.type = 'text/css'; ss.rel = 'stylesheet'; ss.href = item; ss.media = 'screen';
			hd.appendChild(ss);
		} else {
			if (!($('head').children('link[href*="' + item + '"]').length)) {
				$('head').append('<link rel="stylesheet" media="screen" type="text/css" href="' + item + qschar + new Date().getTime() + '" />');
			}
		}
	} else if (type == 'JAVASCRIPT') {
		if (!($('script[src*="' + item + '"]').length)) {
			$('head').append('<script type="text/javascript" src="' + item + qschar + new Date().getTime() + '"></script>');
		}
	} else {
		if (!($('head').children(item).length)) {
			$('head').append(item);
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