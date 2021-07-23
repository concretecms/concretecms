/* eslint-disable no-new, no-unused-vars, camelcase */
/* global _, Concrete */

import _ from 'underscore'

;(function(window, $) {
    'use strict'

    var ContainerBlock = Concrete.ContainerBlock = function ContainerBlock(elem, edit_mode) {
        this.init.apply(this, _(arguments).toArray())
    }

    ContainerBlock.prototype = _.extend(Object.create(Concrete.Block.prototype), {

        init: function(elem, edit_mode) {
            var my = this
            Concrete.Block.prototype.init.call(my, elem, edit_mode, $())

            elem.children('.ccm-block-cover').remove()
            my.bindDrag()
            my.bindDelete()
        },

        bindDelete: function ContainerBlockDelete() {
            var my = this
            var deleter = my.getElem().find('>ul a[data-inline-command=delete-block]')
            deleter.on('click.containerDelete', function(e) {
                e.preventDefault()
                my.delete()
            })
        },

        bindDrag: function ContainerBlockBindDrag() {
            var my = this
            var mover = my.getElem().find('>ul a[data-inline-command=move-block]').parent()

            $.pep.unbind(mover)
            mover.pep(my.getPepSettings())
        }

    })
})(window, jQuery); // eslint-disable-line semi
