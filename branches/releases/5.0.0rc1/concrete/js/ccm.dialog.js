jQuery.fn.dialog = function(settings) {

	// this is probably woefully inefficient. 
	return $(this).each(function() {
		$(this).click(function(e) {
			ccm_dialogOpen=1;
			options = jQuery.fn.dialog.getOptions(settings, $(this));
			jQuery.fn.dialog._create(options);
			$(this).blur();
			return false;
		});	
	});
}
var ccm_dialogOpen=0;

jQuery.fn.dialog._create = function(opts) {
	jQuery.fn.dialog.overlay(opts);
	jQuery.fn.dialog.position(opts);
	jQuery.fn.dialog.loadShell(opts);	
	if (jQuery.fn.dialog.totalDialogs > 0) {
		jQuery.fn.dialog.deactivate(jQuery.fn.dialog.totalDialogs-1);
	}
	/*
	if (opts.replace == true) {
		jQuery.fn.dialog.close(jQuery.fn.dialog.totalDialogs-1);
	}
	*/
	
	jQuery.fn.dialog.showLoader(opts);
	jQuery.fn.dialog.load(opts);
	jQuery.fn.dialog.dialogs.push(opts);
	jQuery.fn.dialog.totalDialogs++;
}

jQuery.fn.dialog.open = function(settings) {
	options = jQuery.fn.dialog.getOptions(settings);
	jQuery.fn.dialog._create(options);

}

jQuery.fn.dialog.getOptions = function(settings, node) {
	var options = jQuery.extend(jQuery.fn.dialog.defaults, settings);

	if (typeof(node) != 'undefined') {
		var _modal = node.attr('dialog-modal');
		var _width = node.attr('dialog-width');
		var _height = node.attr('dialog-height');
		var _title = node.attr('dialog-title');
		var href = node.attr('href');
		var _replace = node.attr('dialog-replace');
	}
	
	if (typeof(_replace) != 'undefined') {
		options.replace = _replace;
	}
	
	if (typeof(_width) != 'undefined') {
		options.width = _width;
	}
	if (typeof(_height) != 'undefined') {
		options.height = _height;
	}
	if (typeof(_title) != 'undefined') {
		options.title = _title;
	}
	if (typeof(_modal) != 'undefined') {
		options.modal = _modal;
	}
	options.modal = (options.modal == "true" || options.modal == true) ? true : false;
	options.replace = (options.replace == "true" || options.replace == true) ? true : false;
	options.href = href;
	
	if (typeof(settings) != 'undefined') {
		if (settings.href != null) {
			options.href = settings.href;
		}
	}
	

	if (typeof(options.width) == 'string') {
		if (options.width.lastIndexOf('%') > -1) {
			var mod = "." + options.width.substring(0, options.width.lastIndexOf('%'));
			options.width = $(window).width() * mod;
		}
	}
	if (typeof(options.height) == 'string') {
		if (options.height.lastIndexOf('%') > -1) {
			var mod = "." + options.height.substring(0, options.height.lastIndexOf('%'));
			options.height = $(window).height() * mod;
		}
	}
	
	options.n = jQuery.fn.dialog.totalDialogs;
	return options;
}

jQuery.fn.dialog.isMacFF = function(fnd) {
	var userAgent = navigator.userAgent.toLowerCase();
	if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1) {
		return true;
	}
}

jQuery.fn.dialog.load = function(fnd) {
	var url = encodeURI(fnd.href);
	$("#ccm-dialog-content" + fnd.n).load(url += "&random=" + (new Date().getTime()),function(){//to do a post change this load method
		jQuery.fn.dialog.position(fnd);
		jQuery.fn.dialog.hideLoader();
		//$("#ccm-dialog-content").remove();
		$("#ccm-dialog-content" + fnd.n + " .ccm-dialog-close").click(function() {
			jQuery.fn.dialog.close(fnd);
		});
		$("#ccm-dialog-content" + fnd.n + " .dialog-launch").dialog();
	});
}

jQuery.fn.dialog.hideLoader = function() {
	$("#ccm-dialog-loader").hide();
}

jQuery.fn.dialog.showLoader = function(fnd) {
	if ($('#ccm-dialog-loader').length < 1) {
		$("body").append("<img id='ccm-dialog-loader' src='"+imgLoader.src+"' />");//add loader to the page
	}
	$('#ccm-dialog-loader').show();//show loader
}

jQuery.fn.dialog.deactivate = function(w) {
	// w = window number. typically the previous window below the current active one
	$("#ccm-dialog-window" + w).css('z-index', '1');
	
}

jQuery.fn.dialog.activate = function(w) {
	// w = window number. typically the previous window below the current active one
	var obj = jQuery.fn.dialog.dialogs[w];
	$("#ccm-dialog-window" + w).css('z-index', obj.realZ);
}

jQuery.fn.dialog.close = function(fnd) {
	jQuery.fn.dialog.totalDialogs--;
	jQuery.fn.dialog.dialogs.splice(jQuery.fn.dialog.totalDialogs, 1);
	$("#TB_imageOff").unbind("click");
	$("#TB_closeWindowButton" + fnd.n).unbind("click");
	if (ccm_animEffects) {
		$("#ccm-dialog-window" + jQuery.fn.dialog.totalDialogs).fadeOut("fast",function(){
			$('#ccm-dialog-window' + jQuery.fn.dialog.totalDialogs).trigger("unload").unbind().remove();
		});
	} else {
		$("#ccm-dialog-window" + jQuery.fn.dialog.totalDialogs).hide();
		$('#ccm-dialog-window' + jQuery.fn.dialog.totalDialogs).trigger("unload").unbind().remove();
	}
	
	if (jQuery.fn.dialog.totalDialogs == 0) {
		$("#TB_HideSelect").trigger("unload").unbind().remove();
		if (ccm_initialSiteActivated) {
			ccm_activateSite();
		}
		if (!ccm_initialHeaderDeactivated) {
			ccm_activateHeader();
		}
	} else {
//		var obj = jQuery.fn.dialog.dialogs[jQuery.fn.dialog.totalDialogs-1];
		jQuery.fn.dialog.activate(jQuery.fn.dialog.totalDialogs-1);
	}

	if (typeof fnd.onClose == "function") {
		fnd.onClose();
	}
	
	document.onkeydown = "";
	document.onkeyup = ""; 
	ccm_dialogOpen=0;
}	

jQuery.fn.dialog.position = function(fnd) {

	fnd.modifiedWidth = parseInt(fnd.width) + 30;
	fnd.modifiedHeight = parseInt(fnd.height)  + 40;
	fnd.contentWidth = fnd.modifiedWidth - 44;
	fnd.contentHeight = fnd.modifiedHeight;
	
	$("#ccm-dialog-window" + fnd.n).css({marginLeft: '-' + parseInt((fnd.modifiedWidth / 2),10) + 'px', width: fnd.modifiedWidth + 'px'});
	if ( !(jQuery.browser.msie && jQuery.browser.version < 7)) { // take away IE6
		$("#ccm-dialog-window" + fnd.n).css({marginTop: '-' + parseInt((fnd.contentHeight / 2),10) + 'px'});
	}
}

jQuery.fn.dialog.loadShell = function(fnd) {
	if($("#ccm-dialog-window" + fnd.n).css("display") != "block"){
		if(fnd.modal == false){//ajax no modal
			$("#ccm-dialog-window" + fnd.n).append("<div class='ccm-dialog-title-bar-l'><div class='ccm-dialog-title-bar-r'><div class='ccm-dialog-title-bar' id='ccm-dialog-title-bar" + fnd.n + "'><div class='ccm-dialog-title' id='ccm-dialog-title" + fnd.n + "'>"+fnd.title+"</div><a href='javascript:void(0)' class='ccm-dialog-close'>Close</a></div></div></div><div id='ccm-dialog-content-wrapper'><div class='ccm-dialog-content-l'><div class='ccm-dialog-content-r'><div class='ccm-dialog-content' id='ccm-dialog-content" + fnd.n + "' style='width:"+fnd.contentWidth+"px;height:"+fnd.contentHeight+"px'></div></div></div></div>");
		}else{//ajax modal
			$("#ccm-dialog-window" + fnd.n).append("<div class='ccm-dialog-title-bar-l'><div class='ccm-dialog-title-bar-r'><div class='ccm-dialog-title-bar' id='ccm-dialog-title-bar" + fnd.n + "'><div class='ccm-dialog-title' id='ccm-dialog-title" + fnd.n + "'>"+fnd.title+"</div></div></div></div><div id='ccm-dialog-content-wrapper'><div class='ccm-dialog-content-l'><div class='ccm-dialog-content-r'><div class='ccm-dialog-content' id='ccm-dialog-content" + fnd.n + "' class='TB_modal' style='width:"+fnd.contentWidth+"px;height:"+fnd.contentHeight+"px;'>");	
		}
	}else{//this means the window is already up, we are just loading new content via ajax
		$("#ccm-dialog-content" + fnd.n)[0].style.width = fnd.contentWidth +"px";
		$("#ccm-dialog-content" + fnd.n)[0].style.height = fnd.contentHeight +"px";
		$("#ccm-dialog-content" + fnd.n)[0].scrollTop = 0;
		$("#ccm-dialog-title" + fnd.n).html(fnd.title);
	}
	$("#ccm-dialog-window" + fnd.n + " .ccm-dialog-close").click(function() {
		jQuery.fn.dialog.close(fnd);
	});
	$("#ccm-dialog-window" + fnd.n).append("<div class='ccm-dialog-content-bl'><div class='ccm-dialog-content-br'><div class='ccm-dialog-content-b'></div></div></div>");
	$("#ccm-dialog-window" + fnd.n).show();

}

jQuery.fn.dialog.overlay = function(fnd) {

	if (fnd.n == 0) {
		ccm_initialHeaderDeactivated = ccm_topPaneDeactivated;
		ccm_initialSiteActivated = ccm_siteActivated;
	}

	ccm_hideMenus();
	ccm_deactivateSite();
	ccm_deactivateHeader();

	if (fnd.zIndex) {
		sz = fnd.zIndex + fnd.n;
	} else {
		sz = jQuery.fn.dialog.startZindex + fnd.n;
	}
	
	fnd.realZ = sz;
	$("body").append("<div class='ccm-dialog-window' id='ccm-dialog-window" + fnd.n + "' style='z-index: " + sz + "'></div>");

	if(jQuery.fn.dialog.isMacFF(fnd)){
		$("#TB_overlay" + fnd.n).addClass("TB_overlayMacFFBGHack");//use png overlay so hide flash
	}else{
		$("#TB_overlay" + fnd.n).addClass("TB_overlayBG");//use background and opacity
	}
}



jQuery.fn.dialog.closeTop = function() {
	var obj = jQuery.fn.dialog.dialogs[jQuery.fn.dialog.totalDialogs-1];
	jQuery.fn.dialog.close(obj);
}

jQuery.fn.dialog.defaults = {
	modal: true,
	width: 500,
	height: 500,
	replace: false,
	title: 'CCM Dialog',
	href: null
};

jQuery.fn.dialog.totalDialogs = 0;
jQuery.fn.dialog.dialogs = new Array();
jQuery.fn.dialog.startZindex = 202;
jQuery.fn.dialog.loaderImage = CCM_IMAGE_PATH + "/throbber_white_32.gif";

var ccm_initialHeaderDeactivated;
	var ccm_initialOverlay;

$(document).ready(function(){   
	imgLoader = new Image();// preload image
	imgLoader.src = jQuery.fn.dialog.loaderImage;

	// preload assets for the dialog window
	i1 = new Image();// preload image
	i1.src = CCM_IMAGE_PATH + "/bg_dialog_br.png";
	i2 = new Image();// preload image
	i2.src = CCM_IMAGE_PATH + "/bg_dialog_b.png";
	i3 = new Image();// preload image
	i3.src = CCM_IMAGE_PATH + "/bg_dialog_bl.png";
	i4 = new Image();// preload image
	i4.src = CCM_IMAGE_PATH + "/bg_dialog_r.png";
	i5 = new Image();// preload image
	i5.src = CCM_IMAGE_PATH + "/bg_dialog_l.png";
	i6 = new Image();// preload image
	i6.src = CCM_IMAGE_PATH + "/bg_dialog_tr.png";
	i7 = new Image();// preload image
	i7.src = CCM_IMAGE_PATH + "/bg_dialog_t.png";
	i8 = new Image();// preload image
	i8.src = CCM_IMAGE_PATH + "/bg_dialog_tl.png";
	
});