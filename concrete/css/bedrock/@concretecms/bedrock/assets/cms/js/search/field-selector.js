/* eslint-disable no-new, no-unused-vars, camelcase */
/* global _, ConcreteAssetLoader */

;(function(global, $) {
    'use strict'

    function ConcreteSearchFieldSelector($element, options) {
        var my = this
        options = options || {}
        options = $.extend({}, options)

        my.$element = $element
        my.options = options

        var $container = $('div[data-container=search-fields]')
        var renderFieldRowTemplate = _.template(
            $('script[data-template=search-field-row]').html()
        )

        my.$element.find('[data-search-field-key]').on('click', function() {
            var key = $(this).data('search-field-key')
            if (key) {
                $.concreteAjax({
                    url: $(this).attr('data-action'),
                    data: {
                        field: key
                    },
                    success: function(r) {
                        _.each(r.assets.css, function(css) {
                            ConcreteAssetLoader.loadCSS(css)
                        })
                        _.each(r.assets.javascript, function(javascript) {
                            ConcreteAssetLoader.loadJavaScript(javascript)
                        })

                        var $content = $container.append(
                            renderFieldRowTemplate({ field: r })
                        )
                        var selects = $content.find('select.ccm-enhanced-select')
                        if (selects.length) {
                            selects.selectpicker({
                                liveSearch: true,
                                width: '100%'
                            })
                        }
                    }
                })
            }
        })

        $container.on('click', 'a[data-search-remove=search-field]', function(e) {
            e.preventDefault()
            var $row = $(this).parentsUntil('.ccm-search-field-selector-row').parent()
            $row.remove()
        })

        var defaultQuery = $('script[data-template=default-query]').html()
        if (defaultQuery) {
            defaultQuery = JSON.parse(defaultQuery)
        }

        if (my.options.query) {
            $.each(my.options.query.fields, function(i, field) {
                $container.append(
                    renderFieldRowTemplate({ field: field })
                )
            })
        } else if (defaultQuery) {
            $.each(defaultQuery.fields, function(i, field) {
                $container.append(
                    renderFieldRowTemplate({ field: field })
                )
            })
        }

        var selects = $container.find('select.ccm-enhanced-select')
        if (selects.length) {
            selects.selectpicker({
                liveSearch: true,
                width: '100%'
            })
        }
    }

    // jQuery Plugin
    $.fn.concreteSearchFieldSelector = function (options) {
        return $.each($(this), function (i, obj) {
            new ConcreteSearchFieldSelector($(this), options)
        })
    }

    global.ConcreteSearchFieldSelector = ConcreteSearchFieldSelector
})(window, jQuery); // eslint-disable-line semi
