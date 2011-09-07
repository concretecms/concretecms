// Twitter Bootstrap Twipsies for jQuery
// (c) 2011 dave stone [dave@davecstone.com]
// Based on:
//   twipsy, facebook style tooltips for jquery
//   version 1.0.0a
//   (c) 2008-2010 jason frame [jason@onehackoranother.com]
// Released under the MIT license

(function($) {
    
    function fixTitle($ele) {
        if ($ele.attr('title') || typeof($ele.attr('original-title')) != 'string') {
            $ele.attr('original-title', $ele.attr('title') || '').removeAttr('title');
        }
    }
    
    function Twipsy(element, options) {
        this.$element = $(element);
        this.options = options;
        this.enabled = true;
        fixTitle(this.$element);
    }
    
    Twipsy.prototype = {
        show: function() {
            var title = this.getTitle();
            if (title && this.enabled) {
                var $tip = this.tip();
                
                $tip.find('.twipsy-inner')[this.options.html ? 'html' : 'text'](title);
                $tip[0].className = 'twipsy'; // reset classname in case of dynamic position
                $tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).appendTo(document.body);
                
                var pos = $.extend({}, this.$element.offset(), {
                    width: this.$element[0].offsetWidth,
                    height: this.$element[0].offsetHeight
                });
                
                var actualWidth = $tip[0].offsetWidth, actualHeight = $tip[0].offsetHeight;
                var position = (typeof this.options.position == 'function')
                                ? this.options.position.call(this.$element[0])
                                : this.options.position;
                
                var tp;
                switch (position) {
                    case 'below':
                        tp = {top: pos.top + pos.height + this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
                        break;
                    case 'above':
                        tp = {top: pos.top - actualHeight - this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
                        break;
                    case 'left':
                        tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth - this.options.offset};
                        break;
                    case 'right':
                        tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width + this.options.offset};
                        break;
                }
                
                if (position.length == 2) {
                    if (position.charAt(1) == 'w') {
                        tp.left = pos.left + pos.width / 2 - 15;
                    } else {
                        tp.left = pos.left + pos.width / 2 - actualWidth + 15;
                    }
                }
                
                $tip.css(tp).addClass('twipsy ' + position);
                
                if (this.options.fade) {
                    $tip.stop().css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: this.options.opacity});
                } else {
                    $tip.css({visibility: 'visible', opacity: this.options.opacity});
                }
            }
        },
        
        hide: function() {
            if (this.options.fade) {
                this.tip().stop().fadeOut(function() { $(this).remove(); });
            } else {
                this.tip().remove();
            }
        },
        
        getTitle: function() {
            var title, $e = this.$element, o = this.options;
            fixTitle($e);
            var title, o = this.options;
            if (typeof o.title == 'string') {
                title = $e.attr(o.title == 'title' ? 'original-title' : o.title);
            } else if (typeof o.title == 'function') {
                title = o.title.call($e[0]);
            }
            title = ('' + title).replace(/(^\s*|\s*$)/, "");
            return title || o.fallback;
        },
        
        tip: function() {
            if (!this.$tip) {
                this.$tip = $('<div class="twipsy"></div>').html('<div class="twipsy-arrow"></div><div class="twipsy-inner"/></div>');
            }
            return this.$tip;
        },
        
        validate: function() {
            if (!this.$element[0].parentNode) {
                this.hide();
                this.$element = null;
                this.options = null;
            }
        },
        
        enable: function() { this.enabled = true; },
        disable: function() { this.enabled = false; },
        toggleEnabled: function() { this.enabled = !this.enabled; }
    };
    
    $.fn.twipsy = function(options) {
        
        if (options === true) {
            return this.data('twipsy');
        } else if (typeof options == 'string') {
            return this.data('twipsy')[options]();
        }
        
        options = $.extend({}, $.fn.twipsy.defaults, options);
        
        function get(ele) {
            var twipsy = $.data(ele, 'twipsy');
            if (!twipsy) {
                twipsy = new Twipsy(ele, $.fn.twipsy.elementOptions(ele, options));
                $.data(ele, 'twipsy', twipsy);
            }
            return twipsy;
        }
        
        function enter() {
            var twipsy = get(this);
            twipsy.hoverState = 'in';
            if (options.delayIn == 0) {
                twipsy.show();
            } else {
                setTimeout(function() { if (twipsy.hoverState == 'in') twipsy.show(); }, options.delayIn);
            }
        };
        
        function leave() {
            var twipsy = get(this);
            twipsy.hoverState = 'out';
            if (options.delayOut == 0) {
                twipsy.hide();
            } else {
                setTimeout(function() { if (twipsy.hoverState == 'out') twipsy.hide(); }, options.delayOut);
            }
        };
        
        if (!options.live) this.each(function() { get(this); });
        
        if (options.trigger != 'manual') {
            var binder   = options.live ? 'live' : 'bind',
                eventIn  = options.trigger == 'hover' ? 'mouseenter' : 'focus',
                eventOut = options.trigger == 'hover' ? 'mouseleave' : 'blur';
            this[binder](eventIn, enter)[binder](eventOut, leave);
        }
        
        return this;
        
    };
    
    $.fn.twipsy.defaults = {
        delayIn: 0,
        delayOut: 0,
        fade: false,
        fallback: '',
        position: 'n',
        html: false,
        live: false,
        offset: 0,
        opacity: 0.8,
        title: 'title',
        trigger: 'hover'
    };
    
    // Overwrite this method to provide options on a per-element basis.
    // For example, you could store the position in a 'twipsy-position' attribute:
    // return $.extend({}, options, {position: $(ele).attr('twipsy-position') || 'n' });
    // (remember - do not modify 'options' in place!)
    $.fn.twipsy.elementOptions = function(ele, options) {
        return $.metadata ? $.extend({}, options, $(ele).metadata()) : options;
    };
    
    $.fn.twipsy.autoNS = function() {
        return $(this).offset().top > ($(document).scrollTop() + $(window).height() / 2) ? 'b' : 'a';
    };
    
    $.fn.twipsy.autoWE = function() {
        return $(this).offset().left > ($(document).scrollLeft() + $(window).width() / 2) ? 'l' : 'r';
    };
    
})(jQuery);