import Dropzone from 'dropzone'
import Avatar from './Avatar.vue'

// Disable dropzone discovery
Dropzone.autoDiscover = false

export default {

    // Properties tied to our parent
    props: {
        width: Number,
        height: Number,
        uploadurl: String,
        src: String
    },

    // Our internal state
    data: function () {
        return {
            img: null,
            x: 10,
            y: 5,
            cropWidth: 0,
            cropHeight: 0,
            imageHeight: 0,
            imageWidth: 0,
            saving: false,
            currentimage: null,
            hasError: false,
            errorMessage: ''
        }
    },

    /**
     * Handle mounting to our element
     */
    mounted() {
        // Attach the current image
        this.currentimage = this.src

        // Setup Uploading
        this.setupUploading()
    },
    methods: {
        setError(error) {
            this.hasError = true
            this.errorMessage = error
        },
        clearError() {
            this.hasError = false
            this.errorMessage = ''
        },
        /**
         * Setup dropzone for uploading
         */
        setupUploading() {
            if (this.dropzone) {
                return
            }

            const component = this
            this.dropzone = new Dropzone(this.$refs.dropzone, {
                url: this.uploadurl,
                maxFiles: 1,
                previewTemplate: '<span></span>',
                transformFileSync: false,

                // Accept uploaded files from user
                accept(file, done) {
                    component.file = file
                    component.done = done
                },

                // Give the component a chance to finalize the file
                transformFile(file, done) {
                    return component.finalize(file, done)
                },

                // Initialize dropzone
                init() {
                    // Capture thumbnail details
                    this.on('thumbnail', function () {
                        component.img = component.file.dataURL
                        component.imageWidth = component.file.width
                        component.imageHeight = component.file.height
                    })

                    this.on('success', function(event, data) {
                        if (!component.hasError) {
                            component.currentimage = data.avatar
                        }
                    })

                    this.on('error', function (event, data) {
                        component.setError(data.message)
                        component.currentimage = component.src
                    })

                    this.on('complete', function() {
                        component.saving = false
                        component.img = null
                        component.dropzone.destroy()
                        component.dropzone = null

                        setTimeout(function () {
                            component.setupUploading()
                        }, 0)
                    })
                },

                /**
                 * Request full size thumbnails
                 * @param file
                 * @param int width
                 * @param int height
                 * @returns {{int srcWidth, int srcHeight, int trgWidth, int trgHeight}}
                 */
                resize(file, width, height) {
                    return {
                        srcWidth: file.width,
                        srcHeight: file.height,
                        trgWidth: file.width,
                        trgHeight: file.height
                    }
                }
            })
        },

        /**
         * Handle finalizing a user provided image.
         * This is where we actually do the cropping and rendering.
         *
         * @param file
         * @param bool done
         * @returns {*}
         */
        finalize(file, done) {
            const canvas = document.createElement('canvas')
            const ctx = canvas.getContext('2d')
            const img = new Image()

            img.src = file.dataURL

            canvas.width = this.width
            canvas.height = this.height

            // Draw the image cropped
            ctx.drawImage(
                img, this.$refs.image.x, this.$refs.image.y,
                this.$refs.image.resizeWidth, this.$refs.image.resizeHeight)

            this.saving = true
            // Complete the upload
            const data = canvas.toDataURL()
            const result = done(Dropzone.dataURItoBlob(data))

            this.currentimage = data
            return result
        },

        /**
         * Attach our shadow and our image together
         */
        attachShadow() {
            // Attach the shadow
            this.$refs.shadow.setViewport(this.$refs.image)
        },

        /**
         * Handle checkmark click
         */
        handleOkay() {
            this.done.call(this.dropzone)
        },

        /**
         * Handle x mark click
         */
        handleCancel() {
            if (window.confirm('Are you sure you want to quit?')) {
                this.done.call(this.dropzone, 'Cancelled upload.')
                this.img = null
                this.saving = false
                this.dropzone.destroy()
                this.clearError()
                this.dropzone = null
                this.setupUploading()
            }
        }
    },
    components: {
        Avatar
    }
}
