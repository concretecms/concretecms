/* global ccmi18n_fileuploader, CCM_SECURITY_TOKEN, NProgress, ConcreteEvent */
/* eslint indent: [2, 4, {"SwitchCase": 1}] */

(function ($) {
    $.fn.concreteFileUploader = function (options) {
        options = options || {}
        const $container = $(this)
        let fileUploader = $container.data('fileUploader')

        if (typeof fileUploader === 'undefined') {
            const $dialogEl = $('<div/>')
                .addClass('ccm-ui')

            $('body').append($dialogEl)

            fileUploader = {
                options: null,
                inProgress: false,
                isCompleted: false,
                dropzone: null,
                isDialogOpen: false,
                idleTimer: null,
                forceClose: false,
                uploadedFiles: [],

                templates: {
                    getIncomingFiles: function (incomingPath, incomingStorageLocation, files) {
                        if (typeof files === 'undefined' || files.length === 0) {
                            // noinspection JSUnresolvedVariable
                            return $('<p/>').html(
                                ccmi18n_fileuploader.noFilesFound
                                    .replace('{0}', incomingStorageLocation)
                                    .replace('{1}', incomingPath)
                            )
                        } else {
                            const $wrapper = $('<div/>')
                            const $c = $('<div/>')
                                .addClass('ccm-incoming-files-container')

                            const $table = $('<table/>')
                                .addClass('table table-striped incoming_file_table')

                            const $thead = $('<thead/>')

                            let $tr = $('<tr/>')

                            let $checkbox = $('<input/>')
                                .attr('type', 'checkbox')
                                .addClass('ccm-check-all-incoming')

                            const $th1 = $('<th/>')
                                .addClass('ccm-incoming-files-checkbox')

                            if (typeof fileUploader.options.formData.fID === 'undefined') {
                                $th1
                                    .append($checkbox)
                            }

                            const $th2 = $('<th/>')
                                .addClass('ccm-incoming-files-thumbnail')

                            const $th3 = $('<th/>')
                                .addClass('ccm-incoming-files-filename')
                                .html(ccmi18n_fileuploader.filename)

                            const $th4 = $('<th/>')
                                .addClass('ccm-incoming-files-size')
                                .html(ccmi18n_fileuploader.size)

                            $tr.append($th1)
                            $tr.append($th2)
                            $tr.append($th3)
                            $tr.append($th4)

                            $thead.append($tr)

                            $table.append($thead)

                            const $tbody = $('<tbody/>')

                            for (const file of files) {
                                $tr = $('<tr/>')

                                const checkboxId = 'input-' + fileUploader.getUniqueId()

                                const $checkbox = $('<input/>')
                                    .attr('name', 'send_file[]')
                                    .attr('type', 'checkbox')
                                    .attr('id', checkboxId)
                                    .addClass('ccm-file-select-incoming')
                                    .val(file.basename)

                                if (typeof fileUploader.options.formData.fID !== 'undefined') {
                                    $checkbox.attr('type', 'radio')
                                }

                                if (!file.allowed) {
                                    // noinspection JSUnresolvedVariable
                                    $checkbox
                                        .attr('disabled', 'disabled')
                                        .addClass('launch-tooltip')
                                        .attr('title', ccmi18n_fileuploader.invalidFileExtension)
                                }

                                const $td1 = $('<td/>')
                                    .addClass('ccm-incoming-files-checkbox')
                                    .append($checkbox)

                                let $label = $('<label/>')
                                    .attr('for', checkboxId)
                                    .html(file.thumbnail)

                                const $td2 = $('<td/>')
                                    .addClass('ccm-incoming-files-thumbnail')
                                    .append($label)

                                $label = $('<label/>')
                                    .attr('for', checkboxId)
                                    .html(file.basename)

                                if (!file.allowed) {
                                    // noinspection JSUnresolvedVariable
                                    $label
                                        .addClass('text-danger launch-tooltip')
                                        .attr('title', ccmi18n_fileuploader.invalidFileExtension)
                                }

                                const $td3 = $('<td/>')
                                    .addClass('ccm-incoming-files-filename')
                                    .append($label)

                                // noinspection JSUnresolvedVariable
                                const $td4 = $('<td/>')
                                    .addClass('ccm-incoming-files-size')
                                    .html(file.displaySize)

                                $tr.append($td1)
                                $tr.append($td2)
                                $tr.append($td3)
                                $tr.append($td4)

                                $tbody.append($tr)
                            }

                            $table.append($tbody)

                            const checkboxId = 'input-' + fileUploader.getUniqueId()

                            const $formCheck = $('<div/>')
                                .addClass('form-check')

                            $checkbox = $('<input/>')
                                .attr('name', 'removeFilesAfterPost')
                                .attr('type', 'checkbox')
                                .attr('id', checkboxId)
                                .attr('value', '1')
                                .addClass('form-check-input')

                            const $label = $('<label/>')
                                .attr('for', checkboxId)
                                .addClass('form-check-label')
                                .html(
                                    ccmi18n_fileuploader.removeFilesAfterPost
                                        .replace('{0}', '<code>' + incomingPath + '</code>')
                                        .replace('{1}', incomingStorageLocation)
                                )

                            $formCheck.append($checkbox)
                            $formCheck.append($label)

                            $c.append($table)
                            $wrapper.append($c)
                            $wrapper.append($formCheck)

                            return $wrapper
                        }
                    },

                    getDialog: function () {
                        const $row = $('<div/>')
                            .addClass('row')

                        const $column = $('<div/>')
                            .addClass('col-sm-12')

                        /*
                         * add tab navigation
                         */

                        // noinspection JSUnresolvedVariable
                        const tabs = [
                            {
                                id: 'your-computer',
                                label: ccmi18n_fileuploader.yourComputer
                            },

                            {
                                id: 'incoming-directory',
                                label: ccmi18n_fileuploader.incomingDirectory
                            },

                            {
                                id: 'remote-files',
                                label: ccmi18n_fileuploader.remoteFiles
                            }
                        ]

                        const $ul = $('<ul/>')
                            .attr('role', 'tablist')
                            .attr('id', 'ccm-file-import-tab-menu')
                            .addClass('nav nav-tabs mb-3 nav-fill')

                        let tab = {}

                        for (tab of tabs) {
                            const $li = $('<li/>')
                                .addClass('nav-item')

                            const $a = $('<a/>')
                                .attr('data-toggle', 'tab')
                                .attr('href', '#' + tab.id)
                                .attr('aria-controls', tab.id)
                                .attr('aria-selected', false)
                                .attr('role', 'tab')
                                .attr('id', tab.id + '-tab')
                                .addClass('nav-link')
                                .html(tab.label)

                            const isActive = tab.id === 'your-computer'

                            if (isActive) {
                                $a.attr('aria-selected', true).addClass('active')
                            }

                            $li.append($a)
                            $ul.append($li)
                        }

                        $column.append($ul)

                        const $tabContent = $('<div/>')
                            .addClass('tab-content')

                        /*
                         * add your computer tab
                         */

                        const $yourComputerTab = $('<div/>')
                            .attr('id', 'your-computer')
                            .attr('aria-labelledby', 'your-computer-tab')
                            .attr('role', 'tabpanel')
                            .addClass('tab-pane fade show active')

                        const $fileUploadContainerWrapper = $('<div/>')
                            .addClass('ccm-file-upload-container-wrapper')

                        const $fileUploadContainer = $('<div/>')
                            .addClass('ccm-file-upload-container')

                        const $dzMessage = $('<div/>')
                            .addClass('dz-default dz-message')

                        const $button = $('<button/>')
                            .attr('type', 'button')
                            .addClass('dz-button')

                        const dataUri =
                            'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b' +
                            '3I6IEFkb2JlIElsbHVzdHJhdG9yIDI0LjEuMiwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVp' +
                            'bGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkViZW5lXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDA' +
                            'wL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZX' +
                            'dCb3g9IjAgMCAxMzIgMTMyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxMzIgMTMyOyIgeG1sOnNwYWNlP' +
                            'SJwcmVzZXJ2ZSI+CjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+Cgkuc3Qwe29wYWNpdHk6MC45O2ZpbGwtcnVsZTpldmVub2Rk' +
                            'O2NsaXAtcnVsZTpldmVub2RkO2ZpbGw6I0U2RjVGRjtlbmFibGUtYmFja2dyb3VuZDpuZXcgICAgO30KCS5zdDF7ZmlsbDo' +
                            'jNEE5MEUyO30KCS5zdDJ7ZmlsbDpub25lO3N0cm9rZTojRThGNkZGO3N0cm9rZS13aWR0aDo4O30KPC9zdHlsZT4KPGcgaW' +
                            'Q9IlBhZ2UtMSI+Cgk8ZyBpZD0iRmlsZS1NYW5hZ2VyLS0tRmlsZVNldHMtRHJvcGRvd24iIHRyYW5zZm9ybT0idHJhbnNsY' +
                            'XRlKC02NTUuMDAwMDAwLCAtNjY3LjAwMDAwMCkiPgoJCTxyZWN0IGlkPSJSZWN0YW5nbGUiIHg9IjEiIHk9IjQ2OCIgY2xh' +
                            'c3M9InN0MCIgd2lkdGg9IjE0NDAiIGhlaWdodD0iNTQwIi8+CgkJPGcgaWQ9Imljb25zOC1kcmFnLWFuZC1kcm9wIiB0cmF' +
                            'uc2Zvcm09InRyYW5zbGF0ZSg2NTUuMDAwMDAwLCA2NjcuMDAwMDAwKSI+CgkJCTxwYXRoIGlkPSJTaGFwZSIgY2xhc3M9In' +
                            'N0MSIgZD0iTTMuMiwwYzAsMC0wLjEsMC0wLjEsMEMzLjEsMCwzLDAsMi45LDBDMi44LDAsMi43LDAuMSwyLjYsMC4xQzIuN' +
                            'CwwLjIsMi4xLDAuMiwxLjgsMC4zCgkJCQlDMS43LDAuNCwxLjUsMC41LDEuNCwwLjZDMS4yLDAuOCwxLDAuOSwwLjksMUMw' +
                            'LjgsMS4xLDAuNywxLjMsMC42LDEuNEMwLjUsMS42LDAuMywxLjgsMC4yLDIuMUMwLjIsMi4yLDAuMiwyLjQsMC4xLDIuNQo' +
                            'JCQkJQzAuMSwyLjcsMCwyLjksMCwzLjF2MTMuMWMwLDEuNywxLjQsMy4xLDMuMiwzLjFzMy4yLTEuNCwzLjItMy4xVjYuNG' +
                            'gxM2MxLjcsMCwzLjEtMS40LDMuMS0zLjJTMjEuMSwwLDE5LjQsMEgzLjUKCQkJCUMzLjQsMCwzLjQsMCwzLjIsMEMzLjMsM' +
                            'CwzLjIsMCwzLjIsMHogTTMyLjEsMEMzMC40LDAsMjksMS40LDI5LDMuMnMxLjQsMy4yLDMuMSwzLjJoNi42YzEuNywwLDMu' +
                            'MS0xLjQsMy4xLTMuMlM0MC40LDAsMzguNywwCgkJCQlIMzIuMXogTTUxLjQsMGMtMS43LDAtMy4xLDEuNC0zLjEsMy4yczE' +
                            'uNCwzLjIsMy4xLDMuMmg2LjZjMS43LDAsMy4xLTEuNCwzLjEtMy4yUzU5LjgsMCw1OC4xLDBINTEuNHogTTcwLjcsMAoJCQ' +
                            'kJYy0xLjcsMC0zLjEsMS40LTMuMSwzLjJzMS40LDMuMiwzLjEsMy4yaDYuNmMxLjcsMCwzLjEtMS40LDMuMS0zLjJTNzkuM' +
                            'SwwLDc3LjQsMEg3MC43eiBNOTAsMGMtMS43LDAtMy4xLDEuNC0zLjEsMy4yCgkJCQlzMS40LDMuMiwzLjEsMy4yaDEzdjku' +
                            'OGMwLDEuNywxLjQsMy4xLDMuMiwzLjFjMS44LDAsMy4yLTEuNCwzLjItMy4xVjMuMWMwLTAuMi0wLjEtMC40LTAuMS0wLjZ' +
                            'jMC0wLjIsMC0wLjMtMC4xLTAuNQoJCQkJYy0wLjEtMC4yLTAuMi0wLjQtMC40LTAuN2MtMC4xLTAuMS0wLjItMC4zLTAuMy' +
                            '0wLjRjLTAuMi0wLjItMC4zLTAuMy0wLjUtMC40Yy0wLjItMC4xLTAuMy0wLjItMC41LTAuMwoJCQkJYy0wLjItMC4xLTAuN' +
                            'S0wLjEtMC44LTAuMmMtMC4xLDAtMC4yLTAuMS0wLjMtMC4xYy0wLjEsMC0wLjEsMC0wLjIsMGMwLDAtMC4xLDAtMC4xLDBj' +
                            'MCwwLDAsMC0wLjEsMGMtMC4xLDAtMC4xLDAtMC4yLDBIOTB6CgkJCQkgTTMuMiwyNS44Yy0xLjgsMC0zLjIsMS40LTMuMiw' +
                            'zLjF2Ni42YzAsMS43LDEuNCwzLjEsMy4yLDMuMXMzLjItMS40LDMuMi0zLjF2LTYuNkM2LjQsMjcuMiw1LDI1LjgsMy4yLD' +
                            'I1Ljh6IE0xMDYuMiwyNS44CgkJCQljLTEuOCwwLTMuMiwxLjQtMy4yLDMuMXY2LjZjMCwxLjcsMS40LDMuMSwzLjIsMy4xY' +
                            'zEuOCwwLDMuMi0xLjQsMy4yLTMuMXYtNi42QzEwOS41LDI3LjIsMTA4LDI1LjgsMTA2LjIsMjUuOHogTTMuMiw0NS4xCgkJ' +
                            'CQljLTEuOCwwLTMuMiwxLjQtMy4yLDMuMXY2LjZDMCw1Ni41LDEuNCw1OCwzLjIsNThzMy4yLTEuNCwzLjItMy4xdi02LjZ' +
                            'DNi40LDQ2LjUsNSw0NS4xLDMuMiw0NS4xeiBNNDEuOSw0NS4xCgkJCQlDMzQuOCw0NS4xLDI5LDUwLjksMjksNTh2NjEuMm' +
                            'MwLDcuMSw1LjgsMTIuOSwxMi45LDEyLjloNzcuM2M3LjEsMCwxMi45LTUuOCwxMi45LTEyLjlWNThjMC03LjEtNS44LTEyL' +
                            'jktMTIuOS0xMi45SDQxLjl6CgkJCQkgTTQxLjksNTEuNWg3Ny4zYzMuNiwwLDYuNCwyLjgsNi40LDYuNHY2MS4yYzAsMy42' +
                            'LTIuOCw2LjQtNi40LDYuNEg0MS45Yy0zLjYsMC02LjQtMi44LTYuNC02LjRWNTgKCQkJCUMzNS40LDU0LjQsMzguMyw1MS4' +
                            '1LDQxLjksNTEuNXogTTMuMiw2NC40Yy0xLjgsMC0zLjIsMS40LTMuMiwzLjF2Ni42YzAsMS43LDEuNCwzLjEsMy4yLDMuMX' +
                            'MzLjItMS40LDMuMi0zLjF2LTYuNgoJCQkJQzYuNCw2NS44LDUsNjQuNCwzLjIsNjQuNHogTTc0LDcwLjhWMTAzbDcuOC02L' +
                            'jZsNC45LDExLjVsNC4xLTEuOGwtNS4yLTExLjNsMTAuOS0xLjRMNzQsNzAuOHogTTMuMiw4My43CgkJCQljLTEuOCwwLTMu' +
                            'MiwxLjQtMy4yLDMuMXYxMi43YzAsMC4xLDAsMC4xLDAsMC4yYzAsMCwwLDAsMCwwLjFjMCwwLDAsMC4xLDAsMC4xYzAsMC4' +
                            'xLDAsMC4xLDAsMC4yYzAsMC4xLDAuMSwwLjIsMC4xLDAuMwoJCQkJYzAuMSwwLjMsMC4xLDAuNSwwLjIsMC44YzAuMSwwLj' +
                            'IsMC4yLDAuMywwLjMsMC41YzAuMSwwLjIsMC4yLDAuNCwwLjQsMC41YzAuMSwwLjEsMC4zLDAuMiwwLjQsMC4zYzAuMiwwL' +
                            'jEsMC40LDAuMywwLjcsMC40CgkJCQljMC4xLDAuMSwwLjMsMC4xLDAuNSwwLjFjMC4yLDAsMC40LDAuMSwwLjYsMC4xaDE2' +
                            'LjNjMS43LDAsMy4xLTEuNCwzLjEtMy4ycy0xLjQtMy4yLTMuMS0zLjJoLTEzdi05LjhDNi40LDg1LjEsNSw4My43LDMuMiw' +
                            '4My43CgkJCQl6Ii8+CgkJPC9nPgoJCTxyZWN0IGlkPSJSZWN0YW5nbGUtQ29weS00IiB4PSI0IiB5PSIxNzciIGNsYXNzPS' +
                            'JzdDIiIHdpZHRoPSIxNDMyIiBoZWlnaHQ9IjgyOSIvPgoJPC9nPgo8L2c+Cjwvc3ZnPgo='

                        // noinspection HtmlRequiredAltAttribute,RequiredAttributes,JSUnresolvedVariable
                        const $image = $('<img/>')
                            .attr('src', dataUri)
                            .attr('alt', ccmi18n_fileuploader.dropFilesHere)

                        // noinspection JSUnresolvedVariable
                        const $span = $('<span/>')
                            .html(ccmi18n_fileuploader.dropFilesHere)

                        $button.append($image)
                        $button.append($span)
                        $dzMessage.append($button)
                        $fileUploadContainer.append($dzMessage)
                        $fileUploadContainerWrapper.append($fileUploadContainer)
                        $yourComputerTab.append($fileUploadContainerWrapper)

                        $tabContent.append($yourComputerTab)

                        /*
                         * add incoming directory tab
                         */

                        const $incomingDirectoryTab = $('<div/>')
                            .attr('id', 'incoming-directory')
                            .attr('aria-labelledby', 'incoming-directory-tab')
                            .attr('role', 'tabpanel')
                            .addClass('tab-pane fade')

                        const $incomingDirectoryContainer = $('<div/>')
                            .addClass('ccm-incoming-directory-container')
                            .html(ccmi18n_fileuploader.loading)

                        $incomingDirectoryTab.append($incomingDirectoryContainer)

                        $tabContent.append($incomingDirectoryTab)

                        /*
                         * add remote files tab
                         */

                        const $remoteFilesTab = $('<div/>')
                            .attr('id', 'remote-files')
                            .attr('aria-labelledby', 'remote-files-tab')
                            .attr('role', 'tabpanel')
                            .addClass('tab-pane fade')

                        const $remoteFilesContainer = $('<div/>')
                            .addClass('ccm-remote-files-container')

                        // noinspection JSUnresolvedVariable
                        const $urlInput = $('<input/>')
                            .attr('type', 'text')
                            .attr('required', 'required')
                            .attr('placeholder', ccmi18n_fileuploader.enterSingleUrl)
                            .addClass('ccm-remote-file-url form-control')

                        // noinspection JSUnresolvedVariable
                        const $urlTextarea = $('<textarea/>')
                            .attr('required', 'required')
                            .attr('placeholder', ccmi18n_fileuploader.enterMultipleUrls)
                            .addClass('ccm-remote-file-url form-control')

                        $remoteFilesContainer.append($urlInput)
                        $remoteFilesContainer.append($urlTextarea)

                        $remoteFilesTab.append($remoteFilesContainer)

                        $tabContent.append($remoteFilesTab)

                        $column.append($tabContent)

                        /*
                         * Add folder selector
                         */

                        const $selectDirectoryContainer = $('<div/>')
                            .addClass('ccm-directory-selector-container')

                        const $selectDirectoryFormGroup = $('<div/>')
                            .addClass('form-group')

                        const selectDirectoryId = 'input-' + fileUploader.getUniqueId()

                        // noinspection JSUnresolvedVariable
                        const $selectDirectoryLabel = $('<label/>')
                            .attr('for', selectDirectoryId)
                            .html(ccmi18n_fileuploader.uploadFilesTo)

                        const $selectDirectoryInputGroup = $('<div/>')
                            .addClass('input-group')

                        const $select = $('<select/>')
                            .addClass('ccm-directory-selector')
                            .attr('data-size', 5)
                            .attr('data-live-search', 'true')
                            .attr('id', selectDirectoryId)

                        const $selectDirectoryInputGroupPrepend = $('<div/>')
                            .addClass('input-group-prepend')

                        // noinspection JSUnresolvedVariable
                        const $a = $('<a/>')
                            .attr('href', 'javascript:void(0);')
                            .addClass('btn btn-outline-secondary ccm-file-uploader-create-new-directory-button')
                            .html(ccmi18n_fileuploader.createNewDirectoryButton)

                        $selectDirectoryInputGroupPrepend.append($a)
                        $selectDirectoryInputGroup.append($select)
                        $selectDirectoryInputGroup.append($selectDirectoryInputGroupPrepend)
                        $selectDirectoryFormGroup.append($selectDirectoryLabel)
                        $selectDirectoryFormGroup.append($selectDirectoryInputGroup)

                        $selectDirectoryContainer.append($selectDirectoryFormGroup)
                        $column.append($selectDirectoryContainer)

                        const $div = $('<div/>')
                            .addClass('ccm-file-uploader-new-directory-name-container')
                            .addClass('hidden-container')

                        const $formGroup = $('<div/>')
                            .addClass('form-group')

                        const inputId = 'input-' + fileUploader.getUniqueId()

                        const $label = $('<label/>')
                            .attr('for', inputId)
                            .html(ccmi18n_fileuploader.directoryName)

                        // noinspection JSUnresolvedVariable
                        const $input = $('<input/>')
                            .attr('type', 'text')
                            .attr('placeholder', ccmi18n_fileuploader.directoryPlaceholder)
                            .attr('id', inputId)
                            .addClass('ccm-file-uploader-new-directory-name')
                            .addClass('form-control')

                        $formGroup.append($label)
                        $formGroup.append($input)
                        $div.append($formGroup)
                        $column.append($div)

                        $row.append($column)

                        return $row
                    },

                    getPreviewItem: function () {
                        const $fileUploaderOuter = $('<div/>')

                        const $fileUploadWrapper = $('<div/>')
                            .addClass('ccm-file-upload-wrapper')

                        const $fileUploadItemWrapper = $('<div/>')
                            .addClass('ccm-file-upload-item-wrapper')

                        const $fileUploadItem = $('<div/>')
                            .addClass('ccm-file-upload-item')

                        const $fileUploadItemInner = $('<div/>')
                            .addClass('ccm-file-upload-item-inner')

                        const $fileUploadItemImageWrapper = $('<div/>')
                            .addClass('ccm-file-upload-image-wrapper')

                        // noinspection HtmlRequiredAltAttribute,RequiredAttributes
                        const $fileUploadItemImage = $('<img/>')
                            .attr('data-dz-thumbnail', '')

                        const $fileUploadItemProgressText = $('<div/>')
                            .addClass('ccm-file-upload-progress-text')

                        const $fileUploadItemProgressTextSvg = $('<svg/>')
                            .attr('viewbox', '0 0 100 100')
                            .attr('xmlns', 'http://www.w3.org/2000/svg')
                            .attr('xmlns:xlink', 'http://www.w3.org/1999/xlink')

                        const $fileUploadItemProgressTextSvgText = $('<text/>')
                            .attr('x', '50%')
                            .attr('y', '50%')
                            .attr('dominant-baseline', 'middle')
                            .attr('text-anchor', 'middle')
                            .addClass('ccm-file-upload-progress-text-value')

                        const $fileUploadItemProgressSvg = $('<svg/>')
                            .attr('viewbox', '0 0 120 120')
                            .attr('width', '120')
                            .attr('height', '120')
                            .addClass('ccm-file-upload-progress')

                        // noinspection RequiredAttributes
                        const $fileUploadItemProgressSvgCircle = $('<circle/>')
                            .attr('stroke', '#4A90E2')
                            .attr('stroke-width', '5')
                            .attr('fill', 'transparent')
                            .attr('r', '52')
                            .attr('cx', '60')
                            .attr('cy', '60')

                        const $fileUploadLabel = $('<div/>')
                            .attr('data-dz-size', '')
                            .addClass('ccm-file-upload-label')

                        $fileUploadItemProgressSvg.append($fileUploadItemProgressSvgCircle)
                        $fileUploadItemProgressTextSvg.append($fileUploadItemProgressTextSvgText)
                        $fileUploadItemProgressText.append($fileUploadItemProgressTextSvg)
                        $fileUploadItemImageWrapper.append($fileUploadItemImage)
                        $fileUploadItemInner.append($fileUploadItemImageWrapper)
                        $fileUploadItemInner.append($fileUploadItemProgressText)
                        $fileUploadItemInner.append($fileUploadItemProgressSvg)
                        $fileUploadItem.append($fileUploadItemInner)
                        $fileUploadItemWrapper.append($fileUploadItem)
                        $fileUploadItemWrapper.append($fileUploadLabel)
                        $fileUploadWrapper.append($fileUploadItemWrapper)
                        $fileUploaderOuter.append($fileUploadWrapper)

                        return $fileUploaderOuter
                    }
                },

                makeContainerDraggable: function () {
                    const $fileInput = $('<input/>')
                        .attr('type', 'file')
                        .attr('multiple', 'multiple')
                        .addClass('ccm-file-uploader-container-dropzone-file-element')
                        .addClass('hidden')
                        .on('change', function (e) {
                            e.preventDefault()

                            const files = $(this).get(0).files

                            if (files.length) {
                                let file = {}

                                for (file of files) {
                                    fileUploader.dropzone.addFile(file)
                                }

                                $(this).addClass('hidden')

                                fileUploader.open()
                            }

                            return false
                        })

                    $container.append($fileInput)

                    $container
                        .addClass('ccm-file-uploader-container-dropzone')
                        .bind('dragenter dragstart drag dragover', function () {
                            if (fileUploader.idleTimer !== null) {
                                clearTimeout(fileUploader.idleTimer)
                                fileUploader.idleTimer = null
                            }

                            if (fileUploader.isDialogOpen) {
                                return
                            }

                            $fileInput.removeClass('hidden')
                        })
                        .bind('dragexit dragend drop', function () {
                            $fileInput.addClass('hidden')
                        })
                        .bind('dragleave', function () {
                            fileUploader.idleTimer = setTimeout(function () {
                                $fileInput.addClass('hidden')
                            }, 500)
                        })
                },

                raiseError: function (errors) {
                    fileUploader.isCompleted = false
                    fileUploader.inProgress = false

                    fileUploader.refresh()

                    $dialogEl.find('.ccm-directory-selector').removeAttr('disabled').selectpicker('refresh')
                    $dialogEl.find('.ccm-file-uploader-create-new-directory-button').removeClass('disabled')

                    let error = ''

                    for (error of errors) {
                        // noinspection JSUnresolvedVariable
                        ConcreteAlert.error({
                            title: ccmi18n_fileuploader.errorNotificationTitle,
                            message: error,
                            appendTo: document.body
                        })
                    }
                },

                uploadFromComputer: function () {
                    fileUploader.inProgress = true
                    fileUploader.dropzone.options.autoProcessQueue = true
                    fileUploader.dropzone.processQueue()

                    fileUploader.refresh()
                },

                uploadFromIncomingDirectory: function () {
                    const fileIds = []

                    $dialogEl.find('.ccm-file-select-incoming:checked').each(function () {
                        fileIds.push($(this).val())
                    })

                    var removeFilesAfterPost = $dialogEl.find('input[name=removeFilesAfterPost').is(':checked') ? 1 : 0

                    $.ajax({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/import_incoming',
                        method: 'POST',
                        data: $.extend({
                            ccm_token: CCM_SECURITY_TOKEN,
                            send_file: fileIds,
                            removeFilesAfterPost: removeFilesAfterPost,
                            currentFolder: $dialogEl.find('select.ccm-directory-selector').find('option:selected').val()
                        }, fileUploader.options.formData),
                        dataType: 'json',
                        success: function (data) {
                            if (!data.error) {
                                // refresh for the case files has been deleted
                                fileUploader.fetchFilesFromIncomingDirectory()

                                ConcreteEvent.publish('FileManagerAddFilesComplete', {
                                    files: data.files
                                })

                                fileUploader.uploadComplete()
                            } else {
                                fileUploader.raiseError(data.errors)
                            }
                        }
                    })
                },

                uploadFromRemoteFiles: function () {
                    $.ajax({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/import_remote' + '?_=' + new Date().getTime(),
                        method: 'POST',
                        data: $.extend({
                            ccm_token: CCM_SECURITY_TOKEN,
                            url_upload: $dialogEl.find('.ccm-remote-file-url:not(.d-none)').val(),
                            currentFolder: $dialogEl.find('select.ccm-directory-selector').find('option:selected').val()
                        }, fileUploader.options.formData),
                        dataType: 'json',
                        success: function (data) {
                            if (!data.error) {
                                $dialogEl.find('.ccm-remote-file-url').val('')
                                ConcreteEvent.publish('FileManagerAddFilesComplete', {
                                    files: data.files
                                })
                            } else {
                                fileUploader.raiseError(data.errors)
                            }
                        }
                    })
                },

                upload: function () {
                    NProgress.start()

                    switch ($dialogEl.find('.nav .active').attr('id')) {
                        case 'your-computer-tab':
                            fileUploader.uploadFromComputer()
                            break

                        case 'incoming-directory-tab':
                            fileUploader.uploadFromIncomingDirectory()
                            break

                        case 'remote-files-tab':
                            fileUploader.uploadFromRemoteFiles()
                            break
                    }
                },

                reinit: function () {
                    fileUploader.initIncomingDirectoryTab()
                },

                open: function (formData) {
                    this.reinit()

                    if (typeof formData === 'undefined') {
                        formData = []
                    }

                    fileUploader.options.formData = formData

                    $dialogEl.dialog('open')
                },

                setOptions: function (options) {
                    options = options || {}

                    fileUploader.options = options
                    fileUploader.options.formData = fileUploader.options.formData || {}
                },

                renderContainer: function () {
                    $dialogEl.append(fileUploader.templates.getDialog())
                },

                createDirectory: function (directoryName) {
                    $.ajax({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/create_directory' + '?_=' + new Date().getTime(),
                        method: 'POST',
                        data: {
                            ccm_token: CCM_SECURITY_TOKEN,
                            directoryName: directoryName,
                            currentFolder: $dialogEl.find('select.ccm-directory-selector').find('option:selected').val()
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (!data.error) {
                                // re-fetch the directories and select the new folder
                                // noinspection JSUnresolvedVariable
                                fileUploader.fetchDirectories(data.directoryId, function () {
                                    fileUploader.upload()
                                })
                            } else {
                                fileUploader.raiseError(data.errors)
                            }
                        }
                    })
                },

                fetchDirectories: function (selectedDirectoryId, clb) {
                    $.ajax({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/fetch_directories' + '?_=' + new Date().getTime(),
                        method: 'POST',
                        data: {
                            ccm_token: CCM_SECURITY_TOKEN
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (!data.error) {
                                const $selectBox = $dialogEl.find('select.ccm-directory-selector')

                                if (typeof selectedDirectoryId === 'undefined' || selectedDirectoryId === null) {
                                    selectedDirectoryId = $selectBox.find('option:selected').val()
                                }

                                $selectBox.empty()

                                let directory = ''

                                for (directory of data.directories) {
                                    const $option = $('<option/>')

                                    // noinspection JSUnresolvedVariable
                                    $option
                                        .attr('data-icon', 'fas fa-folder')
                                        .addClass('level-' + directory.directoryLevel)
                                        .attr('value', directory.directoryId)
                                        .html(directory.directoryName)

                                    $selectBox.append($option)
                                }

                                // refresh options
                                $selectBox.selectpicker('refresh')

                                if ($selectBox.find('option[value=\'' + selectedDirectoryId + '\']').length) {
                                    // re-selected previous selected option
                                    $selectBox.selectpicker('val', selectedDirectoryId)
                                } else {
                                    // the option is not available, select first options instead
                                    const firstDirectoryId = $selectBox.find('option:first').val()

                                    $selectBox.selectpicker('val', firstDirectoryId)
                                }

                                $dialogEl.find('.ccm-file-uploader-new-directory-name').val('')

                                if (!$dialogEl.find('.ccm-file-uploader-new-directory-name-container').hasClass('hidden-container')) {
                                    $dialogEl.find('.ccm-file-uploader-create-new-directory-button').trigger('click')
                                }

                                if (typeof clb === 'function') {
                                    clb()
                                }
                            } else {
                                fileUploader.raiseError(data.errors)
                            }
                        }
                    })
                },

                initDirectorySelector: function () {
                    $dialogEl.find('select.ccm-directory-selector')
                        .selectpicker()
                        .addClass('form-control')
                },

                fetchFilesFromIncomingDirectory: function () {
                    $.ajax({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/fetch_incoming_files' + '?_=' + new Date().getTime(),
                        method: 'POST',
                        data: {
                            ccm_token: CCM_SECURITY_TOKEN
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (!data.error) {
                                // noinspection JSUnresolvedVariable
                                $dialogEl.find('.ccm-incoming-directory-container')
                                    .empty()
                                    .append(fileUploader.templates.getIncomingFiles(
                                        data.incomingPath,
                                        data.incomingStorageLocation,
                                        data.files
                                    ))

                                $dialogEl.find('.ccm-check-all-incoming').bind('click change', function () {
                                    if ($dialogEl.find('.ccm-check-all-incoming').is(':checked')) {
                                        $dialogEl.find('.ccm-file-select-incoming[disabled!=disabled]').prop('checked', true)
                                    } else {
                                        $dialogEl.find('.ccm-file-select-incoming[disabled!=disabled]').prop('checked', false)
                                    }

                                    fileUploader.refresh()
                                })

                                $dialogEl.find('.ccm-file-select-incoming').bind('click change', function () {
                                    fileUploader.refresh()
                                })
                            } else {
                                fileUploader.raiseError(data.errors)
                            }
                        }
                    })
                },

                initCreateNewFolderFunctionality: function () {
                    $dialogEl.find('.ccm-file-uploader-create-new-directory-button').on('click', function (e) {
                        e.preventDefault()

                        if ($(this).hasClass('disabled')) {
                            return
                        }

                        const $container = $dialogEl.find('.ccm-file-uploader-new-directory-name-container')

                        if ($container.hasClass('hidden-container')) {
                            $container
                                .removeClass('hidden-container')
                                .css({
                                    display: 'none'
                                })
                                .slideDown('fast', function () {
                                    fileUploader.refresh()
                                })
                        } else {
                            $container.slideUp('fast', function () {
                                $container.addClass('hidden-container')

                                fileUploader.refresh()
                            })
                        }

                        return false
                    })

                    $dialogEl.find('.ccm-file-uploader-new-directory-name').on('change, keyup', fileUploader.refresh)
                },

                refresh: function () {
                    $dialogEl.find('.ccm-file-upload-item-wrapper')
                        .removeClass('col-2')
                        .removeClass('col-4')

                    const containerWidth = $dialogEl.find('.ccm-file-upload-container').prop('scrollWidth')
                    let thumbnailWidth = containerWidth

                    if (containerWidth >= 450 && containerWidth < 700) {
                        thumbnailWidth = thumbnailWidth / 2
                    } else if (containerWidth >= 700 && containerWidth < 900) {
                        thumbnailWidth = thumbnailWidth / 4
                    } else if (containerWidth >= 900) {
                        thumbnailWidth = thumbnailWidth / 6
                    }

                    thumbnailWidth -= 42 // padding + border (see css declaration of .ccm-file-upload-item-wrapper)

                    $dialogEl.find('.ccm-file-upload-item-wrapper').css('width', thumbnailWidth + 'px')

                    if (typeof fileUploader.options.formData.fID !== 'undefined') {
                        fileUploader.dropzone.options.maxFiles = 1
                    } else {
                        fileUploader.dropzone.options.maxFiles = null
                    }

                    switch ($dialogEl.find('.nav .active').attr('id')) {
                        case 'your-computer-tab':
                            if ((fileUploader.dropzone.files.length && !fileUploader.inProgress)) {
                                $dialogEl.closest('.ui-dialog').find('.ccm-file-uploader-submit-button').removeAttr('disabled')
                            } else {
                                $dialogEl.closest('.ui-dialog').find('.ccm-file-uploader-submit-button').attr('disabled', 'disabled')
                            }

                            break

                        case 'incoming-directory-tab':
                            if ($dialogEl.find('.ccm-file-select-incoming:checked').length) {
                                $dialogEl.closest('.ui-dialog').find('.ccm-file-uploader-submit-button').removeAttr('disabled')
                            } else {
                                $dialogEl.closest('.ui-dialog').find('.ccm-file-uploader-submit-button').attr('disabled', 'disabled')
                            }

                            break

                        case 'remote-files-tab':
                            if (typeof fileUploader.options.formData.fID !== 'undefined') {
                                $dialogEl.find('textarea.ccm-remote-file-url').addClass('d-none')
                                $dialogEl.find('input.ccm-remote-file-url').removeClass('d-none')
                            } else {
                                $dialogEl.find('textarea.ccm-remote-file-url').removeClass('d-none')
                                $dialogEl.find('input.ccm-remote-file-url').addClass('d-none')
                            }

                            if ($dialogEl.find('.ccm-remote-file-url:not(.d-none)').val().length > 0) {
                                $dialogEl.closest('.ui-dialog').find('.ccm-file-uploader-submit-button').removeAttr('disabled')
                            } else {
                                $dialogEl.closest('.ui-dialog').find('.ccm-file-uploader-submit-button').attr('disabled', 'disabled')
                            }

                            break
                    }

                    if (!$dialogEl.find('.ccm-file-uploader-new-directory-name-container').hasClass('hidden-container') &&
                        $dialogEl.find('.ccm-file-uploader-new-directory-name').val().length === 0) {
                        $dialogEl.closest('.ui-dialog').find('.ccm-file-uploader-submit-button').attr('disabled', 'disabled')
                    }
                },

                reset: function () {
                    NProgress.done()

                    $dialogEl.find('input[type=checkbox], input[type=radio]').prop('checked', false)
                    $dialogEl.find('.ccm-remote-file-url').val('')
                    $dialogEl.find('.ccm-file-upload-container')
                        .removeClass('dz-started')
                        .find('.ccm-file-upload-wrapper')
                        .remove()

                    fileUploader.dropzone.removeAllFiles(true)

                    fileUploader.isCompleted = false
                    fileUploader.inProgress = false

                    $dialogEl.find('.ccm-file-uploader-new-directory-name').val('')

                    if (!$dialogEl.find('.ccm-file-uploader-new-directory-name-container').hasClass('hidden-container')) {
                        $dialogEl.find('.ccm-file-uploader-create-new-directory-button').trigger('click')
                    }

                    $('.nav-tabs li:first a').trigger('click')
                },

                initYourComputerTab: function () {
                    fileUploader.uploadedFiles = []

                    $dialogEl.find('.ccm-file-upload-container').dropzone({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/upload',
                        previewTemplate: fileUploader.templates.getPreviewItem().html(),
                        autoProcessQueue: false,
                        uploadMultiple: true,
                        parallelUploads: 4,
                        paramName: 'files',
                        maxFiles: null,
                        autoQueue: true,

                        reset: function () {
                            fileUploader.dropzone.options.autoProcessQueue = false
                        },

                        addedfiles: function () {
                            fileUploader.refresh()
                        },

                        removedfile: function () {
                            fileUploader.refresh()
                        },

                        error: function (files, message) {
                            fileUploader.raiseError([message])
                        },

                        success: function (data, response) {
                            response.files.forEach(function (file) {
                                if (typeof file.fID !== 'undefined') {
                                    let fileAlreadyAdded = false

                                    for (const curFile of fileUploader.uploadedFiles) {
                                        if (curFile.fID === file.fID) {
                                            fileAlreadyAdded = true
                                            break
                                        }
                                    }

                                    if (!fileAlreadyAdded) {
                                        fileUploader.uploadedFiles.push(file)
                                    }
                                }
                            })
                        },

                        sending: function (file, xhr, formData) {
                            const $selectBox = $dialogEl.find('select.ccm-directory-selector')

                            formData.append('responseFormat', 'dropzone')
                            formData.append('ccm_token', CCM_SECURITY_TOKEN)
                            formData.append('currentFolder', $selectBox.find('option:selected').val())

                            if (typeof fileUploader.options.formData === 'object') {
                                let key = ''

                                for (key in fileUploader.options.formData) {
                                    // noinspection JSUnfilteredForInLoop
                                    formData.append(key, fileUploader.options.formData[key])
                                }
                            }
                        },

                        totaluploadprogress: function (progress) {
                            NProgress.set(progress / 100)
                        },

                        queuecomplete: function () {
                            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                                fileUploader.isCompleted = true
                                fileUploader.inProgress = false

                                fileUploader.dropzone.options.autoProcessQueue = false

                                $dialogEl.find('.ccm-directory-selector').removeAttr('disabled').selectpicker('refresh')
                                $dialogEl.find('.ccm-file-uploader-create-new-directory-button').removeClass('disabled')

                                if (fileUploader.uploadedFiles.length !== 0) {
                                    if (typeof fileUploader.options.formData.fID === 'undefined') {
                                        ConcreteEvent.publish('FileManagerAddFilesComplete', {
                                            files: fileUploader.uploadedFiles
                                        })
                                    } else {
                                        ConcreteEvent.publish('FileManagerReplaceFileComplete', {
                                            files: fileUploader.uploadedFiles
                                        })
                                    }

                                    fileUploader.uploadComplete()
                                }
                            }

                            fileUploader.uploadedFiles = []

                            fileUploader.reset()
                            fileUploader.refresh()
                        },

                        init: function () {
                            fileUploader.options = options
                            fileUploader.dropzone = this
                            fileUploader.refresh()
                        },

                        uploadprogress: function (file, progress) {
                            fileUploader.inProgress = true

                            const $fileElement = $(file.previewElement)
                            const circle = $fileElement.find('circle').get(0)
                            const radius = circle.r.baseVal.value
                            const circumference = radius * 2 * Math.PI

                            circle.style.strokeDasharray = `${circumference} ${circumference}`
                            circle.style.strokeDashoffset = `${circumference}`
                            circle.style.strokeDashoffset = circumference - progress / 100 * circumference

                            $fileElement.find('.ccm-file-upload-progress-text-value').html(parseInt(progress) + '%')
                            $fileElement.addClass('in-progress')

                            $dialogEl.find('.ccm-directory-selector').attr('disabled', 'disabled').selectpicker('refresh')
                            $dialogEl.find('.ccm-file-uploader-create-new-directory-button').addClass('disabled')
                        }
                    })
                },

                uploadComplete: function () {
                    NProgress.done()

                    fileUploader.reset()

                    $dialogEl.dialog('close')
                },

                initIncomingDirectoryTab: function () {
                    /*
                     * Fetch files from incoming directory
                     */

                    fileUploader.fetchFilesFromIncomingDirectory()
                },

                initRemoteFilesTab: function () {
                    $dialogEl.find('.ccm-remote-file-url').bind('keypress keyup change', fileUploader.refresh)
                },

                initTabNavigation: function () {
                    $dialogEl.find('.nav-link').bind('shown.bs.tab', function () {
                        fileUploader.refresh()

                        // bootstrap has not finally rendered the view even the 'shown.bs.tab' should first be triggered
                        // after everything is complete. Therefore we execute the refresh method again after 300ms when
                        // a tab has changed.

                        setTimeout(function () {
                            fileUploader.refresh()
                        }, 300)
                    })
                },

                getUniqueId: function () {
                    return Math.random().toString(36).substr(2, 9)
                },

                isFunction: function (value) {
                    return !!(value && value.constructor && value.call && value.apply)
                },

                initDialog: function () {
                    // noinspection JSUnresolvedVariable
                    $dialogEl.dialog({
                        title: ccmi18n_fileuploader.dialogTitle,
                        width: '80%',
                        height: '800',
                        modal: true,
                        autoOpen: false,
                        buttons: [{
                            text: ccmi18n_fileuploader.cancelButton,
                            click: function () {
                                $dialogEl.dialog('close')
                            }
                        }, {
                            text: ccmi18n_fileuploader.continueButton,
                            click: function (e) {
                                e.preventDefault()

                                if (!$dialogEl.find('.ccm-file-uploader-new-directory-name-container').hasClass('hidden-container')) {
                                    /*
                                     * Create the directory before uploading.
                                     *
                                     * In this case the upload will be started after the directory has been created.
                                     */

                                    fileUploader.createDirectory(
                                        $dialogEl.find('.ccm-file-uploader-new-directory-name').val()
                                    )
                                } else {
                                    fileUploader.upload()
                                }

                                return false
                            }
                        }],

                        resize: fileUploader.refresh,

                        close: function () {
                            fileUploader.isDialogOpen = false
                        },

                        beforeClose: function (e) {
                            if (fileUploader.dropzone.files.length && !fileUploader.forceClose) {
                                e.preventDefault()

                                // noinspection JSUnresolvedVariable
                                ConcreteAlert.confirm(
                                    ccmi18n_fileuploader.confirmMessage,
                                    function () {
                                        fileUploader.forceClose = true
                                        $dialogEl.dialog('close')
                                        jQuery.fn.dialog.closeTop()
                                    },
                                    'btn-primary',
                                    ccmi18n_fileuploader.confirmButton
                                )

                                return false
                            }

                            fileUploader.forceClose = false

                            // select the first value in directory input
                            const $selectBox = $dialogEl.find('select.ccm-directory-selector')

                            $selectBox.val($selectBox.find('option:first').val())
                            $selectBox.selectpicker('refresh')

                            // reset everything
                            fileUploader.reset()

                            // select the first tab
                            $dialogEl.find('.nav-item:nth-child(1) .nav-link').trigger('click')
                        },

                        open: function () {
                            if (fileUploader.isFunction(fileUploader.options.folderID)) {
                                fileUploader.fetchDirectories(parseInt(fileUploader.options.folderID()))
                            } else {
                                fileUploader.fetchDirectories()
                            }

                            /*
                             * Ugly polyfill to re-initialize the bootstrap tab navigation
                             * because within a dynamic window the tab's wont work after
                             * re-opining the dialog window.
                             *
                             * Removing the dialog is also not possible because the dropzone
                             * within the dialog is required for the main view.
                             */

                            $dialogEl.find('a.nav-link').unbind('click').bind('click', function () {
                                const $tabMenuItem = $(this)

                                $dialogEl.find('.tab-pane').each(function () {
                                    const $tabPane = $(this)

                                    if ($tabPane.attr('id') === $tabMenuItem.attr('aria-controls')) {
                                        $tabPane
                                            .addClass('active')
                                            .fadeIn('fast', function () {
                                                $tabPane.addClass('show')

                                                // trigger the bootstrap event
                                                $tabMenuItem.trigger('shown.bs.tab')
                                            })
                                    } else if ($tabPane.hasClass('active')) {
                                        $tabPane
                                            .removeClass('active')
                                            .fadeOut('fast', function () {
                                                $tabPane.removeClass('show')
                                            })
                                    }
                                })
                            })

                            fileUploader.isDialogOpen = true
                            fileUploader.refresh()
                        },

                        create: function () {
                            /*
                             * Unfortunately need to apply bootstrap button classes in this way...
                             *
                             * See: https://stackoverflow.com/questions/6702279/jquery-ui-dialog-buttons-how-to-add-class
                             */

                            $(this)
                                .closest('.ui-dialog')
                                .find('.ui-dialog-buttonset button')
                                .eq(0)
                                .attr('class', '')
                                .addClass('btn btn-secondary float-left')

                            $(this)
                                .closest('.ui-dialog')
                                .find('.ui-dialog-buttonset button')
                                .eq(1)
                                .attr('class', '')
                                .addClass('ccm-file-uploader-submit-button')
                                .addClass('btn btn-primary')

                            // init components
                            fileUploader.initTabNavigation()
                            fileUploader.initDirectorySelector()
                            fileUploader.initCreateNewFolderFunctionality()

                            fileUploader.initYourComputerTab()
                            fileUploader.initIncomingDirectoryTab()
                            fileUploader.initRemoteFilesTab()

                            // apply close button
                            $dialogEl
                                .parent()
                                .find('.ui-dialog-titlebar-close')
                                .html('<svg><use xlink:href="#icon-dialog-close" /></svg>')
                        }
                    })
                },

                init: function (options) {
                    fileUploader.setOptions(options)

                    fileUploader.renderContainer()
                    fileUploader.initDialog()
                    fileUploader.makeContainerDraggable()

                    $(window).bind('resize', fileUploader.refresh)
                }
            }

            $container.data('fileUploader', fileUploader)

            fileUploader.init(options)
        }

        return fileUploader
    }
})(jQuery)
