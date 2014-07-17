/**
 * Base search class for AJAX forms in the UI
 */

!function(global, $) {
    'use strict';

    function ConcreteAjaxForm($form, options) {
        var my = this;
        options = options || {};
        options = $.extend({
            'beforeSubmit': my.before,
            'complete': my.complete,
            'data': {}
        }, options);
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
    }

    ConcreteAjaxForm.prototype.error = function(r, my, callback) {
        ConcreteAjaxRequest.prototype.error(r, my);
        if (callback) {
            callback(r);
        }
    }

    ConcreteAjaxForm.prototype.success = function(r, my, callback) {
        if (my.validateResponse(r)) {
            if (callback) {
                callback(r);
            } else {
                // if we get a success function passed through, we use it. Otherwise we use the standard
                ConcreteEvent.publish('AjaxFormSubmitSuccess', {response: r, form: my.$form.attr('data-dialog-form')});
                if (r.redirectURL) {
                    window.location.href = r.redirectURL;
                } else {
                    if (my.$form.attr('data-dialog-form')) {
                        jQuery.fn.dialog.closeTop();
                    }
                    ConcreteAlert.notify({
                        'message': r.message,
                        'title': r.title
                    });
                }
            }
        }
    }

    // jQuery Plugin
    $.fn.concreteAjaxForm = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteAjaxForm($(this), options);
        });
    }

    global.ConcreteAjaxForm = ConcreteAjaxForm;

}(this, $);