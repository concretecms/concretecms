!function (global, $) {
    'use strict';

    function ConcreteTopicsTree($element, options) {
        return ConcreteTree.call(this, $element, options);
    }

    ConcreteTopicsTree.prototype = Object.create(ConcreteTree.prototype);


    // jQuery Plugin
    $.fn.concreteTopicsTree = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteTopicsTree($(this), options);
        });
    }

    global.ConcreteTopicsTree = ConcreteTopicsTree;

}(this, $);