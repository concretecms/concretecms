!function(global, $) {
    'use strict';

    function ConcreteFileManager($element, options) {
        'use strict';
        var my = this;
        options = $.extend({
            'breadcrumbElement': 'div.ccm-search-results-breadcrumb',
            'bulkParameterName': 'fID',
            'searchMethod': 'get',
            'selectMode': 'multiple' // Enables multiple advanced item selection, range click, etc
        }, options);

        my.currentFolder = 0;
        my.interactionIsDragging = false;
        my.$breadcrumb = $(options.breadcrumbElement);
        my.$headerSearchInput = $element.find('div[data-header=file-manager] input');
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

    ConcreteFileManager.prototype.setupAdvancedSearch = function() {
        var my = this;
        my.$advancedSearchButton.on('click', function() {
            var url = $(this).attr('href');
            $.fn.dialog.open({
                width: 620,
                height: 500,
                href: url,
                modal: true,
                title: ccmi18n.search,
                onOpen: function() {
                    my.setupAdvancedSearchDialog();
                }
            });
            return false;
        });
    }

    ConcreteFileManager.prototype.setupRowDragging = function() {
        var my = this,
            currentItems,
            $undroppables = my.$element.find('tr[data-file-manager-tree-node-type!=file_folder]');


        my.$element.find('tr[data-file-manager-tree-node-type]').each(function() {
            var $this = $(this), dragClass;
            switch($(this).attr('data-file-manager-tree-node-type')) {
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
                            $sourceItems.remove();
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

    ConcreteFileManager.prototype.setupAdvancedSearchDialog = function() {
        var my = this;
        var $container = $('div[data-container=search-fields]');
        var renderFieldRowTemplate = _.template(
            $('script[data-template=search-field-row]').html()
        );
        var defaultQuery = $('script[data-template=default-query]').html();
        if (defaultQuery) {
            defaultQuery = JSON.parse(defaultQuery);
        }
        $('button[data-button-action=add-field]').on('click', function() {
            $container.append(
                renderFieldRowTemplate()
            );
        });

        if (my.result.query) {
            $.each(my.result.query.fields, function(i, field) {
                $container.append(
                    renderFieldRowTemplate({'field': field})
                );
            });
        } else if (defaultQuery) {
            $.each(defaultQuery.fields, function(i, field) {
                $container.append(
                    renderFieldRowTemplate({'field': field})
                );
            });
        }

        $container.find('select.selectize-select').selectize();
        $container.on('change', 'select.ccm-search-choose-field', function() {
            var key = $(this).val();
            var $content = $(this).parent().find('div.ccm-search-field-content');
            if (key) {
                $.concreteAjax({
                    url: $(this).attr('data-action'),
                    data: {
                        'field': key
                    },
                    success: function(r) {
                        $content.html(r.element);
                        $content.find('select.selectize-select').selectize();
                    }
                });
            }
        });
        $container.on('click', 'a[data-search-remove=search-field]', function(e) {
            e.preventDefault();
            var $row = $(this).parent();
            $row.remove();
        });

        $('button[data-button-action=save-search-preset]').on('click.saveSearchPreset', function() {
            jQuery.fn.dialog.open({
                element: 'div[data-dialog=save-search-preset]:first',
                modal: true,
                width: 320,
                title: 'Save Preset',
                height: 'auto'
            });
        });

        var $presetForm = $('form[data-form=save-preset]');
        var $form = $('form[data-advanced-search-form]');
        $('button[data-button-action=save-search-preset-submit]').on('click.saveSearchPresetSubmit', function() {
            var $presetForm = $('form[data-form=save-preset]');
            $presetForm.trigger('submit');
        });

        $presetForm.on('submit', function() {
            var formData = $form.serializeArray();
            formData = formData.concat($presetForm.serializeArray());
            $.concreteAjax({
                data: formData,
                url: $presetForm.attr('action'),
                success: function(r) {
                    jQuery.fn.dialog.closeAll();
                    ConcreteEvent.publish('SavedSearchCreated', {search: r});
                }
            });
            return false;
        });

        my.setupSearch();
    }

    ConcreteFileManager.prototype.setupBreadcrumb = function(result) {
        var my = this;

        // If we're calling this from a dialog, we move it out to the top of the dialog so it can display properly
        var $container = my.$element.closest('div.ui-dialog').find('.ui-dialog-titlebar');
        if ($container.length) {
            my.$breadcrumb.appendTo($container);
        }

        if (result.breadcrumb) {
            my.$breadcrumb.html('');
            var $nav = $('<ol data-search-navigation="breadcrumb" class="breadcrumb" />');
            $.each(result.breadcrumb, function(i, entry) {
                var activeClass = '';
                if (entry.active) {
                    activeClass = ' class="active"';
                }
                $nav.append('<li' + activeClass + '><a data-file-manager-tree-node="' + entry.folder + '" href="' + entry.url + '">' + entry.name + '</a></li>');
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

    ConcreteFileManager.prototype.setupResetButton = function(result) {
        var my = this;
        if (result.query) {
            my.$headerSearchInput.prop('disabled', true);
            my.$headerSearchInput.attr('placeholder', '');
            my.$advancedSearchButton.html(ccmi18n_filemanager.edit);
            my.$resetSearchButton.show();
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

        $fileUploader.fileupload(args);

        $fileUploader.bind('fileuploadsubmit', function (e, data) {
            data.formData = {
                currentFolder: my.currentFolder,
                ccm_token: my.options.upload_token
            }
        });

        $('a[data-dialog=add-files]').on('click', function() {
            $.fn.dialog.open({
                width: 620,
                height: 500,
                modal: true,
                title: ccmi18n_filemanager.addFiles,
                href: CCM_DISPATCHER_FILENAME + '/tools/required/files/import?currentFolder=' + my.currentFolder
            });
        });

    };

    ConcreteFileManager.prototype.refreshResults = function(files) {
        var my = this;
        my.reloadFolder();

    }

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
        ConcreteEvent.unsubscribe('SavedSearchCreated');
        ConcreteEvent.subscribe('SavedSearchCreated', function(e, data) {
            my.ajaxUpdate(data.search.baseUrl, {});

        });

    }

    ConcreteFileManager.prototype.showMenu = function($element, $menu, event) {
        var my = this;
        var concreteMenu = new ConcreteFileMenu($element, {
            menu: $menu,
            handle: 'none',
            container: my
        });
        concreteMenu.show(event);
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
        }

        // Hide clear if we're not in choose mode
        if (my.options.selectMode != 'choose') {
            var $clear = $menu.find('a[data-file-manager-action=clear]').parent();
            $clear.next('li.divider').remove();
            $clear.remove();
        }

    }

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

                var fileMenu = new ConcreteFileMenu(false, {'container': my});
                fileMenu.setupMenuOptions($list);

                ConcreteEvent.publish('ConcreteMenuShow', {menu: my, menuElement: $(this).parent()});
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

    ConcreteFileManager.prototype.hoverIsEnabled = function($element) {
        var my = this;
        return !my.interactionIsDragging;
    }


    ConcreteFileManager.prototype.updateResults = function(result) {
        var my = this;
        ConcreteAjaxSearch.prototype.updateResults.call(my, result);
        my.setupFolders(result);
        my.setupBreadcrumb(result);
        my.setupResetButton(result);
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
                ConcreteEvent.publish('FileManagerBeforeSelectFile', {fID: $(this).attr('data-file-manager-file')});
                ConcreteEvent.publish('FileManagerSelectFile', {fID: $(this).attr('data-file-manager-file')});
                my.$downloadTarget.remove();
                return false;
            });
            my.$element.unbind('.concreteFileManagerOpenFolder').on('click.concreteFileManagerOpenFolder', 'tr[data-file-manager-tree-node-type=search_preset],tr[data-file-manager-tree-node-type=file_folder]', function(e) {
                e.preventDefault();
                my.loadFolder($(this).attr('data-file-manager-tree-node'));
            });
        }

    }

    ConcreteFileManager.prototype.loadFolder = function(folderID, url) {
        var my = this;
        var data = my.getSearchData();
        if (!url) {
            var url = my.options.result.baseUrl;
        } else {
            // dynamically update baseUrl because we're coming to this folder via
            // something like the breadcrumb
            my.options.result.baseUrl = url; // probably a nicer way to do this
        }
        data.push({'name': 'folder', 'value': folderID});
        my.currentFolder = folderID;
        my.ajaxUpdate(url, data);
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
            my.$resetSearchButton.addClass('ccm-file-manager-reset-search-right').show();

            return false;
        });

        // If we're calling this from a dialog, we move it out to the top of the dialog so it can display properly
        var $container = my.$element.closest('div.ui-dialog');
        if ($container.length) {
            my.$element.find('div[data-header=file-manager]').appendTo($container);
        }

        $('form[data-advanced-search-form]').concreteAjaxForm({
            'success': function(r) {
                my.updateResults(r);
                jQuery.fn.dialog.closeTop();
                my.$advancedSearchButton.html(ccmi18n_filemanager.edit);
                my.$resetSearchButton.show();
                my.$headerSearchInput.prop('disabled', true).val('');
                my.$headerSearchInput.attr('placeholder', '');
            }
        });
        my.$resetSearchButton.on('click', function(e) {
            my.$element.find('div[data-header=file-manager] input').val('');
            e.preventDefault();
            $.concreteAjax({
                url: $(this).attr('data-button-action-url'),
                success: function(r) {
                    my.updateResults(r);
                    my.$headerSearchInput.prop('disabled', false);
                    my.$headerSearchInput.attr('placeholder', ccmi18n.search);
                    my.$advancedSearchButton.html(ccmi18n.advanced).show();
                    my.$resetSearchButton.removeClass('ccm-file-manager-reset-search-right').hide();
                }
            });
        });
    }

    /**
     * Static Methods
     */
    ConcreteFileManager.launchDialog = function(callback, opts ) {
        var w = $(window).width() - 53;
        var data = {};
        var i;

        var options = {
            filters: [],
            multipleSelection: false, // Multiple selection switch
        };

        $.extend(options, opts);

        if ( options.filters.length > 0 )
        {
            data['field\[\]'] = [];

            for ( i = 0; i < options.filters.length; i++ )
            {
                var filter = $.extend(true, {}, options.filters[i] ); // clone
                data['field\[\]'].push(filter.field);
                delete ( filter.field );
                $.extend( data, filter); // add all remaining fields to the data
            }
        }

        $.fn.dialog.open({
            width: w,
            height: '100%',
            href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/search',
            modal: true,
            data: data,
            title: ccmi18n_filemanager.title,
            onOpen: function(dialog) {
                ConcreteEvent.unsubscribe('FileManagerSelectFile');
                ConcreteEvent.subscribe('FileManagerSelectFile', function(e, data) {
                    var multipleItemsSelected = (Object.prototype.toString.call( data.fID ) === '[object Array]');
                    if (options.multipleSelection && !multipleItemsSelected) {
                        data.fID = [data.fID];
                    } else if (!options.multipleSelection && multipleItemsSelected) {
                        if (data.fID.length > 1) {
                            $('.ccm-search-bulk-action option:first-child').prop('selected', 'selected');
                            alert(ccmi18n_filemanager.chosenTooMany);
                            return;
                        }
                        data.fID = data.fID[0];
                    }
                    jQuery.fn.dialog.closeTop();
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
            data: {'fID': fID},
            error: function(r) {
                ConcreteAlert.dialog('Error', r.responseText);
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
