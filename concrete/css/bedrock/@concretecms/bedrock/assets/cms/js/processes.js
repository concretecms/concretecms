/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */

;(function(global, $) {
    'use strict'

    $(function() {
        ConcreteEvent.subscribe('TaskActivityWindowShow', function (e, data) {
            jQuery.fn.dialog.open({
                width: '720',
                height: '540',
                modal: true,
                title: ccmi18n.siteActivity,
                href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialog/process/activity/' + data.token
            })

        })
    })


})(window, jQuery); // eslint-disable-line semi
