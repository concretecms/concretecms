!function(global, $) {
    'use strict';

    function ConcreteFileManager($element, options) {
        'use strict';
        var my = this;
        options = $.extend({
            'breadcrumbElement': 'div.ccm-search-results-breadcrumb',
            'bulkParameterName': 'fID',
            selectMode: 'multiple' // Enables multiple advanced item selection, range click, etc
        }, options);

        my.currentFolder = 0;
        my.$breadcrumb = $(options.breadcrumbElement);
        my.$advancedSearchButton = $element.find('a[data-launch-dialog=advanced-search]');
        my.$resetSearchButton = $element.find('a[data-button-action=clear-search]');

        my._templateFileProgress = _.template('<div id="ccm-file-upload-progress" class="ccm-ui"><div id="ccm-file-upload-progress-bar">' +
            '<div class="progress progress-striped active"><div class="progress-bar" style="width: <%=progress%>%;"></div></div>' +
            '</div></div>');

        ConcreteAjaxSearch.call(my, $element, options);

        ConcreteTree.setupTreeEvents();

        my.setupEvents();
        my.setupAddFolder();
        my.setupFileUploads();
        my.setupFileDownloads();


    }

    ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

    ConcreteFileManager.prototype.setupBreadcrumb = function(result) {
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

    ConcreteFileManager.prototype.setupFileDownloads = function() {
        var my = this;
        if (!$('#ccm-file-manager-download-target').length) {
            my.$downloadTarget = $('<iframe />', {
                'name': 'ccm-file-manager-download-target',
                'id': 'ccm-file-manager-download-target'
            }).appendTo(document.body);
        } else {
            my.$downloadTarget = $('#ccm-file-manager-download-target');
        }
    };

    ConcreteFileManager.prototype.setupFileUploads = function() {
        var my = this,
            $fileUploader = $('#ccm-file-manager-upload'),
            $maxWidth = $fileUploader.data('image-max-width'),
            $maxHeight = $fileUploader.data('image-max-height'),
            $imageResize = ($maxWidth > 0 && $maxHeight>0),
            $quality = $fileUploader.data('image-quality'),
            errors = [],
            files = [],
            error_template = _.template(
                '<ul><% _(errors).each(function(error) { %>' +
                '<li><strong><%- error.name %></strong><p><%- error.error %></p></li>' +
                '<% }) %></ul>'),
            args = {
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/upload',
                dataType: 'json',
                disableImageResize: !$imageResize,
                imageQuality: ($quality > 0 ? $quality : 85),
                imageMaxWidth:($maxWidth > 0 ? $maxWidth : 1920),
                imageMaxHeight:($maxHeight > 0 ? $maxHeight : 1080),
                error: function(r) {
                    var message = r.responseText;
                    try {
                        message = jQuery.parseJSON(message).errors;
                        var name = this.files[0].name;
                        _(message).each(function(error) {
                            errors.push({ name:name, error:error });
                        });
                    } catch (e) {}
                },
                progressall: function(e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#ccm-file-upload-progress-wrapper').html(my._templateFileProgress({'progress': progress}));
                },
                start: function() {
                    errors = [];
                    $('<div />', {'id': 'ccm-file-upload-progress-wrapper'}).html(my._templateFileProgress({'progress': 100})).appendTo(document.body);
                    $.fn.dialog.open({
                        title: ccmi18n_filemanager.uploadProgress,
                        width: 400,
                        height: 50,
                        onClose: function($dialog) {
                            $dialog.jqdialog('destroy').remove();
                        },
                        element: $('#ccm-file-upload-progress-wrapper'),
                        modal: true
                    });
                },
                done: function(e, data)
                {
                    files.push(data.result[0]);
                },
                stop: function() {
                    jQuery.fn.dialog.closeTop();

                    if (errors.length) {
                        ConcreteAlert.dialog(ccmi18n_filemanager.uploadFailed, error_template({errors: errors}));
                    } else {
                        var canAdd = false;
                        _.each(files, function(file) {
                            if (file.canEditFileProperties) {
                                canAdd = true;
                            }
                        });
                        if (canAdd) {
                            my._launchUploadCompleteDialog(files);
                        } else {
                            my.reloadFolder();
                        }
                        files = [];
                    }
                }
            };

        $fileUploader.find('input[name=ccm_token]').val(my.options.upload_token);

        $fileUploader.fileupload(args);

        $('a[data-dialog=add-files]').on('click', function() {
            $.fn.dialog.open({
                width: 500,
                height: 500,
                modal: true,
                title: ccmi18n_filemanager.addFiles,
                href: CCM_DISPATCHER_FILENAME + '/tools/required/files/import?currentFolder=' + my.currentFolder
            });
        });

    };

    ConcreteFileManager.prototype._launchUploadCompleteDialog = function(files) {
        var my = this;
        ConcreteFileManager.launchUploadCompleteDialog(files, my);
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
                my.reloadFolder();
            }
        });

        ConcreteEvent.unsubscribe('FileManagerAddFilesComplete');
        ConcreteEvent.subscribe('FileManagerAddFilesComplete', function(e, data) {
            my._launchUploadCompleteDialog(data.files);
        });
        ConcreteEvent.unsubscribe('FileManagerDeleteFilesComplete');
        ConcreteEvent.subscribe('FileManagerDeleteFilesComplete', function(e, data) {
            my.reloadFolder();
        });

        ConcreteEvent.unsubscribe('ConcreteTreeUpdateTreeNode.concreteTree');
        ConcreteEvent.subscribe('ConcreteTreeUpdateTreeNode.concreteTree', function(e, r) {
            my.reloadFolder();
        });

        ConcreteEvent.unsubscribe('ConcreteTreeDeleteTreeNode.concreteTree');
        ConcreteEvent.subscribe('ConcreteTreeDeleteTreeNode.concreteTree', function(e, r) {
            my.reloadFolder();
        });
    }

    ConcreteFileManager.prototype.activateMenu = function($menu) {
        var my = this;
        if (my.getSelectedResults().length > 1) {
            // bulk menu
            $menu.find('a').on('click.concreteFileManagerBulkAction', function(e) {

                var value = $(this).attr('data-bulk-action'),
                    type = $(this).attr('data-bulk-action-type'),
                    ids = [];

                $.each(my.getSelectedResults(), function(i, result) {
                    ids.push(result.fID);
                });

                my.handleSelectedBulkAction(value, type, $(this), ids);
            });
        } else {
            $menu.find('a[data-file-manager-action=clear]').on('click', function() {
                var menu = ConcreteMenuManager.getActiveMenu();
                if (menu) {
                    menu.hide();
                }

                //_.defer(function() { container.$element.html(container._chooseTemplate); });
                return false;
            });
            $menu.find('a[data-file-manager-action=download]').on('click', function(e) {
                e.preventDefault();
                window.frames['ccm-file-manager-download-target'].location =
                    CCM_TOOLS_PATH + '/files/download?fID=' + $(this).attr('data-file-id');
            });
            $menu.find('a[data-file-manager-action=duplicate]').on('click', function() {
                $.concreteAjax({
                    url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/duplicate',
                    data: {fID: $(this).attr('data-file-id')},
                    success: function(r) {
                        if (typeof(container.refreshResults) != 'undefined') {
                            container.refreshResults();
                        }
                    }
                });
                return false;
            });
        }
    }

    ConcreteFileManager.prototype.setupBulkActions = function() {
        var my = this;

        // Or, maybe we're using a button launcher
        my.$element.on('click', 'button.btn-menu-launcher', function(event) {
            var $menu = my.getResultMenu(my.getSelectedResults());
            if ($menu) {
                $(this).parent().find('ul').remove();
                $(this).parent().append($menu.find('ul'));
            }
        });
    }

    ConcreteFileManager.prototype.handleSelectedBulkAction = function(value, type, $option, ids) {
        var my = this, itemIDs = [];

        if (value == 'choose') {
            ConcreteEvent.publish('FileManagerBeforeSelectFile', { fID: ids });
            ConcreteEvent.publish('FileManagerSelectFile', { fID: ids });
        } else if (value == 'download') {
            $.each(ids, function(i, id) {
                itemIDs.push({'name': 'item[]', 'value': id});
            });
            my.$downloadTarget.get(0).src = CCM_TOOLS_PATH + '/files/download?' + jQuery.param(itemIDs);
        } else {
            ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, type, $option, ids);
        }
    };


    ConcreteFileManager.prototype.reloadFolder = function() {
        this.loadFolder(this.currentFolder);
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
        my.$element.find('#ccm-file-manager-upload input[name=currentFolder]').val(my.currentFolder);
    }

    ConcreteFileManager.prototype.getResultMenu = function(results) {
        var my = this;
        var $menu = ConcreteAjaxSearch.prototype.getResultMenu.call(this, results);
        if ($menu) {
            my.activateMenu($menu);
        }
        return $menu;
    }

    ConcreteFileManager.prototype.setupSearch = function() {
        var my = this;
        ConcreteAjaxSearch.prototype.setupSearch.call(this);
        my.$element.find('div[data-header=file-manager] form').on('submit', function() {
            var data = $(this).serializeArray();
            data.push({'name': 'submitSearch', 'value': '1'});
            my.ajaxUpdate($(this).attr('action'), data);
            my.$advancedSearchButton.hide();
            my.$resetSearchButton.show();

            return false;
        });

        $('form[data-advanced-search-form]').on('submit', function() {
            var data = $(this).serializeArray();
            data.push({'name': 'submitSearch', 'value': '1'});
            my.ajaxUpdate($(this).attr('action'), data);
            jQuery.fn.dialog.closeTop();
            my.$advancedSearchButton.hide();
            my.$resetSearchButton.show();
            return false;
        });


        my.$resetSearchButton.on('click', function(e) {
            e.preventDefault();
            var $form = $(this).closest('form');
            var data = [{'name': 'submitSearch', 'value': '1'}];
            $form.find('input[name=fKeywords]').val('');
            my.ajaxUpdate($form.attr('action'), data);
            my.$advancedSearchButton.show();
            my.$resetSearchButton.hide();
        });
    }


    ConcreteFileManager.launchUploadCompleteDialog = function(files, my) {
        if (files && files.length && files.length > 0) {
            var data = '';
            _.each(files, function(file) {
                data += 'fID[]=' + file.fID + '&';
            });
            data = data.substring(0, data.length - 1);
            $.fn.dialog.open({
                width: '660',
                height: '500',
                href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/upload_complete',
                modal: true,
                data: data,
                onClose: function() {
                    var data = {filemanager: my}
                    ConcreteEvent.publish('FileManagerUploadCompleteDialogClose', data);
                },
                onOpen: function() {
                    var data = {filemanager: my}
                    ConcreteEvent.publish('FileManagerUploadCompleteDialogOpen', data);
                },
                title: ccmi18n_filemanager.uploadComplete
            });
        }
    }


    $.fn.concreteFileManager = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteFileManager($(this), options);
        });
    };

    global.ConcreteFileManager = ConcreteFileManager;
    //global.ConcreteFileManagerMenu = ConcreteFileManagerMenu;

}(window, $);
