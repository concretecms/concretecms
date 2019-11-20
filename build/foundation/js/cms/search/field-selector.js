/* jshint unused:vars, undef:true, browser:true, jquery:true */
/* global _, ConcreteAssetLoader */

;(function(global, $) {
    'use strict';

    function ConcreteSearchFieldSelector($element, options) {
        var my = this;
        options = options || {};
        options = $.extend({}, options);

        my.$element = $element;
        my.options = options;

        var $container = $('div[data-container=search-fields]');
        var renderFieldRowTemplate = _.template(
            $('script[data-template=search-field-row]').html()
        );
        var defaultQuery = $('script[data-template=default-query]').html();
        if (defaultQuery) {
            defaultQuery = JSON.parse(defaultQuery);
        }
        $('button[data-button-action=add-field]').on('click', function() {
            $container.append(
                renderFieldRowTemplate()
            );
        });
        
        if (my.options.result && my.options.result.query) {
            $.each(my.options.result.query.fields, function(i, field) {
                $container.append(
                    renderFieldRowTemplate({'field': field})
                );
            });
        } else if (defaultQuery) {
            $.each(defaultQuery.fields, function(i, field) {
                $container.append(
                    renderFieldRowTemplate({'field': field})
                );
            });
        }

        var selects = $container.find('select.selectize-select');
        if (selects.length) {
            selects.selectize({
                plugins: ['remove_button']
            });
        }
        $container.on('change', 'select.ccm-search-field-selector-choose', function() {
            var key = $(this).val();
            var $content = $(this).parent().find('div.form-group');
            if (key) {
                $.concreteAjax({
                    url: $(this).attr('data-action'),
                    data: {
                        'field': key
                    },
                    success: function(r) {
                        _.each(r.assets.css, function(css) {
                            ConcreteAssetLoader.loadCSS(css);
                        });
                        _.each(r.assets.javascript, function(javascript) {
                            ConcreteAssetLoader.loadJavaScript(javascript);
                        });
                        $content.html(r.element);
                        var selects = $content.find('select.selectize-select');
                        if (selects.length) {
                            selects.selectize({
                                plugins: ['remove_button']
                            });
                        }
                    }
                });
            }
        });
        $container.on('click', 'a[data-search-remove=search-field]', function(e) {
            e.preventDefault();
            var $row = $(this).parent();
            $row.remove();
        });    
    }


    // jQuery Plugin
    $.fn.concreteSearchFieldSelector = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteSearchFieldSelector($(this), options);
        });
    };

    global.ConcreteSearchFieldSelector = ConcreteSearchFieldSelector;

})(window, jQuery);
