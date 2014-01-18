/** 
 * Simple alert using dialog class.
 */


!function(global, $) {
    'use strict';

    var ConcreteAlert = {
        
        notice : function(title, message, onCloseFn) {
            $('<div id="ccm-popup-alert" class="ccm-ui"><div id="ccm-popup-alert-message" class="alert alert-danger">' + message + '</div></div>').dialog({
                title: title,
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
            ConcreteAlert.showResponseNotification(messageText, icon, style);
        },

        showResponseNotification: function(message, icon, class, appendTo) {
            if (appendTo) {
                var $appendTo = $(appendTo);
            } else {
                var $appendTo = $(document.body);
            }
            $('<div id="ccm-notification-hud" class="ccm-ui ccm-notification ccm-notification-' + class + '"><i class="glyphicon glyphicon-' + icon + '"></i><div class="ccm-notification-inner">' + message + '</div></div>').
            appendTo($appendTo).delay(5).queue(function() {
                $(this).addClass('animated fadeIn');
                $(this).dequeue();
            }).delay(2000).queue(function() {
                $(this).css('opacity', 1);
                $(this).dequeue();
            }).delay(1).queue(function() {
                $(this).addClass('animated bounceOutRight');
                $(this).dequeue();
            }).delay(1000).queue(function() {
                $(this).remove();
                $(this).dequeue();
            });
        }
        /*
        showLoader: function() {
            var $lm = $('<div id="ccm-modal-overlay"><div id="ccm-loader"></div></div>').appendTo($(document.body));
            $lm.delay(1).queue(function() {
                $lm.addClass("ccm-modal-overlay-active");
                $(this).dequeue();
            }).delay(300).queue(function() {
                $('#ccm-loader').addClass('ccm-loader-active');
                $(this).dequeue();
            });
        },


        hideLoader: function() {
            $('#ccm-modal-overlay').remove();
        }
        */
    }

    global.ConcreteAlert = ConcreteAlert;

}(this, $);