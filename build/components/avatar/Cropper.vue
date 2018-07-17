<template>
    <div class="ccm-avatar-creator-container">
        <avatar-image ref="shadow" v-if="img !== null"
                      @mount="attachShadow"
                      :shadow="true"
                      :img="img"
                      :imageHeight="imageHeight"
                      :imageWidth="imageWidth"
                      :cropperWidth="width"
                      :cropperHeight="height" />
        <div ref='dropzone'
             class="ccm-avatar-creator"
             :style="{width: width + 'px', height: height + 'px' }"
             :class="{editing: img !== null}">
            <avatar-image ref="image" v-if="img"
                          :img="img"
                          :imageHeight="imageHeight"
                          :imageWidth="imageWidth"
                          :cropperWidth="width"
                          :cropperHeight="height" />
            <img class="ccm-avatar-current" v-if="!img" :src="currentimage"/>
            <div class="saving"
                 v-if="saving"
                 :style="{lineHeight: height + 'px' }">
                <i class="fa fa-spin fa-spinner fa-circle-o-notch"></i>
            </div>
        </div>
        <div class="ccm-avatar-actions" v-if="img">
            <a class="ccm-avatar-okay" :style="{width: width / 2 + 'px'}" @click="handleOkay"></a>
            <a class="ccm-avatar-cancel" :style="{width: width / 2 + 'px'}" @click="handleCancel"></a>
        </div>
        <canvas ref="canvas"></canvas>
    </div>
</template>

<script>
    import Dropzone from 'dropzone'

    // Disable dropzone discovery
    Dropzone.autoDiscover = false;

    export default {
        // Our element tagname
        name: "avatar-cropper",

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
                uploadurl: null,
                src: null,
                width: null,
                height: null
            }
        },


        /**
         * Handle mounting to our element
         */
        mounted() {
            // Attach the current image
            this.currentimage = this.src;

            // Setup Uploading
            this.setupUploading();
        },
        methods: {

            /**
             * Setup dropzone for uploading
             */
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

                    // Accept uploaded files from user
                    accept(file, done) {
                        component.file = file;
                        component.done = done;
                    },

                    // Give the component a chance to finalize the file
                    transformFile(file, done) {
                        return component.finalize(file, done);
                    },

                    // Initialize dropzone
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
                     * @param int width
                     * @param int height
                     * @returns {{int srcWidth, int srcHeitght, int trgWidth, int trgHeitght}}
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

            /**
             * Handle finalizing a user provided image.
             * This is where we actually do the cropping and rendering.
             *
             * @param file
             * @param bool done
             * @returns {*}
             */
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

            /**
             * Attach our shadow and our image together
             */
            attachShadow() {
                // Attach the shadow
                this.$refs.shadow.setViewport(this.$refs.image);
            },

            /**
             * Handle checkmark click
             */
            handleOkay() {
                this.done.call(this.dropzone);
            },

            /**
             * Handle x mark click
             */
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
</script>

<style lang="less" scoped>
    .ccm-avatar-creator-container {
        position: relative;

        .ccm-avatar-actions {
            z-index: 20000;
            position: absolute;

            > a {
                width: 50%;
                height: 16px;
                display: inline-block;
                text-align: center;
                opacity: 0.3;
                transition: all 0.5s;
                text-decoration: none;

                &:hover {
                    opacity: 1;
                }

                &:before {
                    font-size: 16px;
                    font-family: FontAwesome;
                    text-align: center;
                }

                &.ccm-avatar-cancel {
                    float: right;
                    color: #FF4136;
                    &:before {
                        content: '\f00d';
                    }
                }

                &.ccm-avatar-okay {
                    color: #3D9970;
                    &:before {
                        content: '\f00c';
                    }
                }
            }
        }

        .ccm-avatar-creator {
            border: solid 1px #999;
            transition: all 0.3s;
            z-index: 10000;
            overflow: hidden;
            position: relative;

            > img.ccm-avatar-current {
                z-index:998;
                display:inline;
                width: 100%;
                height: 100%;
            }

            > div.saving {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(127,219,255, 0.5);
                font-weight:bolder;
                font-size: 16px;
                text-align: center;
                color: #111111;
            }

            &:before {
                transition: all 0.3s;
                content: '\f093';
                font-family: FontAwesome;
                opacity: 0;
                font-size: 16px;
                margin: 0 auto;
                padding-top: 50%;
                line-height: 0%;
                display: block;
                width: 100%;
                text-align: center;
                height: 100%;
                vertical-align: middle;
                color: #3D9970;
                background-color: rgba(238, 238, 238, 0.8);
                z-index:999;
                position: absolute;
            }

            &.dz-started {
                &:before {
                    opacity: 1;
                    -webkit-animation: pulse 1s infinite;
                    animation: pulse 1s infinite;
                }
            }

            @keyframes pulse {
                0% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.3);
                }
                100% {
                    transform: scale(1);
                }
            }

            &.editing {
                &:before {
                    display: none;
                }
            }

            &.dz-clickable {
                pointer: cursor;
            }

            &.dz-clickable:hover, &.dz-drag-hover {
                border-color: #3D9970;
                box-shadow: 0 0 20px -10px #2ECC40;
                &:before {
                    opacity: 1
                }
            }
        }
    }
</style>
