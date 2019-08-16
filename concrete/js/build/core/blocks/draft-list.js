/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global ConcreteEvent */

;(function(global, $) {
    'use strict';

    function ConcreteDraftList($element, options) {
        var my = this;
        options = $.extend({}, options);

        my.$element = $element;
        my.options = options;

        ConcreteEvent.unsubscribe('SitemapDeleteRequestComplete');
        ConcreteEvent.subscribe('SitemapDeleteRequestComplete', function (e) {
            my.hideLoader();
            $.concreteAjax({
                dataType: 'html',
                url: my.options.reloadUrl,
                method: 'get',
                success: function (r) {
                    my.$element.replaceWith(r);
                },
                complete: function() {
                    my.hideLoader();
                }
            });
        });

        my.$element.on('click', 'div.ccm-pagination-wrapper a', function(e) {
            e.preventDefault();
            my.showLoader();
            window.scrollTo(0, 0);
            $.concreteAjax({
                loader: false,
                dataType: 'html',
                url: $(this).attr('href'),
                method: 'get',
                success: function(r) {
                    my.$element.replaceWith(r);
                },
                complete: function() {
                    my.hideLoader();
                }
            });

        });

        my.$element.find('.dialog-launch').dialog();
    }

    ConcreteDraftList.prototype = {
        showLoader: function() {
            var my = this;
            my.$element.find('.ccm-block-desktop-draft-list-for-me-loader').removeClass('hidden');
        },

        hideLoader: function() {
            var my = this;
            my.$element.find('.ccm-block-desktop-draft-list-for-me-loader').addClass('hidden');
        }

    };

    // jQuery Plugin
    $.fn.concreteDraftList = function(options) {
        return $.each($(this), function(i, obj) {
            new ConcreteDraftList($(this), options);
        });
    };

    global.ConcreteDraftList = ConcreteDraftList;

})(this, jQuery);