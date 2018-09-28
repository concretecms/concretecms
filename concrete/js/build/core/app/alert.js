/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global PNotify, ccmi18n */

;(function(global, $) {
    'use strict';

    // Simple alert using dialog class.
    PNotify.prototype.options.styling = {
        container: "ccm-notification",
        notice: "ccm-notification-warning",
        notice_icon: "glyphicon glyphicon-exclamation-sign",
        info: "ccm-notification-info",
        info_icon: "glyphicon glyphicon-info-sign",
        success: "ccm-notification-success",
        success_icon: "glyphicon glyphicon-ok-sign",
        error: "ccm-notification-danger",
        error_icon: "glyphicon glyphicon-warning-sign",
        closer: "ccm-notification-closer",
        pin_up: false,
        pin_down: false
    };
    PNotify.prototype.options.width = '400px';
    PNotify.prototype.options.addclass = 'ccm-ui';
    PNotify.prototype.options.animate = {
        animate: true,
        in_class: 'fadeIn',
        out_class: 'bounceOutRight'
    };
    PNotify.prototype.options.buttons.closer_hover = false;

    var ConcreteAlert = {

        /*
        defaultStack: {
            dir1: "down",
            dir2: "left",
            push: "bottom",
            spacing1: 36,
            spacing2: 36,
            context: $("body"),
            modal: false
        },*/

        dialog : function(title, message, onCloseFn) {
            var $div = $('<div id="ccm-popup-alert" class="ccm-ui"><div id="ccm-popup-alert-message">' + message + '</div></div>');
            $div.dialog({
                title: title,
                width: 500,
                maxHeight: 500,
                modal: true,
                dialogClass: 'ccm-ui',
                close: function() {
                	$div.remove();
                	if (onCloseFn) {
                		onCloseFn();
                	}
                }
            });
        },

        confirm: function(message, onConfirmation, btnClass, btnText) {

            var $div = $('<div id="ccm-popup-confirmation" class="ccm-ui"><div id="ccm-popup-confirmation-message">' + message + '</div>');

            btnClass = btnClass ? 'btn ' + btnClass : 'btn btn-primary';
            btnText = btnText ? btnText : ccmi18n.go;

            $div.dialog({
                title: ccmi18n.confirm,
                width: 500,
                maxHeight: 500,
                modal: true,
                dialogClass: 'ccm-ui',
                close: function() {
                    $div.remove();
                },
                buttons:[{}],
                'open': function () {
                    $(this).parent().find('.ui-dialog-buttonpane').addClass("ccm-ui").html('');
                    $(this).parent().find('.ui-dialog-buttonpane').append(
                        '<button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-default">' +
                        ccmi18n.cancel + '</button><button data-dialog-action="submit-confirmation-dialog" ' +
                        'class="btn ' + btnClass + ' pull-right">' + btnText + '</button></div>');
                }
            });

            $div.parent().on('click', 'button[data-dialog-action=submit-confirmation-dialog]', function() {
                return onConfirmation();
            });

        },



        info: function(defaults) {
            var options = $.extend({
                type: 'info',
                icon: 'question'
            }, defaults);

            return this.notify(options);
        },

        error: function(defaults) {
            var options = $.extend({
                type: 'error',
                icon: 'exclamation-circle'
            }, defaults);

            return this.notify(options);
        },

        notify: function(defaults) {
            var options = $.extend({
                type: 'success',
                icon: 'check',
                title: false,
                message: false,
                appendTo: false,
                delay: 2000,
                callback: function() {}
            }, defaults);

            var notifyOptions = {
                text: options.message,
                icon: 'fa fa-' + options.icon,
                type: options.type,
                delay: options.delay,
                after_close: options.callback
            };

            if (options.title) {
                notifyOptions.title = options.title;
            }
            if (options.hide === false) {
                notifyOptions.hide = options.hide;
            }

            new PNotify(notifyOptions);

        }

    };

    global.ConcreteAlert = ConcreteAlert;

})(this, jQuery);
