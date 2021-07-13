/* eslint-disable no-new, no-unused-vars, camelcase, no-irregular-whitespace, new-cap */

function ConcreteInlineStyleCustomizer($element, options) {
    var my = this
    options = $.extend({
    }, options)

    my.options = options
    my.$element = $element
    my.$toolbar = my.$element.find('>ul')

    my.setupForm()
    my.setupButtons()
    my.setupSliders()
    my.setupDeviceVisibilityComponent()
    my.setupDropdowns()
    my.setupSelectBoxes()
}

ConcreteInlineStyleCustomizer.prototype = {

    refreshStyles: function(resp) {
        if (resp.oldIssID) {
            $('head').find('style[data-style-set=' + resp.oldIssID + ']').remove()
        }
        if (resp.issID && resp.css) {
            $('head').append(resp.css)
        }
    },

    setupForm: function() {
        var my = this
        my.$element.find('.launch-tooltip').tooltip()
        my.$element.concreteAjaxForm({
            success: function(resp) {
                my.handleResponse(resp)
            },
            error: function(r) {
                my.$toolbar.prependTo('#ccm-inline-toolbar-container').show()
            }
        })
    },

    setupButtons: function() {
        var my = this
        my.$toolbar.on('click.inlineStyleCustomizer', 'input[data-action=cancel-design]', function() {
            my.$element.hide()
            ConcreteEvent.fire('EditModeExitInline')
            return false
        })

        my.$toolbar.on('click.inlineStyleCustomizer', 'input[data-action=reset-design]', function() {
            $.concreteAjax({
                url: $(this).attr('data-reset-action'),
                success: function(resp) {
                    my.handleResponse(resp)
                }
            })
            return false
        })

        my.$toolbar.on('click.inlineStyleCustomizer', 'input[data-action=save-design]', function() {
            // move the toolbar back into the form so it submits. so great.
            my.$toolbar.hide().prependTo(my.$element)
            my.$element.submit()
            ConcreteEvent.unsubscribe('EditModeExitInlineComplete')
            return false
        })
    },

    setupDropdowns: function() {
        var my = this
        my.$toolbar.find('.ccm-inline-toolbar-icon-cell > a').on('click', function () {
            const $dropdown = $(this).parent().find('.ccm-dropdown-menu')
            const isActive = $dropdown.hasClass('active')
            my.$toolbar.find('.ccm-inline-toolbar-icon-selected').removeClass('ccm-inline-toolbar-icon-selected')

            $('.ccm-dropdown-menu').removeClass('active')

            if (!isActive) {
                $dropdown.addClass('active')
                $(this).parent().addClass('ccm-inline-toolbar-icon-selected')
            }
        })
    },

    setupDeviceVisibilityComponent: function() {
        var my = this
        my.$toolbar.find('button[data-hide-on-device]').on('click', function (e) {
            e.stopPropagation()

            const input = $(this).attr('data-hide-on-device')

            if ($(this).hasClass('active')) {
                $(this).removeClass('active')
                $($('input[data-hide-on-device-input=' + input + ']').val(1))
            } else {
                $(this).addClass('active')
                $($('input[data-hide-on-device-input=' + input + ']').val(0))
            }
        })
    },

    setupSelectBoxes: function() {
        var my = this

        my.$toolbar.find('.selectpicker').selectpicker()

        var $customClass = my.$toolbar.find('#customClass')

        $customClass.selectpicker({
            liveSearch: true,
            allowAdd: true // new option used by our extension of bootstrap-select
        })
    },

    setupSliders: function() {
        var my = this
        my.$toolbar.find('.ccm-inline-style-sliders').each(function() {
            var targetInput = $(this).next().children('.ccm-inline-style-slider-value')
            var targetInputFormat = targetInput.attr('data-value-format')
            var sliderElement = $(this)
            var min = parseInt($(this).attr('data-style-slider-min'))
            var max = parseInt($(this).attr('data-style-slider-max'))
            var defaultValue = $(this).attr('data-style-slider-default-setting')
            var currentValue = function () {
                return parseInt(targetInput.val().replace(/\D-/g, ''))
            }
            var disableCheck = function () {
                if (parseInt(defaultValue) === currentValue() || isNaN(currentValue())) {
                    targetInput.prop('disabled', true).val(defaultValue + targetInputFormat)
                }
            }

            sliderElement.slider({
                min: min,
                max: max,
                value: currentValue(),
                slide: function(event, ui) {
                    targetInput.prop('disabled', false)
                    targetInput.val(ui.value + targetInputFormat)
                    disableCheck()
                }
            })

            targetInput.change(function () {
                var value = currentValue()

                if (value > max) {
                    value = max
                } else if (value < min) {
                    value = min
                } else if (isNaN(value)) {
                    value = defaultValue
                }

                $(this).val(value + targetInputFormat)
                sliderElement.slider('value', value)
                disableCheck()
            }).blur(function () {
                disableCheck()
            }).parent().click(function () {
                if (targetInput.prop('disabled')) {
                    targetInput.prop('disabled', false).select()
                }
            })

            disableCheck()
        })
    }

}

function ConcreteBlockInlineStyleCustomizer($element, options) {
    var my = this
    ConcreteInlineStyleCustomizer.call(my, $element, options)
}

ConcreteBlockInlineStyleCustomizer.prototype = Object.create(ConcreteInlineStyleCustomizer.prototype)

ConcreteBlockInlineStyleCustomizer.prototype.handleResponse = function(resp) {
    var my = this
    var editor = new Concrete.getEditMode()
    var area = editor.getAreaByID(resp.aID)
    var block = area.getBlockByID(parseInt(resp.originalBlockID))
    var arEnableGridContainer = area.getEnableGridContainer() ? 1 : 0
    var action = CCM_DISPATCHER_FILENAME + '/ccm/system/block/render'

    $.get(action, {
        arHandle: area.getHandle(),
        cID: resp.cID,
        bID: resp.bID,
        arEnableGridContainer: arEnableGridContainer
    }, function (r) {
        ConcreteToolbar.disableDirectExit()
        var newBlock = block.replace(r)
        ConcreteAlert.notify({
            message: resp.message
        })

        my.refreshStyles(resp)
        ConcreteEvent.fire('EditModeExitInline', {
            action: 'save_inline',
            block: newBlock
        })
        ConcreteEvent.fire('EditModeExitInlineComplete', {
            block: newBlock
        })
        $.fn.dialog.hideLoader()
        editor.destroyInlineEditModeToolbars()
        editor.scanBlocks()
    })
}

function ConcreteAreaInlineStyleCustomizer($element, options) {
    var my = this
    ConcreteInlineStyleCustomizer.call(my, $element, options)
}

ConcreteAreaInlineStyleCustomizer.prototype = Object.create(ConcreteInlineStyleCustomizer.prototype)

ConcreteAreaInlineStyleCustomizer.prototype.handleResponse = function(resp) {
    var my = this
    var editor = new Concrete.getEditMode()
    var area = editor.getAreaByID(resp.aID)
    my.refreshStyles(resp)
    area.getElem().removeClassExcept('ccm-area ccm-global-area')
    if (resp.containerClass) {
        area.getElem().addClass(resp.containerClass)
    }
    editor.destroyInlineEditModeToolbars()
}

// jQuery Plugin
$.fn.concreteBlockInlineStyleCustomizer = function (options) {
    return $.each($(this), function (i, obj) {
        new ConcreteBlockInlineStyleCustomizer($(this), options)
    })
}

$.fn.concreteAreaInlineStyleCustomizer = function (options) {
    return $.each($(this), function (i, obj) {
        new ConcreteAreaInlineStyleCustomizer($(this), options)
    })
}

$.fn.concreteInlineStyleCustomizer = function (options) {
    return $.each($(this), function (i, obj) {
        if ($(this).data('targetElement') === 'block') {
            new ConcreteBlockInlineStyleCustomizer($(this), options)
        } else {
            new ConcreteAreaInlineStyleCustomizer($(this), options)
        }
    })
}

$.fn.removeClassExcept = function (val) {
    return this.each(function (index, el) {
        var keep = val.split(' ')  // list we'd like to keep
        var reAdd = [] // ones that should be re-added if found
        var $el = $(el) // element we're working on

        // look for which we re-add (based on them already existing)
        for (var i = 0; i < keep.length; i++) {
            if ($el.hasClass(keep[i])) reAdd.push(keep[i])
        }

        // drop all, and only add those confirmed as existing
        $el
            .removeClass() // remove existing classes
            .addClass(reAdd.join(' '))  // re-add the confirmed ones
    })
}

global.ConcreteBlockInlineStyleCustomizer = ConcreteBlockInlineStyleCustomizer
global.ConcreteAreaInlineStyleCustomizer = ConcreteAreaInlineStyleCustomizer
