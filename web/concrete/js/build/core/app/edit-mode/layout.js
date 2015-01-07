(function (window, $, _, Concrete) {
    'use strict';

    var Layout = Concrete.Layout = function Layout(elem, edit_mode) {
        this.init.apply(this, _(arguments).toArray());
    };

    Layout.prototype = _.extend(Object.create(Concrete.Block.prototype), {

        init: function(elem, edit_mode) {
            var my = this;
            my.bindEvent('EditModeInlineEditLoaded.editmode', function (e, data) {
                if (data.block === my) {
                    my.bindDrag();
                }
            });
            Concrete.Block.prototype.init.call(my, elem, edit_mode, $());

            elem.children('.ccm-block-cover').remove();
        },

        bindDrag: function layoutBindDrag() {
            var my = this,
                peper = $('a[data-layout-command="move-block"]').parent();

            $.pep.unbind(peper);
            peper.pep(my.getPepSettings());
        },

        addToDragArea: function layoutAddToDragArea() {
            Concrete.Block.prototype.addToDragArea.apply(this, _.toArray(arguments));

            var container = $('#ccm-inline-toolbar-container');
            container.css({
                top: this.getElem().offset().top - container.outerHeight() - 5
            });
        }

    });

}(window, jQuery, _, Concrete));
