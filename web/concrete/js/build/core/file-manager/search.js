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


        // TODO
        my._templateSearchResultsMenu =  _.template($('script[data-template=search-results-default-file-menu]').html());

        ConcreteAjaxSearch.call(my, $element, options);

        my.setupFileUploads();
        my.setupEvents();
    }


    ConcreteFileManager.prototype = Object.create(ConcreteAjaxSearch.prototype);

    ConcreteFileManager.prototype.setupFileMenu = function( parent, data ) {
        
    }

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
                start: function(e) {
                    $('#ccm-search-uploaded-fIDs').val(''); // reset the list
                    my.updateTargetButtons();

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
                stop: function(e) {
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
                        my.updateTargetButtons();
                        my.$targetUploaded.click();
                        my.$bulkActionsMenu.addClass("open");
                    }
                },
                done: function( e, data) {
                    if (!data || !data.jqXHR ) return;
                    data.jqXHR.done(function(data, status, jq ) {

                        if ( data.length > 0 )
                        {
                            var i;
                            var store = $('#ccm-search-uploaded-fIDs').val();
                            for ( i = 0; i < data.length ; i++ ) store = store += String(data[i].fID) + ",";
                            $('#ccm-search-uploaded-fIDs').val(store);
                        }
                        
                    });
                    data.jqXHR.fail(function(){
                        $('#ccm-search-uploaded-fIDs').val(''); // reset the list
                    });

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
                return false;
            });
        }
    };


    ConcreteFileManager.prototype.handleSelectedBulkAction = function(type, $anchor, items) {
        var my = this;
        var itemIDs = [];

        $.each(items, function(i, val) {
            itemIDs.push({'name': 'fID[]', 'value':val});
        });

        my.$bulkActionsMenu.removeClass("open");
        ConcreteAjaxSearch.handleFileMenuAction( type, $anchor, itemIDs );

        this.publish('SearchBulkActionSelect', {type: type, anchor: $anchor, items: itemIDs});
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
            return $('script[data-template=search-results-default-file-menu]').html();
        }
    };

    // jQuery Plugin
    $.fn.concreteFileManager = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteFileManager($(this), options);
        });
    };

    global.ConcreteFileManager = ConcreteFileManager;

}(window, $);
