import * as PNotify from '@pnotify/core'
import * as PNotifyMobile from '@pnotify/mobile'
import * as PNotifyFontAwesome5Fix from '@pnotify/font-awesome5-fix'
import * as PNotifyFontAwesome5 from '@pnotify/font-awesome5'

PNotify.defaultModules.set(PNotifyMobile, {})
PNotify.defaultModules.set(PNotifyFontAwesome5Fix, {})
PNotify.defaultModules.set(PNotifyFontAwesome5, {})

PNotify.defaults.styling = {
    prefix: 'ccm-notification',
    container: 'ccm-notification',
    notice: 'ccm-notification-warning',
    info: 'ccm-notification-info',
    success: 'ccm-notification-success',
    error: 'ccm-notification-danger',
    closer: 'ccm-notification-closer',
    // Confirm Module
    'action-bar': 'ccm-notification-ml',
    'prompt-bar': 'ccm-notification-ml',
    btn: 'btn mx-1',
    'btn-primary': 'btn-primary',
    'btn-secondary': 'btn-secondary',
    input: 'form-control'
}

PNotify.defaults.addClass = 'ccm-ui'
PNotify.defaults.closerHover = false
PNotify.defaults.sticker = false
PNotify.defaults.width = '360px'

export default PNotify
