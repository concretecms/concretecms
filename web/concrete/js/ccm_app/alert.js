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
        if (title == null) {    
            var messageText = message;
        } else {
            var messageText = '<h3>' + title + '</h3>' + message;
        }
        if (icon == null) {
            var icon = 'pencil';
        }

        var style ='info';
        CCMEditMode.showResponseNotification(messageText, icon, style);
    }
}


