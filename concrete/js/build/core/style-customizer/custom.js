/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ConcreteAjaxRequest */

;(function(global, $) {
    'use strict';

    function ConcreteStyleCustomizerCustomCss($element, options) {
        var my = this;
        my.$element = $element;
        my.options = $.extend(true, {
            cID: null,
            edit: {
                tokenName: '',
                tokenValue: '',
                url: ''
            },
            loadCss: {
                tokenName: '',
                tokenValue: '',
                url: ''
            },
            i18n: {
                editTitle: 'Custom CSS'
            }
        }, options || {});
        $element
            .after($('<span class="ccm-style-customizer-display-swatch-wrapper" data-custom-css-selector="custom"><span class="ccm-style-customizer-display-swatch"><i class="fa fa-cog"></i></span></span>')
                .on('click', function (e) {
                    e.preventDefault();
                    my.edit();
                })
            )
        ;
        $element.addClass('ccm-style-customizer-importexport').data('ccm-style-customizer-importexport', this);
    }

    ConcreteStyleCustomizerCustomCss.prototype = {
        edit: function () {
            var my = this,
                data = {
                    cID: my.options.cID,
                    sccRecordID: my.$element.val(),
                };
            data[my.options.edit.tokenName] = my.options.edit.tokenValue;
            $.fn.dialog.open({
                title: my.options.i18n.editTitle,
                href: my.options.edit.url,
                data: data,
                modal: false,
                width: 640,
                height: 500
            });
        },
        exportStyle: function (data, cb) {
            var my = this,
                sccRecordID = parseInt(my.$element.val());
            if (isNaN(sccRecordID) || sccRecordID < 1) {
                data.custom = '';
                cb();
                return;
            }
            var send = {
                cID: my.options.cID,
                sccRecordID: sccRecordID,
            };
            send[my.options.loadCss.tokenName] = my.options.loadCss.tokenValue;
            $.concreteAjax({
                type: 'GET',
                data: send,
                url: my.options.loadCss.url,
                cache: false,
                skipResponseValidation: true,
                success: function (r) {
                    if (!r || typeof r.css !== 'string') {
                        cb(ConcreteAjaxRequest.renderJsonError(r));
                        return;
                    }
                    data.custom = r.css;
                    cb();
                }
            });
        },
        importStyle: function (data, cb) {
            if (typeof data.custom !== 'string') {
                cb();
                return;
            }
            var my = this,
                send = {
                    cID: my.options.cID,
                    sccRecordID: my.$element.val(),
                    value: data.custom
                };
            send[my.options.saveCss.tokenName] = my.options.saveCss.tokenValue;
            $.concreteAjax({
                type: 'POST',
                data: send,
                url: my.options.saveCss.url,
                skipResponseValidation: true,
                success: function (r) {
                    if (!r || typeof r.sccRecordID !== 'number') {
                        cb(ConcreteAjaxRequest.renderJsonError(r));
                        return;
                    }
                    my.$element.val(r.sccRecordID.toString());
                    cb();
                }
            });
        }
    };

    // jQuery Plugin
    $.fn.concreteStyleCustomizerCustomCss = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteStyleCustomizerCustomCss($(this), options);
        });
    };

    global.ConcreteStyleCustomizerCustomCss = ConcreteStyleCustomizerCustomCss;

})(this, jQuery);
