/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, Concrete */

import * as _ from 'underscore';

;(function(window, $) {
    'use strict';

    var ContainerBlock = Concrete.ContainerBlock = function ContainerBlock(elem, edit_mode) {
        this.init.apply(this, _(arguments).toArray());
    };

    ContainerBlock.prototype = _.extend(Object.create(Concrete.Block.prototype), {

        init: function(elem, edit_mode) {
            var my = this;
            Concrete.Block.prototype.init.call(my, elem, edit_mode, $());
            
            elem.children('.ccm-block-cover').remove();
            my.bindDrag();
            my.bindDelete();
        },
        
        bindDelete: function ContainerBlockDelete() {
            var my = this,
                deleter = my.getElem().find('a[data-inline-command=delete-block');
            deleter.on('click', function() {
                my.delete();
            });
        },

        bindDrag: function ContainerBlockBindDrag() {
            var my = this,
                peper = $('a[data-inline-command="move-block"]').parent();

            $.pep.unbind(peper);
            peper.pep(my.getPepSettings());
        }
        
    });

})(window, jQuery);
