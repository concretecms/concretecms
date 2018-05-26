/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, ccmi18n, ccmi18n_filemanager, CCM_DISPATCHER_FILENAME, CCM_TOOLS_PATH, ConcreteAlert, ConcreteAjaxRequest, ConcreteAjaxSearch, ConcreteEvent, ConcreteFileMenu, ConcreteTree */

;(function(global, $) {
    'use strict';

    function ConcreteFileManager($element, options) {
        var my = this;
        options = $.extend({
            'breadcrumbElement': 'div.ccm-search-results-breadcrumb.ccm-file-manager-breadcrumb',
            'bulkParameterName': 'fID',
            'searchMethod': 'get',
            'appendToOuterDialog': true,
            'selectMode': 'multiple' // Enables multiple advanced item selection, range click, etc
        }, options);

        my.currentFolder = 0;
        my.interactionIsDragging = false;
        my.$breadcrumb = $(options.breadcrumbElement);

        my._templateFileProgress = _.template('<div id="ccm-file-upload-progress" class="ccm-ui"><div id="ccm-file-upload-progress-bar">' +
            '<div class="progress progress-striped active"><div class="progress-bar" style="width: <%=progress%>%;"></div></div>' +
            '</div></div>');

        ConcreteAjaxSearch.call(my, $element, options);

        ConcreteTree.setupTreeEvents();

        my.setupEvents();
        my.setupAddFolder();
        my.setupFolderNavigation();
        my.setupFileUploads();
        my.setupFileDownloads();

    }

    ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

    ConcreteFileManager.prototype.setupRowDragging = function() {
        var my = this,
            $undroppables = my.$element.find('tr[data-file-manager-tree-node-type!=file_folder]');


        // Mobile check, copied from magnific popup
        var appVersion = navigator.appVersion,
            isAndroid = (/android/gi).test(appVersion),
            isIOS = (/iphone|ipad|ipod/gi).test(appVersion),
            probablyMobile = (isAndroid || isIOS || /(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent));

        if (!probablyMobile) {
            my.$element.find('tr[data-file-manager-tree-node-type]').each(function() {
                var $this = $(this),
                    dragClass;
                switch ($(this).attr('data-file-manager-tree-node-type')) {
                    case 'file_folder':
                        dragClass = 'ccm-search-results-folder';
                        break;
                    case 'file':
                        dragClass = 'ccm-search-results-file';
                        break;
                }


                if (dragClass) {

                    $this.draggable({
                        delay: 300,
                        start: function(e) {
                            my.interactionIsDragging = true;
                            $('html').addClass('ccm-search-results-dragging');
                            $undroppables.css('opacity', '0.4');
                            if (e.altKey) {
                                my.$element.addClass('ccm-search-results-copy');
                            }
                            my.$element.find('.ccm-search-select-hover').removeClass('ccm-search-select-hover');
                            $(window).on('keydown.concreteSearchResultsCopy', function(e) {
                                if (e.keyCode == 18) {
                                    my.$element.addClass('ccm-search-results-copy');
                                } else {
                                    my.$element.removeClass('ccm-search-results-copy');
                                }
                            });
                            $(window).on('keyup.concreteSearchResultsCopy', function(e) {
                                if (e.keyCode == 18) {
                                    my.$element.removeClass('ccm-search-results-copy');
                                }
                            });
                        },
                        stop: function() {
                            $('html').removeClass('ccm-search-results-dragging');
                            $(window).unbind('.concreteSearchResultsCopy');
                            $undroppables.css('opacity', '');
                            my.$element.removeClass('ccm-search-results-copy');
                            //$('.ccm-search-result-dragging').removeClass('ccm-search-result-dragging');
                            my.interactionIsDragging = false;
                        },
                        revert: 'invalid',
                        helper: function() {
                            var $selected = my.$element.find('.ccm-search-select-selected');
                            return $('<div class="' + dragClass + ' ccm-draggable-search-item"><span>' + $selected.length + '</span></div>').data('$selected', $selected);
                        },
                        cursorAt: {
                            left: -20,
                            top: 5
                        }
                    });

                }
            });

            my.$element.find('tr[data-file-manager-tree-node-type=file_folder], ol[data-search-navigation=breadcrumb] a[data-file-manager-tree-node]').droppable({
                tolerance: 'pointer',
                hoverClass: 'ccm-search-select-active-droppable',
                drop: function(event, ui) {

                    var $sourceItems = ui.helper.data('$selected'),
                        sourceIDs = [],
                        destinationID = $(this).data('file-manager-tree-node'),
                        copyNodes = event.altKey;

                    $sourceItems.each(function() {
                        var $sourceItem = $(this);
                        var sourceID = $sourceItem.data('file-manager-tree-node');
                        if (sourceID == destinationID) {
                            $sourceItems = $sourceItems.not(this);
                        } else {
                            sourceIDs.push($(this).data('file-manager-tree-node'));
                        }
                    });
                    if (sourceIDs.length === 0) {
                        return;
                    }
                    if (!copyNodes) {
                        $sourceItems.hide();
                    }
                    new ConcreteAjaxRequest({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/tree/node/drag_request',
                        data: {
                            ccm_token: my.options.upload_token,
                            copyNodes: copyNodes ? '1' : 0,
                            sourceTreeNodeIDs: sourceIDs,
                            treeNodeParentID: destinationID
                        },
                        success: function(r) {
                            if (!copyNodes) {
                                my.reloadFolder();
                            }
                            ConcreteAlert.notify({
                                'message': r.message,
                                'title': r.title
                            });
                        },
                        error: function(xhr) {
                            $sourceItems.show();
                            var msg = xhr.responseText;
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                msg = xhr.responseJSON.errors.join("<br/>");
                            }
                            ConcreteAlert.dialog(ccmi18n.error, msg);
                        }
                    });
                }


            });
        }
    };

    ConcreteFileManager.prototype.setupBreadcrumb = function(result) {
        var my = this;


        if (result.breadcrumb) {
            my.$breadcrumb.html('');
            if (result.breadcrumb.length) {
                var $nav = $('<ol data-search-navigation="breadcrumb" class="breadcrumb" />');
                $.each(result.breadcrumb, function(i, entry) {
                    var activeClass = '';
                    if (entry.active) {
                        activeClass = ' class="active"';
                    }

                    var $anchor = $($.parseHTML('<a data-file-manager-tree-node="' + entry.folder + '" href="' + entry.url + '"></a>'));
                    $anchor.text(entry.name);
                    $('<li' + activeClass + '><a data-file-manager-tree-node="' + entry.folder + '" href="' + entry.url + '"></a></li>').append($anchor).appendTo($nav);

                    $nav.find('li.active a').on('click', function(e) {
                        e.stopPropagation();
                        e.preventDefault();
                        if (entry.menu) {
                            var $menu = $(entry.menu);
                            my.showMenu($nav, $menu, e);
                        }
                    });
                });

                $nav.appendTo(my.$breadcrumb);
                $nav.on('click.concreteSearchBreadcrumb', 'a', function() {
                    my.loadFolder($(this).attr('data-file-manager-tree-node'), $(this).attr('href'));
                    return false;
                });
            }
        }
    };

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
            $imageResize = ($maxWidth > 0 && $maxHeight > 0),
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
                imageMaxWidth: ($maxWidth > 0 ? $maxWidth : 1920),
                imageMaxHeight: ($maxHeight > 0 ? $maxHeight : 1080),
                error: function(r) {
                    var message = r.responseText,
                        name = this.files[0].name;

                    try {
                        message = $.parseJSON(message).errors;
                        _(message).each(function(error) {
                            errors.push({ name: name, error: error });
                        });
                    } catch (e) {
                        errors.push({ name: name, error: message });
                    }
                },
                progressall: function(e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#ccm-file-upload-progress-wrapper').html(my._templateFileProgress({ 'progress': progress }));
                },
                start: function() {
                    errors = [];
                    $('<div />', { 'id': 'ccm-file-upload-progress-wrapper' }).html(my._templateFileProgress({ 'progress': 100 })).appendTo(document.body);
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
                done: function(e, data) {
                    files.push(data.result[0]);
                },
                stop: function() {
                    $.fn.dialog.closeTop();

                    if (errors.length) {
                        ConcreteAlert.dialog(ccmi18n_filemanager.uploadFailed, error_template({ errors: errors }));
                    } else {
                        my._launchUploadCompleteDialog(files);
                        files = [];
                    }
                }
            };

        $fileUploader.fileupload(args);

        $fileUploader.bind('fileuploadsubmit', function(e, data) {
            data.formData = {
                currentFolder: my.currentFolder,
                ccm_token: my.options.upload_token
            };
        });

        $('a[data-dialog=add-files]').on('click', function(e) {
            e.preventDefault();
            $.fn.dialog.open({
                width: 620,
                height: 500,
                modal: true,
                title: ccmi18n_filemanager.addFiles,
                href: CCM_DISPATCHER_FILENAME + '/tools/required/files/import?currentFolder=' + my.currentFolder,
                onClose: function() {
                    my.refreshResults();
                }
            });
        });

    };

    ConcreteFileManager.prototype.refreshResults = function(files) {
        var my = this;
        if (this.currentFolder) {
            my.loadFolder(this.currentFolder, false, true);
        } else {
            // re-trigger a file search
            $('div[data-header=file-manager] form').trigger('submit');
        }
    };

    ConcreteFileManager.prototype._launchUploadCompleteDialog = function(files) {
        var my = this;
        ConcreteFileManager.launchUploadCompleteDialog(files, my);
    };

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
    };

    ConcreteFileManager.prototype.setupEvents = function() {
        var my = this;
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
            if (data.form == 'add-folder' || data.form == 'move-to-folder') {
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

        ConcreteEvent.unsubscribe('FileManagerJumpToFolder.concreteTree');
        ConcreteEvent.subscribe('FileManagerJumpToFolder.concreteTree', function(e, r) {
            my.loadFolder(r.folderID);
        });

        ConcreteEvent.unsubscribe('ConcreteTreeDeleteTreeNode.concreteTree');
        ConcreteEvent.subscribe('ConcreteTreeDeleteTreeNode.concreteTree', function(e, r) {
            my.reloadFolder();
        });
        ConcreteEvent.unsubscribe('SavedSearchCreated');
        ConcreteEvent.subscribe('SavedSearchCreated', function(e, data) {
            my.ajaxUpdate(data.search.baseUrl, {});

        });

    };

    ConcreteFileManager.prototype.showMenu = function($element, $menu, event) {
        var my = this;
        var concreteMenu = new ConcreteFileMenu($element, {
            menu: $menu,
            handle: 'none',
            container: my
        });
        concreteMenu.show(event);
    };


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
        }

        // Hide clear if we're not in choose mode
        if (my.options.selectMode != 'choose') {
            var $clear = $menu.find('a[data-file-manager-action=clear]').parent();
            $clear.next('li.divider').remove();
            $clear.remove();
        }

    };

    ConcreteFileManager.prototype.setupBulkActions = function() {
        var my = this;

        // Or, maybe we're using a button launcher
        my.$element.on('click', 'button.btn-menu-launcher', function(event) {
            var $menu = my.getResultMenu(my.getSelectedResults());
            if ($menu) {
                $menu.find('.dialog-launch').dialog();
                var $list = $menu.find('ul');
                $list.attr('data-search-file-menu', $menu.attr('data-search-file-menu'));
                $(this).parent().find('ul').remove();
                $(this).parent().append($list);

                var fileMenu = new ConcreteFileMenu();
                fileMenu.setupMenuOptions($(this).next('ul'));

                ConcreteEvent.publish('ConcreteMenuShow', { menu: my, menuElement: $(this).parent() });
            }
        });
    };

    ConcreteFileManager.prototype.handleSelectedBulkAction = function(value, type, $option, ids) {
        var my = this,
            itemIDs = [];

        if (value == 'choose') {
            ConcreteEvent.publish('FileManagerBeforeSelectFile', { fID: ids });
            ConcreteEvent.publish('FileManagerSelectFile', { fID: ids });
        } else if (value == 'download') {
            $.each(ids, function(i, id) {
                itemIDs.push({ 'name': 'item[]', 'value': id });
            });
            my.$downloadTarget.get(0).src = CCM_TOOLS_PATH + '/files/download?' + $.param(itemIDs);
        } else {
            ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, type, $option, ids);
        }
    };


    ConcreteFileManager.prototype.reloadFolder = function() {
        this.loadFolder(this.currentFolder);
    };

    ConcreteFileManager.prototype.setupAddFolder = function() {
        var my = this;
        $('a[data-launch-dialog=add-file-manager-folder]').on('click', function(e) {
            $('div[data-dialog=add-file-manager-folder] input[name=currentFolder]').val(my.currentFolder);
            $('div[data-dialog=add-file-manager-folder] input[name=folderName]').val('');

            $.fn.dialog.open({
                element: 'div[data-dialog=add-file-manager-folder]',
                modal: true,
                width: 320,
                title: 'Add Folder',
                height: 'auto'
            });

            $('div[data-dialog=add-file-manager-folder]').on('dialogopen', function() {
                var $this = $(this);
                $this.off('dialogopen');
                $this.find('[autofocus]').focus();
            });
            e.preventDefault();
        });
    };

    ConcreteFileManager.prototype.setupFolderNavigation = function() {
        $('a[data-launch-dialog=navigate-file-manager]').on('click', function(e) {
            e.preventDefault();
            $.fn.dialog.open({
                width: '560',
                height: '500',
                modal: true,
                title: ccmi18n_filemanager.jumpToFolder,
                href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/jump_to_folder',
            });
        });
    };

    ConcreteFileManager.prototype.hoverIsEnabled = function($element) {
        var my = this;
        return !my.interactionIsDragging;
    };


    ConcreteFileManager.prototype.updateResults = function(result) {
        var my = this;
        ConcreteAjaxSearch.prototype.updateResults.call(my, result);
        my.setupFolders(result);
        my.setupBreadcrumb(result);
        my.setupRowDragging();

        if (my.options.selectMode == 'choose') {
            my.$element.unbind('.concreteFileManagerHoverFile');
            my.$element.on('mouseover.concreteFileManagerHoverFile', 'tr[data-file-manager-tree-node-type]', function() {
                $(this).addClass('ccm-search-select-hover');
            });
            my.$element.on('mouseout.concreteFileManagerHoverFile', 'tr[data-file-manager-tree-node-type]', function() {
                $(this).removeClass('ccm-search-select-hover');
            });
            my.$element.unbind('.concreteFileManagerChooseFile').on('click.concreteFileManagerChooseFile', 'tr[data-file-manager-tree-node-type=file]', function(e) {
                ConcreteEvent.publish('FileManagerBeforeSelectFile', { fID: $(this).attr('data-file-manager-file') });
                ConcreteEvent.publish('FileManagerSelectFile', { fID: $(this).attr('data-file-manager-file') });
                my.$downloadTarget.remove();
                return false;
            });
            my.$element.unbind('.concreteFileManagerOpenFolder').on('click.concreteFileManagerOpenFolder', 'tr[data-file-manager-tree-node-type=search_preset],tr[data-file-manager-tree-node-type=file_folder]', function(e) {
                e.preventDefault();
                my.loadFolder($(this).attr('data-file-manager-tree-node'));
            });
        }

    };

    ConcreteFileManager.prototype.loadFolder = function(folderID, url, showRecentFirst) {
        var my = this;
        var data = my.getSearchData();
        if (!url) {
            url = my.options.result.baseUrl;
        } else {
            // dynamically update baseUrl because we're coming to this folder via
            // something like the breadcrumb
            my.options.result.baseUrl = url; // probably a nicer way to do this
        }
        data.push({ 'name': 'folder', 'value': folderID });

        if (my.options.result.filters) {
            // We are loading a folder with a filter. So we loop through the fields
            // and add them to data.
            $.each(my.options.result.filters, function(i, field) {
                var fieldData = field.data;
                data.push({ 'name': 'field[]', 'value': field.key });
                for (var key in fieldData) {
                    data.push({ 'name': key, 'value': fieldData[key] });
                }
            });
        }

        if (showRecentFirst) {
            data.push({ 'name': 'ccm_order_by', 'value': 'folderItemModified' });
            data.push({ 'name': 'ccm_order_by_direction', 'value': 'desc' });
        }

        my.currentFolder = folderID;
        my.ajaxUpdate(url, data);

        my.$element.find('#ccm-file-manager-upload input[name=currentFolder]').val(my.currentFolder);
    };

    ConcreteFileManager.prototype.getResultMenu = function(results) {
        var my = this;
        var $menu = ConcreteAjaxSearch.prototype.getResultMenu.call(this, results);
        if ($menu) {
            my.activateMenu($menu);
        }
        return $menu;
    };

    /**
     * Static Methods
     */
    ConcreteFileManager.launchDialog = function(callback, opts) {
        var w = $(window).width() - 100;
        var data = {};
        var i;

        var options = {
            filters: [],
            multipleSelection: false, // Multiple selection switch
        };

        $.extend(options, opts);

        if (options.filters.length > 0) {
            data['field\[\]'] = [];

            for (i = 0; i < options.filters.length; i++) {
                var filter = $.extend(true, {}, options.filters[i]); // clone
                data['field\[\]'].push(filter.field);
                delete(filter.field);
                $.extend(data, filter); // add all remaining fields to the data
            }
        }

        $.fn.dialog.open({
            width: w,
            height: '80%',
            href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/search',
            modal: true,
            data: data,
            title: ccmi18n_filemanager.title,
            onOpen: function(dialog) {
                ConcreteEvent.unsubscribe('FileManagerSelectFile');
                ConcreteEvent.subscribe('FileManagerSelectFile', function(e, data) {
                    var multipleItemsSelected = (Object.prototype.toString.call(data.fID) === '[object Array]');
                    if (options.multipleSelection && !multipleItemsSelected) {
                        data.fID = [data.fID];
                    } else if (!options.multipleSelection && multipleItemsSelected) {
                        if (data.fID.length > 1) {
                            $('.ccm-search-bulk-action option:first-child').prop('selected', 'selected');
                            window.alert(ccmi18n_filemanager.chosenTooMany);
                            return;
                        }
                        data.fID = data.fID[0];
                    }
                    $.fn.dialog.closeTop();
                    callback(data);
                });
            }
        });
    };

    ConcreteFileManager.getFileDetails = function(fID, callback) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/get_json',
            data: { 'fID': fID },
            error: function(r) {
                ConcreteAlert.dialog(ccmi18n.error, r.responseText);
            },
            success: function(r) {
                callback(r);
            }
        });
    };


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
                    var data = { filemanager: my };
                    ConcreteEvent.publish('FileManagerUploadCompleteDialogClose', data);
                },
                onOpen: function() {
                    var data = { filemanager: my };
                    ConcreteEvent.publish('FileManagerUploadCompleteDialogOpen', data);
                },
                title: ccmi18n_filemanager.uploadComplete
            });
        }
    };


    $.fn.concreteFileManager = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteFileManager($(this), options);
        });
    };

    global.ConcreteFileManager = ConcreteFileManager;
    //global.ConcreteFileManagerMenu = ConcreteFileManagerMenu;

})(window, jQuery);
