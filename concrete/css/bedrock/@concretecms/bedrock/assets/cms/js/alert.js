/* global ccmi18n */

import PNotify from './pnotify'
import * as PNotifyAnimate from '@pnotify/animate'

const modules = new Map([
    ...PNotify.defaultModules,
    [PNotifyAnimate, {
        inClass: 'fadeIn',
        outClass: 'bounceOutRight'
    }]
])

const stackBottomRight = new PNotify.Stack({
    dir1: 'up', // The primary stacking direction. Can be 'up', 'down', 'right', or 'left'.
    firstpos1: 25, // The notices will appear 25 pixels from the bottom of the context.
    spacing1: 15, // Number of pixels between notices along dir1.
    dir2: 'left', // The secondary stacking direction. Should be a perpendicular direction to dir1.
    firstpos2: 25, // The notices will appear 25 pixels from the right of the context.
    spacing2: 15, // Number of pixels between notices along dir2.
    push: 'bottom', // Push new notices on top of previous ones.
    maxOpen: 3,
    modal: false
})

class ConcreteAlert {
    static dialog (title, message, onCloseFn) {
        const $div = $(`<div id="ccm-popup-alert" class="ccm-ui"><div id="ccm-popup-alert-message">${message}</div></div>`)
        $div.dialog({
            title: title,
            width: 500,
            maxHeight: 500,
            modal: true,
            dialogClass: 'ccm-ui',
            close: function () {
                $div.remove()
                if (onCloseFn) {
                    onCloseFn()
                }
            }
        })
    }

    static confirm (message, onConfirmation, btnClass, btnText) {
        const $div = $(`<div id="ccm-popup-confirmation" class="ccm-ui"><div id="ccm-popup-confirmation-message">${message}</div>`)

        btnClass = btnClass ? `btn ${btnClass}` : 'btn btn-primary'
        btnText = btnText || ccmi18n.go

        $div.dialog({
            title: ccmi18n.confirm,
            width: 500,
            maxHeight: 500,
            modal: true,
            dialogClass: 'ccm-ui',
            close: function () {
                $div.remove()
            },
            buttons: [{}],
            open: function () {
                const $btnPane = $(this).parent().find('.ui-dialog-buttonpane')
                $btnPane.addClass('ccm-ui').html('')
                $btnPane.append(`
                    <button onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary">${ccmi18n.cancel}</button>
                    <button data-dialog-action="submit-confirmation-dialog" class="btn ${btnClass} float-right">${btnText}</button></div>
                `)
            }
        })

        $div.parent().on('click', 'button[data-dialog-action=submit-confirmation-dialog]', function () {
            if (typeof onConfirmation === 'function') {
                return onConfirmation()
            }
        })
    }

    static info (defaults) {
        const options = $.extend({
            type: 'info',
            icon: 'info-circle'
        }, defaults)

        return this.notify(options)
    }

    static error (defaults) {
        const options = $.extend({
            type: 'error',
            icon: 'exclamation-circle'
        }, defaults)

        return this.notify(options)
    }

    static notify (defaults) {
        const options = $.extend({
            type: 'success',
            icon: 'check-circle',
            title: false,
            message: false,
            delay: 2000,
            callback: () => {}
        }, defaults)

        const notifyOptions = {
            text: options.message,
            textTrusted: true,
            icon: 'fas fa-' + options.icon,
            type: options.type,
            delay: options.delay,
            stack: stackBottomRight,
            modules: modules,
            labels: {
                close: ccmi18n.closeWindow
            }
        }

        if (options.title) {
            notifyOptions.title = options.title
        }

        if (options.hide === false) {
            notifyOptions.hide = options.hide
        }

        const notice = PNotify.alert(notifyOptions)

        notice.on('pnotify:afterClose', options.callback)

        return notice
    }
}

global.ConcreteAlert = ConcreteAlert
