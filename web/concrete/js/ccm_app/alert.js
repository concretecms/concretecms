/** 
 * Simple alert using dialog class.
 */

var ccmAlert = {  
    notice : function(title, message, onCloseFn) {
        $.fn.dialog.open({
            href: CCM_TOOLS_PATH + '/alert',
            title: title,
            width: 320,
            height: 160,
            modal: false, 
			onOpen: function () {
        		$("#ccm-popup-alert-message").html(message);
			},
			onDestroy: onCloseFn
        }); 
    },
    
    hud: function(message, time, icon, title) {
    	if ($('#ccm-notification-inner').length == 0) { 
    		$(document.body).append('<div id="ccm-notification" class="ccm-ui"><div id="ccm-notification-inner"></div></div>');
    	}
    	
    	if (icon == null) {
    		icon = 'edit_small';
    	}
    	
    	if (title == null) {	
	    	var messageText = message;
	    } else {
	    	var messageText = '<h3>' + title + '</h3>' + message;
	    }
    	$('#ccm-notification-inner').html('<img id="ccm-notification-icon" src="' + CCM_IMAGE_PATH + '/icons/' + icon + '.png" width="16" height="16" /><div id="ccm-notification-message">' + messageText + '</div>');
		
		$('#ccm-notification').show();
		
    	if (time > 0) {
    		setTimeout(function() {
    			$('#ccm-notification').fadeOut({easing: 'easeOutExpo', duration: 300});
    		}, time);
    	}
    	
    }
}


