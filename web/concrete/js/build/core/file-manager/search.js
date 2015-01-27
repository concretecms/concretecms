/**
 * block ajax
 */

!function(global, $) {
    'use strict';

    function ConcreteFileManager($element, options) {
        'use strict';
        var my = this;
        options = $.extend({
            'mode': 'menu',
            'uploadElement': 'body',
            'bulkParameterName': 'fID'
        }, options);

        my.options = options;
        my._templateFileProgress = _.template('<div id="ccm-file-upload-progress" class="ccm-ui"><div id="ccm-file-upload-progress-bar">' +
            '<div class="progress progress-striped active"><div class="progress-bar" style="width: <%=progress%>%;"></div></div>' +
            '</div></div>');
        my._templateSearchResultsMenu = _.template(ConcreteFileManagerMenu.get());

        ConcreteAjaxSearch.call(my, $element, options);

        my.setupFileDownloads();
        my.setupFileUploads();
        my.setupEvents();

    }

    ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

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
            $fileUploaders = $('.ccm-file-manager-upload'),
            $fileUploader = $fileUploaders.filter('#ccm-file-manager-upload-prompt'),
            errors = [],
            error_template = _.template(
                '<span><%- message %></span>' +
                '<ul><% _(errors).each(function(error) { %>' +
                '<li><strong><%- error.name %></strong><p><%- error.error %></p></li>' +
                '<% }) %></ul>'),
            args = {
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/file/upload',
                dataType: 'json',
                formData: {'ccm_token': CCM_SECURITY_TOKEN},
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
                    $('#ccm-file-upload-progress-wrapper').remove();
                    $('<div />', {'id': 'ccm-file-upload-progress-wrapper'}).html(my._templateFileProgress({'progress': 100})).appendTo(document.body);
                    $.fn.dialog.open({
                        title: ccmi18n_filemanager.uploadProgress,
                        width: 400,
                        height: 50,
                        element: $('#ccm-file-upload-progress-wrapper'),
                        modal: true
                    });
                },
                stop: function() {
                    jQuery.fn.dialog.closeTop();
                    my.refreshResults();

                    if (errors.length) {
                        ConcreteAlert.error({
                            message: error_template({message: ccmi18n_filemanager.uploadFailed, errors: errors}),
                            title: ccmi18n_filemanager.title,
                            delay: 10000
                        });
                    } else {
                        ConcreteAlert.notify({
                            'message': ccmi18n_filemanager.uploadComplete,
                            'title': ccmi18n_filemanager.title
                        });
                    }
                }
            };

        $fileUploader = $fileUploader.length ? $fileUploader : $fileUploaders.first();
        $fileUploader.on('click', function() {
            $(this).find('input').trigger('click');
        });

        $fileUploader.fileupload(args);
    };

    ConcreteFileManager.prototype.setupEvents = function() {
        var my = this;
        ConcreteEvent.subscribe('FileManagerUpdateRequestComplete', function(e) {
            my.refreshResults();
        });
    };

    ConcreteFileManager.prototype.setupStarredResults = function() {
        var my = this;
        my.$element.unbind('.concreteFileManagerStar').on('click.concreteFileManagerStar', 'a[data-search-toggle=star]', function() {
            var $link = $(this);
            var data = {'fID': $(this).attr('data-search-toggle-file-id')};
            my.ajaxUpdate($link.attr('data-search-toggle-url'), data, function(r) {
                if (r.star) {
                    $link.parent().addClass('ccm-file-manager-search-results-star-active');
                } else {
                    $link.parent().removeClass('ccm-file-manager-search-results-star-active');
                }
            });
            return false;
        });
    };

    ConcreteFileManager.prototype.updateResults = function(result) {
        var my = this;
        ConcreteAjaxSearch.prototype.updateResults.call(my, result);
        my.setupStarredResults();
        if (my.options.mode == 'choose') {
            my.$element.unbind('.concreteFileManagerHoverFile');
            my.$element.on('mouseover.concreteFileManagerHoverFile', 'tr[data-file-manager-file]', function() {
                $(this).addClass('ccm-search-select-hover');
            });
            my.$element.on('mouseout.concreteFileManagerHoverFile', 'tr[data-file-manager-file]', function() {
                $(this).removeClass('ccm-search-select-hover');
            });
            my.$element.unbind('.concreteFileManagerChooseFile').on('click.concreteFileManagerChooseFile', 'tr[data-file-manager-file]', function() {
                ConcreteEvent.publish('FileManagerBeforeSelectFile', {fID: $(this).attr('data-file-manager-file')});
                ConcreteEvent.publish('FileManagerSelectFile', {fID: $(this).attr('data-file-manager-file')});
                my.$downloadTarget.remove();
                return false;
            });
        }
    };

    ConcreteFileManager.prototype.handleSelectedBulkAction = function(value, type, $option, $items) {
        var my = this, itemIDs = [];
        $.each($items, function(i, checkbox) {
            itemIDs.push({'name': 'item[]', 'value': $(checkbox).val()});
        });

        if (value == 'download') {
            my.$downloadTarget.get(0).src = CCM_TOOLS_PATH + '/files/download?' + jQuery.param(itemIDs);
        } else {
            ConcreteAjaxSearch.prototype.handleSelectedBulkAction.call(this, value, type, $option, $items);
        }
    };

    ConcreteAjaxSearch.prototype.createMenu = function($selector) {
        var my = this;
        $selector.concreteFileMenu({
            'container': my,
            'menu': $('[data-search-menu=' + $selector.attr('data-launch-search-menu') + ']')
        });
    };

    /**
     * Static Methods
     */
    ConcreteFileManager.launchDialog = function(callback, opts ) {
        var w = $(window).width() - 53;
        var data = {};
        var i;

        var options = {
            filters: [], // filters must be an array of objects ex: [{ field: Concrete.const.Controller.Search.Files.FILTER_BY_TYPE, type: Concrete.const.Core.File.Type.Type.T_IMAGE }]
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
            onOpen: function() {
                ConcreteEvent.unsubscribe('FileManagerSelectFile');
                ConcreteEvent.subscribe('FileManagerSelectFile', function(e, data) {
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

    var ConcreteFileManagerMenu = {

        get: function() {
            return '<div class="ccm-ui"><div class="ccm-popover-file-menu popover fade" data-search-file-menu="<%=item.fID%>" data-search-menu="<%=item.fID%>">' +
                '<div class="arrow"></div><div class="popover-inner"><ul class="dropdown-menu">' +
                '<% if (typeof(displayClear) != \'undefined\' && displayClear) { %>' +
                '<li><a href="#" data-file-manager-action="clear">' + ccmi18n_filemanager.clear + '</a></li>' +
                '<li class="divider"></li>' +
                '<% } %>' +
                '<% if (item.canViewFile) { %>' +
                    '<li><a class="dialog-launch" dialog-modal="false" dialog-append-buttons="true" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.view + '" href="' + CCM_TOOLS_PATH + '/files/view?fID=<%=item.fID%>">' + ccmi18n_filemanager.view + '</a></li>' +
                '<% } %>' +
                '<li><a href="#" onclick="window.frames[\'ccm-file-manager-download-target\'].location=\'' + CCM_TOOLS_PATH + '/files/download?fID=<%=item.fID%>\'; return false">' + ccmi18n_filemanager.download + '</a></li>' +
                '<% if (item.canEditFile) { %>' +
                    '<li><a class="dialog-launch" dialog-modal="true" dialog-width="90%" dialog-height="70%" dialog-title="' + ccmi18n_filemanager.edit + '" href="' + CCM_TOOLS_PATH + '/files/edit?fID=<%=item.fID%>">' + ccmi18n_filemanager.edit + '</a></li>' +
                '<% } %>' +
                '<li><a class="dialog-launch" dialog-modal="true" dialog-width="680" dialog-height="450" dialog-title="' + ccmi18n_filemanager.properties + '" href="' + CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/properties?fID=<%=item.fID%>">' + ccmi18n_filemanager.properties + '</a></li>' +
                '<% if (item.canReplaceFile) { %>' +
                    '<li><a class="dialog-launch" dialog-modal="true" dialog-width="500" dialog-height="200" dialog-title="' + ccmi18n_filemanager.replace + '" href="' + CCM_TOOLS_PATH + '/files/replace?fID=<%=item.fID%>">' + ccmi18n_filemanager.replace + '</a></li>' +
                '<% } %>' +
                '<% if (item.canCopyFile) { %>' +
                    '<li><a href="#" data-file-manager-action="duplicate">' + ccmi18n_filemanager.duplicate + '</a></li>' +
                '<% } %>' +
                '<li><a class="dialog-launch" dialog-modal="true" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.sets + '" href="' + CCM_TOOLS_PATH + '/files/add_to?fID=<%=item.fID%>">' + ccmi18n_filemanager.sets + '</a></li>' +
                '<% if (item.canDeleteFile || item.canEditFilePermissions) { %>' +
                    '<li class="divider"></li>' +
                '<% } %>' +
                '<% if (item.canEditFilePermissions) { %>' +
                    '<li><a class="dialog-launch" dialog-modal="true" dialog-width="520" dialog-height="450" dialog-title="' + ccmi18n_filemanager.permissions + '" href="' + CCM_TOOLS_PATH + '/files/permissions?fID=<%=item.fID%>">' + ccmi18n_filemanager.permissions + '</a></li>' +
                '<% } %>' +
                '<% if (item.canDeleteFile) { %>' +
                '<li><a class="dialog-launch" dialog-modal="true" dialog-width="500" dialog-height="200" dialog-title="' + ccmi18n_filemanager.deleteFile + '" href="' + CCM_TOOLS_PATH + '/files/delete?fID=<%=item.fID%>">' + ccmi18n_filemanager.deleteFile + '</a></li>' +
                '<% } %>' +
            '</ul></div></div>';
        }
    };

    // jQuery Plugin
    $.fn.concreteFileManager = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteFileManager($(this), options);
        });
    };

    global.ConcreteFileManager = ConcreteFileManager;
    global.ConcreteFileManagerMenu = ConcreteFileManagerMenu;

}(window, $);
