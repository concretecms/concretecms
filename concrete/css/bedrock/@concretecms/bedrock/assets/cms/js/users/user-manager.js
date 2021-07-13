/* global ccmi18n, CCM_DISPATCHER_FILENAME, ConcreteAlert, ConcreteAjaxRequest, ConcreteEvent */
class ConcreteUserManager {
    static launchDialog(callback, opts) {
        const options = {
            multipleSelection: false
        }

        $.extend(options, opts)

        $.fn.dialog.open({
            title: ccmi18n.chooseUser,
            href: `${CCM_DISPATCHER_FILENAME}/ccm/system/dialogs/user/search`,
            width: '740px',
            modal: true,
            height: '600px',
            data: options,
            onOpen: function() {
                ConcreteEvent.unsubscribe('UserSearchDialogSelectUser.core')
                ConcreteEvent.subscribe('UserSearchDialogSelectUser.core', function (e, r) {
                    const response = {}
                    if (!options.multipleSelection) {
                        response.uID = r.uID[0]
                    } else {
                        response.uID = r.uID
                    }

                    $.fn.dialog.closeTop()
                    callback(response)
                })
            }
        })
    }

    static getUserDetails(userID, callback) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: `${CCM_DISPATCHER_FILENAME}/ccm/system/user/get_json`,
            data: { uID: userID },
            error: function (r) {
                ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.errorResponseToString(r))
            },
            success: function(r) {
                callback(r)
            }
        })
    }
}

global.ConcreteUserManager = ConcreteUserManager
