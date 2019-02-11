/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ConcreteAjaxRequest, ccm_doProgressiveOperation, ConcreteEvent, ConcreteAlert */

/* Base search class for AJAX forms in the UI */
;(function(global, $) {
    'use strict';

    function ConcreteAjaxForm($form, options) {
        var my = this;
        options = options || {};
        options = $.extend({
            'progressiveOperation': false,
            'progressiveOperationTitle': '',
            'beforeSubmit': my.before,
            'complete': my.complete,
            'data': {},
            error: null,
            skipResponseValidation: false
        }, options);
        if (!options.data) {
            options.data = {};
        }
        options.data.__ccm_consider_request_as_xhr = '1';
        my.$form = $form;
        ConcreteAjaxRequest.call(my, options);
        return my.$form;
    }

    ConcreteAjaxForm.prototype = Object.create(ConcreteAjaxRequest.prototype);

    ConcreteAjaxForm.prototype.execute = function() {
        var my = this,
            options = my.options,
            successCallback = options.success,
            errorCallback = options.error;

        my.$form.ajaxForm({
            type: options.type,
            data: options.data,
            url: options.url,
            dataType: options.dataType,
            beforeSubmit: function() {
                options.beforeSubmit(my);
            },
            error: function(r) {
                my.error(r, my, errorCallback);
            },
            success: function(r) {
                my.success(r, my, successCallback);
            },
            complete: function() {
                options.complete(my);
            }
        });
    };

    ConcreteAjaxForm.prototype.handleProgressiveOperation = function(resp, onComplete) {
        var my = this,
            url = my.$form.attr('action') ? my.$form.attr("action") : my.options.url,
            params = my.$form.formToArray(true);

        jQuery.fn.dialog.hideLoader();

        new ConcreteProgressiveOperation({
            response: resp,
            title: my.options.progressiveOperationTitle,
            onComplete: function() {
                onComplete(resp);
            }
        });
    }

    ConcreteAjaxForm.prototype.error = function(r, my, callback) {
        ConcreteAjaxRequest.prototype.error(r, my);
        if (callback) {
            callback(r);
        }
    };

    ConcreteAjaxForm.prototype.doFinish = function(r) {
        var my = this;
        ConcreteEvent.publish('AjaxFormSubmitSuccess', {response: r, form: my.$form.attr('data-dialog-form')});
        if (r.redirectURL) {
            window.location.href = r.redirectURL;
        } else {
            if (my.$form.attr('data-dialog-form')) {
                $.fn.dialog.closeTop();
            }
            if (r.message) {
                ConcreteAlert.notify({
                    'message': r.message,
                    'title': r.title
                });
            }
        }
    };

    ConcreteAjaxForm.prototype.success = function(resp, my, callback) {
        if (my.options.skipResponseValidation || my.validateResponse(resp)) {
            if (callback) {
                if (my.options.progressiveOperation) {
                    my.handleProgressiveOperation(resp, function(r) {
                        callback(r);
                    });
                } else {
                    callback(resp);
                }
            } else {
                if (my.options.progressiveOperation) {
                    my.handleProgressiveOperation(resp, function(r) {
                        my.doFinish(r);
                    });
                } else {
                    my.doFinish(resp);
                }
            }
        }
    };

    // jQuery Plugin
    $.fn.concreteAjaxForm = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteAjaxForm($(this), options);
        });
    };

    global.ConcreteAjaxForm = ConcreteAjaxForm;

})(this, jQuery);
