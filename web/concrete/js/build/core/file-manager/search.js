!function(global, $) {
    'use strict';

    function ConcreteFileManager($element, options) {
        'use strict';
        var my = this;
        options = $.extend({
            'breadcrumbElement': 'div.ccm-search-results-breadcrumb',
            selectMode: 'multiple' // Enables multiple advanced item selection, range click, etc
        }, options);

        this.$breadcrumb = $(options.breadcrumbElement);

        this.currentFolder = 0;
        ConcreteAjaxSearch.call(my, $element, options);

        my.setupEvents();
        my.setupAddFolder();
    }

    ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

    ConcreteAjaxSearch.prototype.setupBreadcrumb = function(result) {
        var my = this;
        if (result.breadcrumb) {
            my.$breadcrumb.html('');
            var $nav = $('<ol data-search-navigation="breadcrumb" class="breadcrumb" />');
            $.each(result.breadcrumb, function(i, entry) {
                if (entry.active) {
                    $nav.append('<li> ' + entry.name + '</li>');
                } else {
                    $nav.append('<li><a data-folder-node-id="' + entry.folder + '" href="' + entry.url + '">' + entry.name + '</a></li>');
                }
            });

            $nav.appendTo(my.$breadcrumb);

            $nav.on('click.concreteSearchBreadcrumb', 'a', function() {
                my.loadFolder($(this).attr('data-folder-node-id'));
                return false;
            });

        }
    }

    ConcreteFileManager.prototype.setupFolders = function(result) {
        var my = this;
        var $total = my.$element.find('tbody tr');
        if (result.folder) {
            my.currentFolder = result.folder.treeNodeID;
        }
        my.$element.find('tbody tr').on('dblclick', function() {
            var index = $total.index($(this));
            if (index > -1) {
                var result = my.getResult().items[index];
                if (result) {
                    if (result.isFolder) {
                        my.loadFolder(result.treeNodeID);
                    }
                }
            }
        });
    }

    ConcreteFileManager.prototype.setupEvents = function() {
        var my = this;
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
            if (data.form == 'add-folder') {
                my.loadFolder(my.currentFolder);
            }
        });

    }

    ConcreteFileManager.prototype.setupAddFolder = function() {
        var my = this;
        my.$element.find('a[data-launch-dialog=add-file-manager-folder]').on('click', function() {
            $('div[data-dialog=add-file-manager-folder] input[name=currentFolder]').val(my.currentFolder);
            jQuery.fn.dialog.open({
                element: 'div[data-dialog=add-file-manager-folder]',
                modal: true,
                width: 320,
                title: 'Add Folder',
                height: 'auto'
            });
        });
    }

    ConcreteFileManager.prototype.updateResults = function(result) {
        var my = this;
        ConcreteAjaxSearch.prototype.updateResults.call(my, result);
        my.setupFolders(result);
        my.setupBreadcrumb(result);
    }

    ConcreteFileManager.prototype.loadFolder = function(folderID) {
        var my = this;
        var data = my.getSearchData();
        data.push({'name': 'folder', 'value': folderID});
        my.currentFolder = folderID;
        my.ajaxUpdate(my.options.result.baseUrl, data);
    }

    $.fn.concreteFileManager = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteFileManager($(this), options);
        });
    };

    global.ConcreteFileManager = ConcreteFileManager;
    //global.ConcreteFileManagerMenu = ConcreteFileManagerMenu;

}(window, $);
