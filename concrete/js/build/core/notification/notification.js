!function(global, $) {
    'use strict';

    function ConcreteNotificationList($element, options) {
        'use strict';
        var my = this,
            options = $.extend({
            }, options);

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

        my.$element.on('click', 'a[data-workflow-task]', function(e) {
            var action = $(this).attr('data-workflow-task'),
                $form = $(this).closest('form'),
                $notification = $(this).closest('div[data-notification-alert-id]');

            e.preventDefault();
            $form.append('<input type="hidden" name="action_' + action + '" value="' + action + '">');

            $form.ajaxSubmit({
                dataType: 'json',
                beforeSubmit: function() {
                    jQuery.fn.dialog.showLoader();
                },
                success: function(r) {
                    $notification.addClass('animated fadeOut');
                    jQuery.fn.dialog.hideLoader();
                    setTimeout(function() {
                        $notification.remove();
                        my.handleEmpty();
                    }, 500);
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
        }

    }

    // jQuery Plugin
    $.fn.concreteNotificationList = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteNotificationList($(this), options);
        });
    }

    global.ConcreteNotificationList = ConcreteNotificationList;

}(this, $);
