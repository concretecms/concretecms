/**
 * block ajax
 */

!function(global, $) {
    'use strict';

    function ConcreteFileSelector($element, options) {
        'use strict';
        var my = this,
            options = $.extend({
                'chooseText': ccmi18n_filemanager.chooseNew,
                'inputName': 'concreteFile',
                'fID': false,
                'filters': []
            }, options);

        var dialogOpts = {};
        dialogOpts.filters = options.filters;

        my.$element = $element;
        my.options = options;
        my._chooseTemplate = _.template(my.chooseTemplate, {'options': my.options});
        my._loadingTemplate = _.template(my.loadingTemplate);
        my._fileLoadedTemplate = _.template(my.fileLoadedTemplate);

        my.$element.append(my._chooseTemplate);
        my.$element.on('click', 'div.ccm-file-selector-choose-new', function() {
            ConcreteFileManager.launchDialog(function(data) {
                my.loadFile(data.fID, function() {
                    my.$element.closest('form').trigger('change');
                });
            }, dialogOpts);
            return false;
        });


        if (my.options.fID) {
            my.loadFile(my.options.fID);
        }

    }

    ConcreteFileSelector.prototype = {


        chooseTemplate: '<div class="ccm-file-selector-choose-new">' +
            '<input type="hidden" name="<%=options.inputName%>" value="0" /><%=options.chooseText%></div>',
        loadingTemplate: '<div class="ccm-file-selector-loading"><input type="hidden" name="<%=inputName%>" value="<%=fID%>"><img src="' + CCM_IMAGE_PATH + '/throbber_white_16.gif" /></div>',
        fileLoadedTemplate: '<div class="ccm-file-selector-file-selected"><input type="hidden" name="<%=inputName%>" value="<%=file.fID%>" />' +
            '<div class="ccm-file-selector-file-selected-thumbnail"><%=file.resultsThumbnailImg%></div>' +
            '<div class="ccm-file-selector-file-selected-title"><div><%=file.title%></div></div><div class="clearfix"></div>' +
            '</div>',

        loadFile: function(fID, callback) {
            var my = this;
            my.$element.html(my._loadingTemplate({'inputName': my.options.inputName, 'fID': fID}));
            ConcreteFileManager.getFileDetails(fID, function(r) {
                var file = r.files[0];
                my.$element.html(my._fileLoadedTemplate({'inputName': my.options.inputName, 'file': file}));
                my.$element.find('.ccm-file-selector-file-selected').on('click', function(event) {
                    var menu = file.treeNodeMenu;
                    if (menu) {
                        var concreteMenu = new ConcreteFileMenu($(this), {
                            menuLauncherHoverClass: 'ccm-file-manager-menu-item-hover',
                            menu: $(menu),
                            handle: 'none',
                            container: my
                        });
                        concreteMenu.show(event);
                    }
                });
                if (callback) {
                    callback(r);
                }
            });
        }

    }

    // jQuery Plugin
    $.fn.concreteFileSelector = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteFileSelector($(this), options);
        });
    }

    global.ConcreteFileSelector = ConcreteFileSelector;

}(this, $);
