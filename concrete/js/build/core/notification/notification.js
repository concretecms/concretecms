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

                var $items = my.$element.find('div[data-notification-alert-id]');
                if ($items.length < 1) {
                    my.$element.find('[data-notification-description=empty]').show();
                }
            });
        });
    }

    ConcreteNotificationList.prototype = {


    }

    // jQuery Plugin
    $.fn.concreteNotificationList = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteNotificationList($(this), options);
        });
    }

    global.ConcreteNotificationList = ConcreteNotificationList;

}(this, $);
