/* eslint-disable no-new, no-unused-vars, camelcase, eqeqeq */

/* Extend bootstrap-select */
;(function (global, $) {
    // grab a reference to existing functions
    var _init = $.fn.selectpicker.Constructor.prototype.init
    var _destroy = $.fn.selectpicker.Constructor.prototype.destroy

    // extend the prototype with own functions
    $.extend(true, $.fn.selectpicker.Constructor.prototype, {
    // this will replace the original $.fn.selectpicker.Constructor.prototype.init function
        init: function () {
            var that = this
            var addNoResultClassName = function (addedNode) {
                var $addedNode = $(addedNode)
                var $addedNodeBsSelect = $addedNode.closest('.bootstrap-select')
                if (
                    $addedNode.hasClass('no-results') &&
          $addedNodeBsSelect.length &&
          $addedNodeBsSelect.find('select').selectpicker('liveSearch') == true
                ) {
                    $addedNode.addClass('ccm-enhanced-select-input-add-new-term')
                }
            }

            // always add the required selectize-input class name if not present
            if (this.options.styleBase.indexOf('ccm-enhanced-select') == -1) {
                this.options.styleBase += ' ccm-enhanced-select'
            }

            // call the original init function
            _init.apply(this, arguments)

            if (this.options.liveSearch && this.options.allowAdd) {
                this.options.noneResultsText = ccmi18n.selectNoResult

                if ('MutationObserver' in window) {
                    this.allowAddMutationObserver = new MutationObserver(function (mutations) {
                        mutations.forEach(function (mutation) {
                            if (mutation.type == 'childList') {
                                if (mutation.addedNodes.length > 0) {
                                    mutation.addedNodes.forEach(function (addedNode) {
                                        addNoResultClassName(addedNode)
                                    })
                                }
                            }
                        })
                    })

                    this.allowAddMutationObserver.observe(this.$element.closest('.bootstrap-select').get(0), {
                        childList: true,
                        subtree: true
                    })
                } else {
                    $('html').addEventListener('DOMNodeInserted', function (event) {
                        event.stopImmediatePropagation()
                        addNoResultClassName(event.target)
                    }, true)
                }

                $(document.body).on('click', '.no-results.ccm-enhanced-select-input-add-new-term', function (ev) {
                    ev.stopPropagation()

                    // the .no-results element is removed and added to the DOM when needed
                    // So let's turn off the click handler every time
                    $(this).off('click')

                    var $searchbox = that.$searchbox
                    if (!$searchbox.length) return
                    var txt = $searchbox.val()
                    if (txt) txt.replace(/[|]/g, '')
                    if ($.trim(txt) == '') return
                    var select = that.$element
                    var newOption = $('<option>', {
                        selected: true,
                        text: txt,
                        value: txt // [NOUR check what happens with this]
                    })
                    var firstOption = $('option', select).eq(1)
                    if (firstOption.length) {
                        firstOption.before(newOption)
                    } else {
                        select.append(newOption)
                    }

                    select.selectpicker('refresh')
                    $searchbox.trigger('focus')
                })
            }
        },

        // this will replace the original $.fn.selectpicker.Constructor.prototype.destroy function
        destroy: function () {
            // call the original destroy function
            _destroy.apply(this, arguments)

            if (
                this.options.liveSearch &&
        this.options.allowAdd &&
        this.allowAddMutationObserver &&
        typeof this.allowAddMutationObserver != 'undefined'
            ) {
                // free up memory if possible
                this.allowAddMutationObserver.disconnect()
                this.allowAddMutationObserver = null
            }
        }
    })
// }
})(window, jQuery); // eslint-disable-line semi
