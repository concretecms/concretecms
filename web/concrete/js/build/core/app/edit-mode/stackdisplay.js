(function (window, $, _, Concrete) {
    'use strict';

    /**
     * Display block for in page stacks.
     * @type {Function}
     */
    var StackDisplay = Concrete.StackDisplay = function StackDisplay(elem, edit_mode) {
        this.init.apply(this, _(arguments).toArray());
    };

    StackDisplay.prototype = _.extend(Object.create(Concrete.Block.prototype), {

        init: function(elem, edit_mode) {
            var my = this;

            elem.children('.ccm-area').children('.ccm-area-block-list').find('.ccm-edit-mode-inline-commands').remove();
            Concrete.Block.prototype.init.apply(my, _(arguments).toArray());
        }

    });

}(window, jQuery, _, Concrete));
