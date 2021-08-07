/* eslint-disable no-new, no-unused-vars, camelcase */

(function() {
    function Button(element) {
        this.element = element
    }

    Button.prototype.disable = function() {
        this.element.prop('disabled', true).addClass('disabled')
        return this
    }

    Button.prototype.enable = function() {
        this.element.prop('disabled', false).removeClass('disabled')
        return this
    }

    var routine = function() {
        $('.ccm-block-calendar-event-list-wrapper.unbound').removeClass('unbound').each(function() {
            var my = $(this)
            var previous = new Button($('button[data-cycle=previous]', my))
            var next = new Button($('button[data-cycle=next]', my))
            var page = my.data('page')
            var list = my.children('.ccm-block-calendar-event-list')
            var events = list.children()
            var start = 0
            var container = $('<div />').css({
                position: 'relative',
                overflow: 'hidden'
            })
            var set_container = $('<div />')
            var slider = $('<div />').css({
                position: 'absolute',
                top: 0,
                left: 0
            })
            var sliding = false

            list.replaceWith(container)

            events.slice(start, page).appendTo(set_container.appendTo(container))
            container.height(container.height())

            previous.element.click(function() {
                if (!sliding && start >= page) {
                    sliding = true
                    start -= page

                    var subset = events.slice(start, start + page)

                    slide(-1, subset, function() {
                        sliding = false
                    })

                    if (!start) {
                        previous.disable()
                    }
                    next.enable()
                }

                return false
            })

            next.element.click(function() {
                if (!sliding || start + 1 >= events.length) {
                    sliding = true
                    start += page

                    var subset = events.slice(start, start + page)

                    slide(1, subset, function() {
                        sliding = false
                    })

                    if (start + page >= events.length) {
                        next.disable()
                    }

                    previous.enable()
                }

                return false
            })

            if (!start) {
                previous.disable()
            }

            if (start + page > events.length) {
                next.disable()
            }

            function slide(direction, subset, callback, length) {
                length = length || 750
                slider.empty().append(subset).height(container.height()).width(container.width()).appendTo(container)
                if (direction > 0) {
                    set_container.css({
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        width: container.width()
                    }).animate({
                        left: -container.width()
                    }, length)
                    slider.css('left', container.width()).animate({ left: 0 }, length, function() {
                        set_container.empty().css({
                            position: 'static',
                            left: 0
                        }).append(subset)
                        slider.remove()
                        callback.apply(this, Array.prototype.slice.call(arguments))
                    })
                } else {
                    set_container.css({
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        width: container.width()
                    }).animate({
                        left: container.width()
                    }, length)
                    slider.css('left', -container.width()).animate({ left: 0 }, length, function() {
                        set_container.empty().css({
                            position: 'static',
                            left: 0
                        }).append(subset)
                        slider.remove()
                        callback.apply(this, Array.prototype.slice.call(arguments))
                    })
                }
            }
        })
    }

    if (typeof jQuery !== 'undefined') {
        routine()
    } else {
        window.addEventListener('load', routine)
    }
}())
