import interact from 'interactjs'

export default {

    // The properties available for our parent to edit
    props: {
        img: String,
        imageHeight: Number,
        imageWidth: Number,
        cropperWidth: Number,
        cropperHeight: Number,
        shadow: Boolean
    },

    // Our state
    data() {
        return {
            x: 0,
            y: 0,
            adjX: 0,
            adjY: 0,
            resizeHeight: 0,
            resizeWidth: 0,
            viewport: null,
            outer: null
        }
    },

    /**
     * Prepare to render by setting up our viewport
     */
    beforeUpdate() {
        if (this.viewport) {
            this.viewport.x = this.x
            this.viewport.y = this.y
            this.viewport.adjX = this.adjX
            this.viewport.adjY = this.adjY
            this.viewport.resizeWidth = this.resizeWidth
            this.viewport.resizeHeight = this.resizeHeight
        }
    },

    /**
     * Once we are attached
     */
    mounted() {
        if (this.shadow === true) {
            this.guessPosition()
            this.setupResizing()
            this.setupDragging()
        }

        // Emit an event
        this.$emit('mount', this)
    },

    methods: {

        /**
         */
        guessPosition() {
            const adjustedHeight = this.adjustedDimensions(this.imageWidth, this.imageHeight)
            let adjX = 0; let adjY = 0

            this.resizeHeight = adjustedHeight.height
            this.resizeWidth = adjustedHeight.width

            if (this.resizeWidth > this.cropperWidth) {
                adjX = -Math.round((this.resizeWidth - this.cropperWidth) / 2)
            }
            if (this.resizeHeight > this.cropperHeight) {
                adjY = -Math.round((this.resizeHeight - this.cropperHeight) / 2)
            }

            const coords = this.adjustedCoordinates(adjX, adjY, this.resizeWidth, this.resizeHeight)
            this.x += coords.x
            this.y += coords.y
        },

        /**
         * Make the avatar resizable
         */
        setupResizing() {
            const me = this
            this.interact = interact(this.$refs.image)
                .resizable({
                    preserveAspectRatio: true,
                    edges: { left: true, right: true, bottom: true, top: true }
                })
                .on('resizemove', function (event) {
                    return me.handleResizeMove(event)
                })
        },

        /**
         * Handle dimensions adjusting
         * @param int width
         * @param int height
         */
        adjustedDimensions(width, height) {
            let bestFactor = 1
            const maxFactor = Math.sqrt(Math.min(width, height))

            // Find the best factor to downsize by
            for (let i = 2; i <= maxFactor; i++) {
                if ((width / i) % 2 === 0 && (height / i) % 2 === 0) {
                    if ((width / i) > this.cropperWidth && (height / i) > this.cropperHeight) {
                        bestFactor = i
                    }
                }
            }

            return {
                width: width / bestFactor,
                height: height / bestFactor,
                factor: bestFactor,
                adjusted: bestFactor !== 1
            }
        },

        /**
         * Handle coordinates adjusting
         * @param int x
         * @param int y
         * @param int width
         * @param int height
         */
        adjustedCoordinates(x, y, width, height) {
            const renderedX = this.x + x
            const renderedY = this.y + y
            const coords = {
                min: {
                    x: -1 * (width - this.cropperWidth),
                    y: -1 * (height - this.cropperHeight)
                },
                max: {
                    x: 0,
                    y: 0
                }
            }
            const adjustedX = Math.max(coords.min.x, Math.min(coords.max.x, renderedX)) - this.x
            const adjustedY = Math.max(coords.min.y, Math.min(coords.max.y, renderedY)) - this.y

            return {
                x: adjustedX,
                y: adjustedY,
                adjusted: adjustedY !== y || adjustedX !== x
            }
        },

        /**
         * Attach a parent Avatar if we're a shadow
         * @param Avatar viewport
         */
        setViewport(viewport) {
            this.viewport = viewport
            viewport.outer = this
            viewport.setupDragging()
        },

        /**
         * Setup interactjs dragging
         */
        setupDragging() {
            const me = this
            this.interact = interact(this.$refs.image)
                .draggable({
                    intertia: false,
                    restrict: false,

                    // Send on move events to component
                    onmove: function (e) {
                        if (me.outer) {
                            return me.outer.handleDragMove(e)
                        }
                        return me.handleDragMove(e)
                    },

                    // Send onstart events to component
                    onstart: function (e) {
                        if (me.outer) {
                            return me.outer.handleDragStart(e)
                        }
                        return me.handleDragStart(e)
                    },

                    // Send onend events to component
                    onend: function (e) {
                        if (me.outer) {
                            return me.outer.handleDragEnd(e)
                        }
                        return me.handleDragEnd(e)
                    }
                })
        },

        /**
         * Handle interactjs drag event
         * @param event
         */
        handleDragMove(event) {
            const coords = this.adjustedCoordinates(
                event.pageX - this.startEvent.pageX, event.pageY - this.startEvent.pageY,
                this.resizeWidth, this.resizeHeight)

            this.adjX = coords.x
            this.adjY = coords.y
        },

        /**
         * Handle interactjs starting to drag
         * @param Event event
         */
        handleDragStart(event) {
            this.startEvent = event
            this.coords = {
                min: {
                    x: -this.resizeWidth + this.cropperWidth,
                    y: -this.resizeHeight + this.cropperHeight
                },
                max: {
                    x: 0,
                    y: 0
                }
            }
        },

        /**
         * Handle interactjs stopping dragging
         * @param event
         */
        handleDragEnd(event) {
            this.x += this.adjX
            this.y += this.adjY
            this.adjX = 0
            this.adjY = 0
        },

        /**
         * Handle resizing
         * @param event
         */
        handleResizeMove(event) {
            const coordinates = this.adjustedCoordinates(
                event.deltaRect.left, event.deltaRect.top,
                event.rect.width, event.rect.height)

            // Don't resize too small
            if (event.rect.width < this.cropperWidth ||
                event.rect.height < this.cropperHeight) {
                // If the image is square
                if (this.imageWidth === this.imageHeight) {
                    this.resizeWidth = this.cropperWidth
                    this.resizeHeight = this.cropperHeight
                    this.x = 0
                    this.y = 0
                }
                return
            }

            // update the element's style
            this.resizeWidth = Math.max(event.rect.width, this.cropperWidth)
            this.resizeHeight = Math.max(event.rect.height, this.cropperHeight)

            // translate when resizing from top or left edges
            this.x += coordinates.x
            this.y += coordinates.y
        }
    }
}
