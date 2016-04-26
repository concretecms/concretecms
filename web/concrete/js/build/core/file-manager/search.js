!function(global, $) {
    'use strict';

    function ConcreteFileManager($element, options) {
        'use strict';
        var my = this;
        options = $.extend({
            selectMode: 'multiple' // Enables multiple advanced item selection, range click, etc
        }, options);

        ConcreteAjaxSearch.call(my, $element, options);

    }

    ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

    $.fn.concreteFileManager = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteFileManager($(this), options);
        });
    };

    global.ConcreteFileManager = ConcreteFileManager;
    //global.ConcreteFileManagerMenu = ConcreteFileManagerMenu;

}(window, $);
