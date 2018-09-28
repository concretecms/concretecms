/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, CCM_DISPATCHER_FILENAME, ccmi18n, ConcreteAjaxRequest, ConcreteAlert, ConcreteEvent */

;(function(global, $) {
    'use strict';

    function ConcreteUserSelector($element, options) {
        var my = this;
        options = $.extend({
            'chooseText': ccmi18n.chooseUser,
            'loadingText': ccmi18n.loadingText,
            'inputName': 'uID',
            'uID': 0
        }, options);

        my.$element = $element;
        my.options = options;
        my._chooseTemplate = _.template(my.chooseTemplate, {'options': my.options});
        my._loadingTemplate = _.template(my.loadingTemplate);
        my._userLoadedTemplate = _.template(my.userLoadedTemplate);

        my.$element.append(my._chooseTemplate);
        my.$element.on('click', 'a[data-user-selector-link=choose]', function(e) {
            e.preventDefault();
            $.fn.dialog.open({
                title: ccmi18n.chooseUser,
                href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/user/search',
                width: '90%',
                modal: true,
                height: '70%'
            });
        });

        if (my.options.uID) {
            my.loadUser(my.options.uID);
        }

        ConcreteEvent.unsubscribe('UserSearchDialogSelectUser.core');
        ConcreteEvent.unsubscribe('UserSearchDialogAfterSelectUser.core');
        ConcreteEvent.subscribe('UserSearchDialogSelectUser.core', function(e, data) {
            my.loadUser(data.uID);
        });

        ConcreteEvent.subscribe('UserSearchDialogAfterSelectUser.core', function(e) {
            jQuery.fn.dialog.closeTop();
        });

    }

    ConcreteUserSelector.prototype = {


        chooseTemplate: '<div class="ccm-item-selector">' +
            '<input type="hidden" name="<%=options.inputName%>" value="0" /><a href="#" data-user-selector-link="choose"><%=options.chooseText%></a></div>',
        loadingTemplate: '<div class="ccm-item-selector"><div class="ccm-item-selector-choose"><input type="hidden" name="<%=options.inputName%>" value="<%=uID%>"><i class="fa fa-spin fa-spinner"></i> <%=options.loadingText%></div></div>',
        userLoadedTemplate: '<div class="ccm-item-selector"><div class="ccm-item-selector-item-selected">' +
            '<input type="hidden" name="<%=inputName%>" value="<%=user.uID%>" />' +
            '<div class="ccm-item-selector-item-selected-thumbnail"><%=user.avatar%></div>' +
            '<a data-user-selector-action="clear" href="#" class="ccm-item-selector-clear"><i class="fa fa-close"></i></a>' +
            '<div class="ccm-item-selector-item-selected-title"><%=user.displayName%></div>' +
            '</div></div>',

        loadUser: function(uID) {
            var my = this;
            my.$element.html(my._loadingTemplate({'options': my.options, 'uID': uID}));

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/user/get_json',
                data: {'uID': uID},
                error: function(r) {
                    ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.errorResponseToString(r));
                },
                success: function(r) {
                    var user = r.users[0];
                    my.$element.html(my._userLoadedTemplate({'inputName': my.options.inputName, 'user': user}));
                    my.$element.on('click', 'a[data-user-selector-action=clear]', function(e) {
                        e.preventDefault();
                        my.$element.html(my._chooseTemplate);
                    });
                }
            });
        }
    };

    // jQuery Plugin
    $.fn.concreteUserSelector = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteUserSelector($(this), options);
        });
    };

    global.ConcreteUserSelector = ConcreteUserSelector;

})(this, jQuery);
