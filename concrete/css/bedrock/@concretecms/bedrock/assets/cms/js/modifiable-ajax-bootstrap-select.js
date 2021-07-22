/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */

/* Extend ajax-bootstrap-select */ ;
;(function (global, $) {
    // grab a reference to existing functions
    var _init = window.AjaxBootstrapSelect.prototype.init
    var _complete = window.AjaxBootstrapSelectRequest.prototype.complete
    var _setStatus = window.AjaxBootstrapSelectList.prototype.setStatus

    // extend the prototype with own functions
    $.extend(true, window.AjaxBootstrapSelect.prototype, {
        init: function () {
            var that = this

            _init.apply(this, arguments)

            if (this.selectpicker) {
                if (this.selectpicker.options.liveSearch && this.selectpicker.options.allowAdd) {
                    // Allow options to override locale specific strings.
                    this.options.locale.statusNoResults = ccmi18n.selectNoResult
                    this.locale[this.options.langCode] = $.extend(true, {}, this.locale[this.options.langCode], this.options.locale)
                }
            }

            $(document.body).on('click', '.no-results.ccm-ajax-enhanced-select-input-add-new-term', function (ev) {
                ev.stopPropagation()

                var $searchbox = that.selectpicker.$searchbox
                if (!$searchbox.length) return
                var txt = $searchbox.val()
                if (txt) txt.replace(/[|]/g, '')
                if ($.trim(txt) == '') return
                var data = []
                var newOption = {
                    preserved: that.options.preserveSelected,
                    disabled: false,
                    selected: true,
                    text: txt,
                    value: txt,
                    class: ''
                }
                data.push(newOption)
                that.list.selected.push(newOption)
                that.list.replaceOptions(data)
                $searchbox.trigger('focus')
            })
        }
    })

    $.extend(true, window.AjaxBootstrapSelectRequest.prototype, {
        complete: function () {
            _complete.apply(this, arguments)

            if (this.plugin.selectpicker.options.liveSearch && this.plugin.selectpicker.options.allowAdd) {
                this.plugin.list.$status.removeClass('no-results ccm-ajax-enhanced-select-input-add-new-term')
                if (arguments.status !== 'abort') {
                    var cache = this.plugin.list.cacheGet(this.plugin.query)
                    if (cache && !cache.length) {
                        // no results
                        this.plugin.list.$status.addClass('no-results ccm-ajax-enhanced-select-input-add-new-term')
                    }
                }
            }
        }
    })

    $.extend(true, window.AjaxBootstrapSelectList.prototype, {
        setStatus: function () {
            if (this.plugin && this.plugin.selectpicker) {
                if (arguments[0]) {
                    // bootstrap-select does this but for some reason ajax-bootstrap-select doesn't so I put it back here
                    arguments[0] = arguments[0].replace('{0}', '"' + this.plugin.selectpicker.$searchbox.val() + '"')
                }
            }
            _setStatus.apply(this, arguments)
        }
    })
})(window, jQuery); // eslint-disable-line semi
