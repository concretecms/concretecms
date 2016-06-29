!function(global, $) {
    'use strict';

    function ConcreteUserSelector($element, options) {
        'use strict';
        var my = this,
            options = $.extend({
                'chooseText': ccmi18n.chooseUser,
                'loadingText': ccmi18n.loadingText,
                'inputName': 'uID',
                'uID': 0
            }, options);

        my.$element = $element;
        my.options = options;
        my._chooseTemplate = _.template(my.chooseTemplate, {'options': my.options});
        my._loadingTemplate = _.template(my.loadingTemplate, {'options': my.options});
        my._pageLoadedTemplate = _.template(my.pageLoadedTemplate);
        my._pageMenuTemplate = _.template(ConcretePageAjaxSearchMenu.get());

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
    }

    ConcreteUserSelector.prototype = {


        chooseTemplate: '<div class="ccm-item-selector">' +
            '<input type="hidden" name="<%=options.inputName%>" value="0" /><a href="#" data-user-selector-link="choose"><%=options.chooseText%></a></div>',
        loadingTemplate: '<div class="ccm-item-selector"><div class="ccm-item-selector-choose"><i class="fa fa-spin fa-spinner"></i> <%=options.loadingText%></div></div>',
        userLoadedTemplate: '<div class="ccm-item-selector"><div class="ccm-item-selector-item-selected">' +
            '<input type="hidden" name="<%=inputName%>" value="<%=user.uID%>" />' +
            '<a data-user-selector-action="clear" href="#" class="ccm-item-selector-clear"><i class="fa fa-close"></i></a>' +
            '<div class="ccm-item-selector-item-selected-title"><%=user.name%></div>' +
            '</div></div>',

        loadUser: function(uID) {
            var my = this;
            my.$element.html(my._loadingTemplate);
            /*
            ConcretePageAjaxSearch.getPageDetails(cID, function(r) {
                var page = r.pages[0];
                my.$element.html(my._pageLoadedTemplate({'inputName': my.options.inputName, 'page': page}));
                my.$element.on('click', 'a[data-page-selector-action=clear]', function(e) {
                    e.preventDefault();
                    my.$element.html(my._chooseTemplate);
                });
            });
            */
        }
    }

    // jQuery Plugin
    $.fn.concreteUserSelector = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteUserSelector($(this), options);
        });
    }

    global.ConcreteUserSelector = ConcreteUserSelector;

}(this, $);
