/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */
/* global _, ccmi18n, ccmi18n_filemanager, CCM_DISPATCHER_FILENAME, ConcreteAlert, ConcreteAjaxRequest, ConcreteAjaxSearch, ConcreteEvent, ConcreteFileMenu, ConcreteTree */
class ConcreteFileManager {
    static launchDialog(callback, opts) {
        var w = '90%'
        var h = '75%'
        var data = {}
        var i

        var options = $.extend({
            filters: [],
            multipleSelection: false // Multiple selection switch
        }, opts)

        if (options.multipleSelection) {
            data.multipleSelection = true
        }

        if (options.filters.length > 0) {
            data['field[]'] = []

            for (i = 0; i < options.filters.length; i++) {
                var filter = $.extend(true, {}, options.filters[i]) // clone
                data['field[]'].push(filter.field)
                delete (filter.field)
                $.extend(data, filter) // add all remaining fields to the data
            }
        }

        $.fn.dialog.open({
            width: w,
            height: h,
            href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/search',
            modal: true,
            data: data,
            title: ccmi18n_filemanager.chooseFile,
            onOpen: function(dialog) {
                ConcreteEvent.unsubscribe('FileManagerSelectFile')
                ConcreteEvent.subscribe('FileManagerSelectFile', function(e, r) {
                    var response = r || {}
                    if (!options.multipleSelection) {
                        response.fID = r.fID[0]
                    } else {
                        response.fID = r.fID
                    }
                    $.fn.dialog.closeTop()
                    callback(response)
                })
            }
        })
    }

    static getFileDetails(fID, callback) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/get_json',
            data: { fID: fID },
            error: function(r) {
                ConcreteAlert.dialog(ccmi18n.error, r.responseText)
            },
            success: function(r) {
                callback(r)
            }
        })
    }
}
window.ConcreteFileManager = ConcreteFileManager
