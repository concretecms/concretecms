$.fn.parallaxize = (function (global, $) {
    'use strict';

    function Parallax() {
        this.init.apply(this, Array.prototype.slice.call(arguments));
    }

    Parallax.prototype = {
        init: function (parent, settings) {
            this.initializeConfig(settings);
            this.config('element', parent);
            this.config('width', 1);
            this.config('height', 1);

            this.initializeDOM();
            this.initializeBindings();
        },

        initializeConfig: function (settings) {
            var config = {};
            settings = $.fn.extend({variation: 20}, settings);

            var getGetter = function (object) {
                return function (key, value) {
                    if (typeof key === 'undefined') {
                        return object;
                    }
                    if (typeof value === 'undefined') {
                        return object[key];
                    }
                    return object[key] = value;
                }
            };

            this.config = getGetter(config);
            this.setting = getGetter(settings);
        },

        initializeDOM: function () {
            var elem = this.config('element'),
                image_container = $('<div/>'),
                image = new Image(),
                image_elem = $(image).appendTo(image_container),
                me = this;
            elem.prepend(image_container);
            image_elem.hide();

            var synchronous = true;
            image.onload = function () {
                me.config('height', image_elem.height());
                me.config('width', image_elem.width());
                me.translate(false);

                if (synchronous) {
                    image_elem.show();
                } else {
                    image_elem.fadeIn(250);
                }
            };
            image.src = elem.data('background-image');
            setTimeout(function () {
                synchronous = false;
            }, 150);
            this.config('image', image_elem);

            elem.addClass('parallaxic-container');
            image_elem.addClass('parallaxic-image');
            image_container.addClass('parallax-image-container');

        },

        initializeBindings: function () {
            var me = this;
            $(global).scroll(function () {
                me.translate();
            });

            $(global).resize(function () {
                me.translate(false);
            });
        },

        translate: function (cache) {
            var transform = [
                this.resizeTranslation(cache),
                this.positionTranslation()
            ].join(' ');

            this.config('image').css({
                transform: transform,
                '-webkit-transform': transform,
                '-moz-transform': transform,
                '-o-transform': transform
            });
        },

        resizeTranslation: function (cached) {
            var scale;
            if (cached !== false) {
                scale = this.config('scale');
                if (scale) {
                    return 'scale(' + scale + ')';
                }
            }

            var variation = this.setting('variation'),
                elem = this.config('element'),
                image = this.config('image'),
                height = elem.height(),
                image_height = this.config('height'), image_width = this.config('width'),
                potential_height = height + variation * 2,
                new_width = elem.width(), new_height;

            new_height = (image_height / image_width) * new_width;

            if (new_height < potential_height) {
                new_height = potential_height;
                new_width = (image_width / image_height) * new_height;
            }

            scale = Math.ceil(new_height / image_height * 1000) / 1000;

            //if (Modernizr && !Modernizr.csstransforms) {
            //    image.css({
            //        width: new_width,
            //        height: new_height
            //    });
            //    return '';
            //}

            this.config('scale', scale);
            return 'scale(' + scale + ')';
        },

        positionTranslation: function () {

            var variation = this.setting('variation'),
                scale = this.config('scale'),
                elem = this.config('element'),
                image = this.config('image'),
                height = elem.height(), width = elem.width(),
                image_height = this.config('height'), image_width = this.config('width'),
                top = (height - image_height) / 2,
                left = (width - image_width) / 2,
                scroll_percentage = this.getScrollPercentage();

            top += (variation * 2 * scroll_percentage) - variation;

            // First assume translate
            //var method_start = 'translate(', method_end = ')';
            //
            //if (Modernizr) {
            //    if (Modernizr.csstransforms3d) {
            //        method_start = 'translate3d(';
            //        method_end = ', 0px)';
            //    } else if (!Modernizr.csstransforms) {
            //        return '';
            //    }
            //}
            //return method_start + Math.round(left) + 'px, ' + Math.round(top) + 'px' + method_end;

            return ['translate3d(', Math.round(left / scale), 'px, ', Math.round(top), 'px, ', '0px)'].join('');
        },

        getScrollPercentage: function () {
            var elem = this.config('element'),
                image = this.config('image'),
                main = $(global),
                min = elem.offset().top - main.height(),
                max = elem.offset().top + elem.height() - min,
                scroll = main.scrollTop() - min;

            return Math.max(0, Math.min(1, scroll / max));
        }

    };

    return function (settings) {
        this.data('parallax', new Parallax(this, settings))
    };

}(window, $));
