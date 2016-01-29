(function (window, $) {
    "use strict";

    /**
     * Parallax class
     *
     *     var parallax = new Parallax(container_div, { speed: 0.25 });
     *     parallax.render();
     *
     * Settings takes:
     * * speed: float between 0 and 1, 0 has no effect, 1 appears stationary. Sweet spot is between 0.2 and 0.7
     * * window: The nearest scrolling parent element to calculate the parallax against, defaults to the window
     *
     * @param HTMLElement element The container
     * @param object settings The settings
     * @constructor
     */
    var Parallax = function Parallax(element, settings) {
        this.$element = $(element);
        this.$image = null;
        this.image = null;

        // Apply the default settings
        this.settings = $.fn.extend({
            variation: 0,
            speed: 0.25,
            window: window
        }, settings);

        /**
         *
         */
        if (this.settings.variation) {
            // Always override speed if the variation is set.
            this.settings.speed = this.settings.variation / this.$element.height() / 5
        }

        this.$window = $(this.settings.window);
    };

    Parallax.prototype = {

        /**
         * Initialize the parallax, this method must be called for the parallax to begin loading.
         */
        init: function () {

            var my = this,
                outer = $('<div/>').addClass('parallax-image-container'),
                image = new Image();

            image.onload = function () {
                my.image = image;
                my.$image = $(image).addClass('parallaxic-image');

                my.bindListeners();

                window.setTimeout(function() {
                    // defer because some browsers fire the event before populating the image attributes
                    my.handleResize();
                    my.render();

                    outer.append(my.$image.hide().fadeIn());
                }, 0);
            };

            image.src = this.$element.data('background-image');

            this.$element.addClass('parallaxic-container').prepend(outer);
        },

        /**
         * Bind event listeners
         */
        bindListeners: function () {
            var obj = this;
            this.$window.on('resize', function (e) {
                obj.handleResize();
                obj.render();
            }).on('scroll', function (e) {
                obj.handleScroll();
                obj.render();
            });
        },

        /**
         * Handle browser resize
         */
        handleResize: function () {
            var frame = this.getFrame();
            frame.determineScale(this);
            frame.determineOffsetLeft(this);
            this.handleScroll();
        },

        /**
         * Handle the browser scroll
         */
        handleScroll: function () {
            var frame;
            if (this.isVisible()) {
                frame = this.getFrame();

                frame.determineOffsetTop(this);
                frame.determineParallax(this);
            }
        },

        /**
         * Is this parallax visible
         * @returns {boolean}
         */
        isVisible: function () {
            var window_top = this.$window.scrollTop(),
                window_height = this.$window.height(),
                container_top = this.$element.offset().top,
                container_height = this.$element.height();

            return (window_top + window_height > container_top && window_top < container_top + container_height);
        },

        /**
         * Render the parallax
         */
        render: function () {
            this.getFrame().render(this);
        },

        /**
         * Get the ParallaxFrame object
         * @returns ParallaxFrame
         */
        getFrame: function () {
            if (!this.frame) {
                this.frame = new ParallaxFrame();
            }

            return this.frame;
        }

    };

    /**
     * The class used for managing parallax state change
     * @constructor
     */
    var ParallaxFrame = function ParallaxFrame() {
        this.scale = 1;
        this.offset = {x: 0, y: 0};
    };

    ParallaxFrame.prototype = {

        /**
         * Determine the images scale to properly satisfy the requirements
         * @param parallax
         */
        determineScale: function (parallax) {
            var image_height = parallax.image.height,
                image_width = parallax.image.width,
                window_height = parallax.$window.height(),
                container_height = parallax.$element.height(),
                container_width = parallax.$element.width(),
                speed = parallax.settings.speed,
                required_padding = speed * (window_height + container_height),
                padded_container_height = container_height + required_padding,
                ratio;

            // If we aren't wide enough, or we are too tall
            if (image_width < container_width || image_height > padded_container_height) {
                // Get the ratio of the container width to the base image width
                ratio = container_width / image_width;

                // If we scale by the ratio, will we be tall enough?
                if (image_height * ratio >= padded_container_height) {
                    this.scale = ratio;
                } else {
                    // Since we weren't tall enough, scale by the height ratio
                    this.scale = padded_container_height / image_height;
                }
            } else {
                // We're too wide and not tall enough, lets just scale by the height ratio
                this.scale = padded_container_height / image_height;
            }

            this.scale = Math.ceil(this.scale * 1000) / 1000;
        },

        /**
         * Figure out how far the image needs to be from the left
         * We need to do this because the image scales away from the center, not the top left
         * @param parallax
         */
        determineOffsetLeft: function (parallax) {
            var image_width = parallax.image.width,
                container_width = parallax.$element.width(),
                scaled_width = image_width * this.scale,
                width_difference = (scaled_width - image_width) + (container_width - scaled_width);

            this.offset.x = width_difference / 2;
        },

        /**
         * Figure out how far the image needs to be from the top
         * We need to do this because the image scales away from the center, not the top left
         * @param parallax
         */
        determineOffsetTop: function (parallax) {
            var image_height = parallax.image.height,
                scaled_height = image_height * this.scale,
                height_difference = scaled_height - image_height;

            this.offset.y = height_difference / 2;
        },

        /**
         * Modify the offset to simulate parallax effect
         * @param parallax
         */
        determineParallax: function (parallax) {
            var speed = parallax.settings.speed,
                $container = parallax.$element,
                container_offset = $container.offset(),
                window_scroll = parallax.$window.scrollTop(),
                distance_to_top;

            distance_to_top = window_scroll - container_offset.top;
            this.offset.y += distance_to_top * speed;
        },

        /**
         * Set the actual CSS to change the positioning of this image
         * @param parallax
         */
        render: function (parallax) {
            var transformation = "";

            // Apply translation
            transformation += "translate3d(" + this.offset.x + "px," + this.offset.y + "px, 0) ";

            // Apply scale
            transformation += "scale3d(" + this.scale + "," + this.scale + "," + this.scale + ")";

            parallax.$image.get(0).style.transform = transformation;
        }
    };

    /**
     * Jquery parallaxize extension, easily create or retrieve a parallax instance with jQuery.
     *
     * @param object settings
     * @return Parallax
     */
    $.fn.parallaxize = function Parallaxize(settings) {
        var me = $(this), parallax;
        if (!me.data('parallax')) {
            parallax = new Parallax(this, settings);
            $(this).data('parallax', parallax);
            parallax.init();
        } else {
            parallax = me.data('parallax');
        }

        return parallax;
    };

}(this, jQuery));
