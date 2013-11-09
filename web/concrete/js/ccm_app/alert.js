/** 
 * Simple alert using dialog class.
 */

var ccmAlert = {  
    notice : function(title, message, onCloseFn) {
        $('<div id="ccm-popup-alert" class="ccm-ui"><div id="ccm-popup-alert-message" class="alert alert-danger">' + message + '</div></div>').dialog({
            title: title,
            width: 320,
            height: 160,
            modal: true,
			onDestroy: onCloseFn
        });
    },


    hud: function(message, time, icon, title) {
        if (title == null) {    
            var messageText = message;
        } else {
            var messageText = '<strong>' + title + '</strong><br/><br/>' + message;
        }
        if (icon == null) {
            var icon = 'pencil';
        }

        var style ='info';
        CCMEditMode.showResponseNotification(messageText, icon, style);
    }
}


