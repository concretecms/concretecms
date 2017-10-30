!function(global, $) {
    'use strict';

    function ConcreteDocumentLibrary(options) {
        options = options || {};
        options = $.extend({
            'bID': 0,
            'allowFileUploading': false,
            'allowInPageFileManagement': false
        }, options);

        this.options = options;
        this.setupDetails();
        this.setupAdvancedSearch();
        this.setupEditProperties();

        if (options.allowFileUploading) {
            this.setupFileUploading();
        }
    }

    ConcreteDocumentLibrary.prototype.setupFileUploading = function() {
        var obj = this;
        $('a[data-document-library-add-files]').on('click', function(e) {
            e.preventDefault();
            var bID = obj.options.bID;
            var $details = $('div[data-document-library-add-files=' + bID + ']'),
                $uploader = $('div[data-document-library-add-files=' + bID + ']'),
                $pending = $uploader.find('.ccm-block-document-library-add-files-pending'),
                $progress = $uploader.find('.ccm-block-document-library-add-files-uploading'),
                uploadAction = $details.attr('data-document-library-upload-action'),
                securityToken = $uploader.find('input[name=ccm_token]').val(),
                errors = [],
                files = [];

            if ($details.is(':visible')) {
                $uploader.fileupload('destroy');
                $(this).removeClass('ccm-block-document-library-add-files-open');
                $details.hide();
            } else {
                $(this).addClass('ccm-block-document-library-add-files-open');
                $details.show();
                $uploader.fileupload({
                    url: uploadAction,
                    dataType: 'json',
                    formData: {'ccm_token': securityToken},
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
                    start: function() {
                        $pending.hide();
                        $progress.show();
                        errors = [];
                    },
                    done: function(e, data)
                    {
                        files.push(data.result.files[0]);
                    },
                    stop: function() {

                        if (obj.options.allowInPageFileManagement) {
                            $progress.hide();
                            $pending.show();

                            if (errors.length) {
                                var str = '';
                                $.each(errors, function(i, o) {
                                    str += o.error + "\n";
                                });
                                alert(str);

                            } else {
                                ConcreteEvent.unsubscribe('FileManagerUploadCompleteDialogClose.documentLibrary');
                                ConcreteEvent.subscribe('FileManagerUploadCompleteDialogClose.documentLibrary', function(e, data) {
                                    window.location.reload();
                                });
                                ConcreteFileManager.launchUploadCompleteDialog(files);
                                files = [];
                            }
                        } else {
                            window.location.reload();
                        }
                    }
                });
            }
        });
    }

    ConcreteDocumentLibrary.prototype.setupDetails = function() {
        $('a[data-document-library-show-details]').on('click', function(e) {
            e.preventDefault();
            var fID = $(this).attr('data-document-library-show-details');
            var $details = $(this).closest('table').find('tr[data-document-library-details=' + fID + ']');
            if ($details.is(':visible')) {
                $(this).removeClass('ccm-block-document-library-details-open');
                $details.hide();
            } else {
                $(this).addClass('ccm-block-document-library-details-open');
                $details.show();
            }
        });
    }

    ConcreteDocumentLibrary.prototype.setupAdvancedSearch = function() {
        $('a[data-document-library-advanced-search]').on('click', function(e) {
            e.preventDefault();
            var bID = $(this).attr('data-document-library-advanced-search');
            var $details = $('div[data-document-library-advanced-search-fields=' + bID + ']');
            if ($details.is(':visible')) {
                $(this).removeClass('ccm-block-document-library-advanced-search-open');
                $details.find('input[name=advancedSearchDisplayed]').val('');
                $details.hide();
            } else {
                $(this).addClass('ccm-block-document-library-advanced-search-open');
                $details.find('input[name=advancedSearchDisplayed]').val(1);
                $details.show();
            }
        });
    }

    ConcreteDocumentLibrary.prototype.setupEditProperties = function() {
        $('a[data-document-library-edit-properties]').on('click', function(e) {
            e.preventDefault();
            var fID = $(this).attr('data-document-library-edit-properties');
            jQuery.fn.dialog.open({
                href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/properties?fID=' + fID,
                modal: true,
                width: 680,
                height: 450,
                title: ccmi18n_filemanager.properties,
                onClose: function() {
                    window.location.reload();
                }
            });
        });
    }

    // jQuery Plugin
    $.concreteDocumentLibrary = function(options) {
        return new ConcreteDocumentLibrary(options);
    }

}(this, $);
