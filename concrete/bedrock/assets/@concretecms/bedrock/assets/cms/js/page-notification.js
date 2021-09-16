/* global ccmi18n */

import PNotify from './pnotify'
import * as PNotifyAnimate from '@pnotify/animate'

const modules = new Map([
    ...PNotify.defaultModules,
    [PNotifyAnimate, {
        inClass: 'fadeIn',
        outClass: 'fadeOut'
    }]
])

class PageNotification {
    constructor() {
        const notificationsBoxHTML = `
    <div class="ccm-notifications-box">
        <div class="ccm-notifications-box-header">
            <div data-bs-toggle="collapse" data-bs-target=".ccm-notifications-box-body" aria-expanded="true" role="button">${ccmi18n.notifications}</div>
            <a href="#" class="btn-close ccm-notifications-box-close"></a></div>
        <div class="ccm-notifications-box-body collapse show"></div>
    </div>
`
        this._$notificationsBox = $(notificationsBoxHTML).appendTo('body')
        this._innerStack = new PNotify.Stack({
            dir1: 'up', // The primary stacking direction. Can be 'up', 'down', 'right', or 'left'.
            dir2: 'left', // The secondary stacking direction. Should be a perpendicular direction to dir1.
            push: 'top', // Push new notices on top of previous ones.
            maxOpen: 20,
            modal: false,
            context: this._$notificationsBox.find('.ccm-notifications-box-body').get(0)
        })

        this._$notificationsBox.on('click', '.ccm-notifications-box-close', (e) => {
            e.preventDefault()
            this.clear()
        })
    }

    get innerStack () {
        return this._innerStack
    }

    open () {
        this._$notificationsBox.fadeIn()
        this._$notificationsBox.find('.collapse').collapse('show')
    }

    setupContent () {
        this._$notificationsBox.find('.dialog-launch').dialog()
    }

    clear () {
        this.innerStack.close()
        this.close()
    }

    close () {
        this._$notificationsBox.find('.collapse').collapse('hide')
        this._$notificationsBox.fadeOut()
    }

    static notify (options) {
        const notificationsBox = PageNotification.getInstance()
        const notifyOptions = $.extend({
            type: 'success',
            icon: 'fas fa-check-mark',
            title: false,
            text: false,
            textTrusted: true,
            maxTextHeight: null,
            hide: false
        }, options)

        notifyOptions.stack = notificationsBox.innerStack
        notifyOptions.modules = modules
        notifyOptions.width = '100%'
        notifyOptions.labels = {
            close: ccmi18n.closeWindow
        }

        const notice = PNotify.alert(notifyOptions)
        notice.on('pnotify:afterOpen', () => {
            notificationsBox.setupContent()
        })
        notice.on('pnotify:afterClose', () => {
            if (notificationsBox.innerStack.length === 1) {
                notificationsBox.close()
            }
        })

        notificationsBox.open()

        return notice
    }

    static getInstance() {
        return PageNotification._instance || (PageNotification._instance = new PageNotification())
    }
}

global.ConcretePageNotification = {
    notify: (options) => PageNotification.notify(options)
}
