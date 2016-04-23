!function(global, $) {
    'use strict';

    function ConcreteFileManagerHeader($element, options) {
        'use strict';
        var my = this;
        options = $.extend({
        }, options);

        my.$element = $element;
        my.setupAddFolder();
        my.setupEvents();
    }

    ConcreteFileManagerHeader.prototype.setupEvents = function() {
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
            if (data.form == 'add-folder') {

            }
        });

    }

    ConcreteFileManagerHeader.prototype.setupAddFolder = function() {
        var my = this;
        my.$element.find('a[data-launch-dialog=add-file-manager-folder]').on('click', function() {
            jQuery.fn.dialog.open({
                element: 'div[data-dialog=add-file-manager-folder]',
                modal: true,
                width: 320,
                title: 'Add Folder',
                height: 'auto'
            });
        });
    }

    global.ConcreteFileManagerHeader = ConcreteFileManagerHeader;

    $.fn.concreteFileManagerHeader = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteFileManagerHeader($(this), options);
        });
    };
}(window, $);
