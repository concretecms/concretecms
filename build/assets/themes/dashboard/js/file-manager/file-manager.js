
;(function(global, $) {
    'use strict'

    function ConcreteFileManagerTable($element, options) {
        var my = this
        options = options || {}
        options = $.extend({
            bulkParameterName: 'fID',
            folderID: 0,
            highlightFolders: []
        }, options)

        my.$element = $element
        my.options = options

        ConcreteSearchResultsTable.call(my, $element, options)

        my.setupFileDownloads()
        my.activateSelectAllCheckbox()
        my.activateIndividualCheckboxes()
        my.disableSelectAllOnInvalidNodeTypeSelection()
        my.setupFileUploads()
        my.setupFileEvents()
        my.setupBulkActions()
        my.setupFolderActions()
        my.setupFavoriteFolderActions()
    }

    ConcreteFileManagerTable.prototype = Object.create(ConcreteSearchResultsTable.prototype)

    ConcreteFileManagerTable.prototype.setupFavoriteFolderActions = function () {
        var $favoriteFolderSelector = $("#favoriteFolderSelector")

        // bind the change handler for the favorite folder selector
        $favoriteFolderSelector.change(function(e) {
            e.preventDefault()
            var favoriteFolderId = $(this).val()
            window.location.href = CCM_DISPATCHER_FILENAME + "/dashboard/files/search/folder/" + favoriteFolderId
        })

        $(".ccm-favorite-folder-switch").change(function() {
            var favoriteFolderId = $(this).val()

            if ($(this).is(":checked")) {
                // add folder to users favorite list
                new ConcreteAjaxRequest({
                    url: CCM_DISPATCHER_FILENAME + "/ccm/system/file/add_favorite_folder/" + favoriteFolderId,
                    success: function() {
                        ConcreteEvent.publish('FileManagerRefreshFavoriteFolderList')
                    }
                })
            } else {
                // add folder to users favorite list
                new ConcreteAjaxRequest({
                    url: CCM_DISPATCHER_FILENAME + "/ccm/system/file/remove_favorite_folder/" + favoriteFolderId,
                    success: function() {
                        ConcreteEvent.publish('FileManagerRefreshFavoriteFolderList')
                    }
                })
            }
        })

        ConcreteEvent.subscribe('FileManagerRefreshFavoriteFolderList', function() {
            // fetch user favorite folders and render list
            new ConcreteAjaxRequest({
                url: CCM_DISPATCHER_FILENAME + "/ccm/system/file/get_favorite_folders",
                success: function(response) {
                    if (Object.keys(response.favoriteFolders).length) {
                        $favoriteFolderSelector.selectpicker('show')
                        $favoriteFolderSelector.html("")

                        for(var favoriteFolderId in response.favoriteFolders) {
                            var favoriteFolderName = response.favoriteFolders[favoriteFolderId]

                            var $favoriteFolderOptionItem = $("<option></option>")
                                .attr("value", favoriteFolderId)
                                .attr("data-icon", "fa fa-folder")
                                .html(favoriteFolderName)

                            $favoriteFolderSelector.append($favoriteFolderOptionItem)
                        }

                        $favoriteFolderSelector.selectpicker('refresh')
                    } else {
                        $favoriteFolderSelector.selectpicker('hide')
                    }
                }
            })
        })

        // load list on page start up
        $favoriteFolderSelector.selectpicker('refresh')
        ConcreteEvent.publish('FileManagerRefreshFavoriteFolderList')
    }

    ConcreteFileManagerTable.prototype.setupFileEvents = function () {
        // Single file event
        ConcreteEvent.subscribe('ConcreteDeleteFile', function() {
            window.location.reload()
        })
        // Bulk file event
        ConcreteEvent.subscribe('FileManagerDeleteFilesComplete', function() {
            window.location.reload()
        })
        // File Folder
        ConcreteEvent.subscribe('ConcreteTreeDeleteTreeNode', function() {
            window.location.reload()
        })
    }

    ConcreteFileManagerTable.prototype.disableSelectAllOnInvalidNodeTypeSelection = function() {
        var my = this
        my.$element.on('change', 'input[data-search-checkbox=individual]', function() {
            var deactivate = false,
                $checked = my.$element.find(my.checkedCheckboxes)
            if ($checked.length) {
                $checked.each(function () {
                    if ($(this).data('node-type') != 'file') {
                        deactivate = true;
                    }
                });
                if (deactivate) {
                    my.$selectAllDropdownButton.prop('disabled', true)
                } else {
                    my.$selectAllDropdownButton.prop('disabled', false)
                }
            }
        })
    }

    ConcreteFileManagerTable.prototype.setupFileUploads = function() {
        var my = this
        my.fileUploaderOptions = {
            folderID: function() {
                return my.options.folderID
            }
        }

        my.$element.parent().concreteFileUploader(my.fileUploaderOptions);
    };

    ConcreteFileManagerTable.prototype.setupFolderActions = function() {
        var my = this
        ConcreteEvent.subscribe('FileManagerJumpToFolder.concreteFileManagerTable', function(e, r) {
            var url = CCM_DISPATCHER_FILENAME + '/dashboard/files/search/folder/' + r.folderID
            window.location.href = url
        });
        ConcreteEvent.subscribe('FileManagerAddFilesComplete.concreteFileManagerTable', function(e, r) {
            // Now it's time for us to get the redirect response in order to go to
            // the proper spot in the file manager.
            const fileIds = []

            r.files.forEach(function(file) {
                fileIds.push(file.fID)
            })

            $.concreteAjax({
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/upload_complete',
                method: 'POST',
                data: {
                    ccm_token: CCM_SECURITY_TOKEN,
                    fID: fileIds,
                },
                dataType: 'json',
                success: function (response) {
                    window.location.href = response.redirectURL
                }
            })

        });
    };

    ConcreteFileManagerTable.prototype.activateSearchResultMenus = function() {
        var my = this;
        my.$searchResultMenu.find('a[data-file-manager-action=download]').on('click', function(e) {
            e.preventDefault()
            var fID = $(this).data('file-id');
            var fUUID = $("input[data-item-id=" +fID + "]").data("item-uuid")
            var downloadIdentifier
            if (fUUID) {
                downloadIdentifier = fUUID
            } else {
                downloadIdentifier = fID
            }
            my.$downloadTarget.get(0).src = CCM_DISPATCHER_FILENAME + '/ccm/system/file/download?fID=' + downloadIdentifier
        })
        my.$searchResultMenu.find('a[data-file-manager-action=duplicate]').on('click', function() {
            var fID = $(this).data('file-id');
            $.concreteAjax({
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/duplicate',
                data: {
                    token: CCM_SECURITY_TOKEN,
                    fID: fID
                },
                success: function(r) {
                    window.location.reload();
                }
            })
            return false
        })
        ConcreteTree.activateMenu(my.$searchResultMenu);
    }


    ConcreteFileManagerTable.prototype.setupFileDownloads = function() {
        var my = this
        if (!$('#ccm-file-manager-download-target').length) {
            my.$downloadTarget = $('<iframe />', {
                name: 'ccm-file-manager-download-target',
                id: 'ccm-file-manager-download-target'
            }).appendTo(document.body)
        } else {
            my.$downloadTarget = $('#ccm-file-manager-download-target')
        }
    }

    ConcreteFileManagerTable.prototype.handleSelectedBulkAction = function(value, type, $option, ids) {
        var my = this
        var itemIDs = []
        if (value == 'download') {
            $.each(ids, function(i, id) {
                var uuid = $("input[data-item-id=" + id + "]").data("item-uuid")
                itemIDs.push({ name: 'fID[]', value: uuid ? uuid : id })
            })
            my.$downloadTarget.get(0).src = CCM_DISPATCHER_FILENAME + '/ccm/system/file/download?' + $.param(itemIDs)
        } else {
            ConcreteSearchResultsTable.prototype.handleSelectedBulkAction.call(this, value, type, $option, ids)
        }
    }

    // jQuery Plugin
    $.fn.concreteFileManagerTable = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteFileManagerTable($(this), options)
        })
    }

    global.ConcreteSearchResultsTable = ConcreteSearchResultsTable
})(window, jQuery);
