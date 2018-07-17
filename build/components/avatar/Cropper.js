import Dropzone from 'dropzone'

// Disable dropzone discovery
Dropzone.autoDiscover = false;

export default {
    name: "avatar-cropper",
    props: [
        'width',
        'height',
        'uploadurl',
        'src'
    ],
    data: function () {
        return {
            'img': null,
            'x': 10,
            'y': 5,
            'cropWidth': 0,
            'cropHeight': 0,
            'imageHeight': 0,
            'imageWidth': 0,
            'saving': false,
            'currentimage': null
        }
    },
    mounted() {
        window.d = this;

        // Attach the current image
        this.currentimage = this.src;

        // Setup Uploading
        this.setupUploading();
    },
    methods: {
        setupUploading() {
            if (this.dropzone) {
                return;
            }

            let component = this;
            this.dropzone = new Dropzone(this.$refs.dropzone, {
                url: this.uploadurl,
                maxFiles: 1,
                previewTemplate: '<span></span>',
                transformFileSync: false,
                accept(file, done) {
                    component.file = file;
                    component.done = done;
                },
                transformFile(file, done) {
                    return component.finalize(file, done);
                },
                init() {
                    // Capture thumbnail details
                    this.on('thumbnail', function () {
                        component.img = component.file.dataURL;
                        component.imageWidth = component.file.width;
                        component.imageHeight = component.file.height;
                    });

                    this.on('success', function(event, data) {
                        component.currentimage = data.avatar;
                    })

                    this.on('complete', function() {
                        component.saving = false;
                        component.img = null;
                        component.dropzone.destroy();
                        component.dropzone = null;

                        setTimeout(function() {
                            component.setupUploading();
                        }, 0);
                    });
                },
                /**
                 * Request full size thumbnails
                 * @param file
                 * @param width
                 * @param height
                 * @returns {{srcWidth, srcHeitght, trgWidth, trgHeitght}}
                 */
                resize(file, width, height) {
                    return {
                        srcWidth: file.width,
                        srcHeight: file.height,
                        trgWidth: file.width,
                        trgHeight: file.height
                    }
                }
            });
        },
        finalize(file, done) {
            let canvas = document.createElement('canvas'),
                ctx = canvas.getContext('2d'),
                img = new Image();

            img.src = file.dataURL;

            canvas.width = this.width;
            canvas.height = this.height;

            // Draw the image cropped
            ctx.drawImage(
                img, this.$refs.image.x, this.$refs.image.y,
                this.$refs.image.resizeWidth, this.$refs.image.resizeHeight);

            this.saving = true;
            // Complete the upload
            let data = canvas.toDataURL(),
                result = done(Dropzone.dataURItoBlob(data));

            this.currentimage = data;
            return result;
        },
        attachShadow() {
            // Attach the shadow
            this.$refs.shadow.setViewport(this.$refs.image);
        },
        handleOkay() {
            this.done.call(this.dropzone);
        },
        handleCancel() {
            if (window.confirm('Are you sure you want to quit?')) {
                this.done.call(this.dropzone, 'Cancelled upload.');
                this.img = null;
                this.dropzone.destroy();
                this.dropzone = null;
                this.setupUploading();
            }
        }
    },
    components: {
        'avatar-image': require('./Avatar.vue')
    }
}
