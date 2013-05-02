/** 
 * Basic functions
 */

/** 
 * JavaScript localization. Provide a key and then reference that key in PHP somewhere (where it will be translated)
 */
ccm_t = function(key) {
	return $("input[name=ccm-string-" + key + "]").val();
}


/** 
 * Basic JSON parsing used in block and page editing
 */
ccm_parseJSON = function(resp, onNoError) {
	if (resp.error) {
		alert(resp.message);	
	} else {
		onNoError();
	}
}

/**
 * Adding header files through JavaScript - useful for block editing responses in page that return CSS and JS files that have to be added to the DOM
 */
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

//legacy 
var ccm_isBlockError = false;
var ccm_blockError = false;

ccm_addError = function(err) {
	if (!ccm_isBlockError) {
		ccm_blockError = '';
		ccm_blockError += '<ul>';
	}
	
	ccm_isBlockError = true;
	ccm_blockError += "<li>" + err + "</li>";;
}

ccm_resetBlockErrors = function() {
	ccm_isBlockError = false;
	ccm_blockError = "";
}



// legacy menu. Will go away when all are converted to bootstrap menus
ccm_fadeInMenu = function(bobj, e) {
	var mwidth = bobj.find('div.popover div.inner').width();
	var mheight = bobj.find('div.popover').height();
	bobj.hide();
	bobj.css('visibility', 'visible');
	
	var posX = e.pageX + 2;
	var posY = e.pageY + 2;

	if ($(window).height() < e.clientY + mheight) {
		posY = posY - mheight - 10;
		posX = posX - (mwidth / 2);
		bobj.find('div.popover').removeClass('below');
		bobj.find('div.popover').addClass('above');
	} else {
		posX = posX - (mwidth / 2);
		posY = posY + 10;
		bobj.find('div.popover').removeClass('above');
		bobj.find('div.popover').addClass('below');
	}	
	
	bobj.css("top", posY + "px");
	bobj.css("left", posX + "px");
	bobj.fadeIn(60);
}

ccm_hideMenus = function() {
	/* 1st, hide all items w/the css menu class */
	ccm_activeMenu = false;
	$("div.ccm-menu").hide();
	$("div.ccm-menu").css('visibility', 'hidden');
	$("div.ccm-menu").show();
}
