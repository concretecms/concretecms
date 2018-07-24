/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global CCM_DISPATCHER_FILENAME */

;(function(global, $) {
    'use strict';

    function ConcreteNotificationList($element, options) {
        var my = this;
        options = $.extend({}, options);

        my.$element = $element;
        my.options = options;

        my.$element.on('click', '[data-notification-action=archive]', function(e) {
            e.preventDefault();
            var $item = $(this).closest('div[data-notification-alert-id]'),
                alertID = $item.attr('data-notification-alert-id'),
                token = $item.attr('data-token');

            $.ajax({
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/notification/alert/archive',
                dataType: 'json',
                data: {
                    'naID': alertID,
                    'ccm_token': token
                },
                type: 'post'
            });

            $item.queue(function() {
                $item.addClass('animated fadeOut');
                $item.dequeue();
            }).delay(500).queue(function () {
                $item.remove();
                $item.dequeue();
                my.handleEmpty();

            });
        });

        my.$element.on('change', 'div[data-form=notification] select', function(e) {
            var $form = $(this).closest('form');
            $form.ajaxSubmit({
                dataType: 'html',
                beforeSubmit: function() {
                    my.showLoader();
                },
                success: function(r) {
                    $('div[data-wrapper=desktop-waiting-for-me]').replaceWith(r);
                },
                complete: function() {
                    my.hideLoader();
                }
            });
        });

        my.$element.on('click', 'div.ccm-pagination-wrapper a', function(e) {
            e.preventDefault();
            my.showLoader();
            window.scrollTo(0, 0);
            $.concreteAjax({
                loader: false,
                dataType: 'html',
                url: $(this).attr('href'),
                method: 'get',
                success: function(r) {
                    $('div[data-wrapper=desktop-waiting-for-me]').replaceWith(r);
                },
                complete: function() {
                    my.hideLoader();
                }
            });

        });

        my.$element.on('click', 'a[data-workflow-task]', function(e) {
            var action = $(this).attr('data-workflow-task'),
                $form = $(this).closest('form'),
                $notification = $(this).closest('div[data-notification-alert-id]');

            e.preventDefault();
            $form.append('<input type="hidden" name="action_' + action + '" value="' + action + '">');

            $form.ajaxSubmit({
                dataType: 'json',
                beforeSubmit: function() {
                    my.showLoader();
                },
                success: function(r) {
                    $notification.addClass('animated fadeOut');
                    setTimeout(function() {
                        $notification.remove();
                        my.handleEmpty();
                    }, 500);
                },
                complete: function() {
                    my.hideLoader();
                }
            });
        });
    }

    ConcreteNotificationList.prototype = {

        handleEmpty: function() {
            var my = this;
            var $items = my.$element.find('div[data-notification-alert-id]');
            if ($items.length < 1) {
                my.$element.find('[data-notification-description=empty]').show();
            }
        },

        showLoader: function() {
            $('div[data-list=notification]').addClass('ccm-block-desktop-waiting-for-me-loading');
        },

        hideLoader: function() {
            $('div[data-list=notification]').removeClass();
        }

    };

    // jQuery Plugin
    $.fn.concreteNotificationList = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteNotificationList($(this), options);
        });
    };

    global.ConcreteNotificationList = ConcreteNotificationList;

})(this, jQuery);
