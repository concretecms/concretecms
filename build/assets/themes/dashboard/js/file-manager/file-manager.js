import Dropzone from '../../../../../node_modules/dropzone/dist/dropzone';


;(function(global, $) {
    'use strict'

    function ConcreteFileManagerTable($element, options) {
        var my = this
        options = options || {}
        options = $.extend({
            bulkParameterName: 'fID',
            folderID: 0,
        }, options)

        my.$element = $element
        my.options = options

        ConcreteSearchResultsTable.call(my, $element, options)

        my.setupFileDownloads()
        my.activateSelectAllCheckbox()
        my.activateIndividualCheckboxes()
        my.disableSelectAllOnInvalidNodeTypeSelection()
        my.setupFileUploads()
        my.setupBulkActions()
    }

    ConcreteFileManagerTable.prototype = Object.create(ConcreteSearchResultsTable.prototype)

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


    ConcreteFileManagerTable.prototype.activateSearchResultMenus = function() {
        var my = this;
        my.$searchResultMenu.find('a[data-file-manager-action=download]').on('click', function(e) {
            var fID = $(this).data('file-id');
            e.preventDefault()
            window.frames['ccm-file-manager-download-target'].location =
                CCM_DISPATCHER_FILENAME + '/ccm/system/file/download?fID=' + fID
        })
        my.$searchResultMenu.find('a[data-file-manager-action=duplicate]').on('click', function() {
            var fID = $(this).data('file-id');
            $.concreteAjax({
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/duplicate',
                data: { fID: fID },
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
                itemIDs.push({ name: 'fID[]', value: id })
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
