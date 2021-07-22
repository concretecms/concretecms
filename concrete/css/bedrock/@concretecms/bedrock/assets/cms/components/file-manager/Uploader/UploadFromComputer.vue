<template>
    <div class="ccm-file-upload-container-wrapper">
        <div class="ccm-file-upload-container" ref="dropzoneElement">
            <div class="dz-default dz-message">
                <button type="button" class="dz-button">
                    <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI0LjEuMiwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkViZW5lXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAxMzIgMTMyIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxMzIgMTMyOyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+Cgkuc3Qwe29wYWNpdHk6MC45O2ZpbGwtcnVsZTpldmVub2RkO2NsaXAtcnVsZTpldmVub2RkO2ZpbGw6I0U2RjVGRjtlbmFibGUtYmFja2dyb3VuZDpuZXcgICAgO30KCS5zdDF7ZmlsbDojNEE5MEUyO30KCS5zdDJ7ZmlsbDpub25lO3N0cm9rZTojRThGNkZGO3N0cm9rZS13aWR0aDo4O30KPC9zdHlsZT4KPGcgaWQ9IlBhZ2UtMSI+Cgk8ZyBpZD0iRmlsZS1NYW5hZ2VyLS0tRmlsZVNldHMtRHJvcGRvd24iIHRyYW5zZm9ybT0idHJhbnNsYXRlKC02NTUuMDAwMDAwLCAtNjY3LjAwMDAwMCkiPgoJCTxyZWN0IGlkPSJSZWN0YW5nbGUiIHg9IjEiIHk9IjQ2OCIgY2xhc3M9InN0MCIgd2lkdGg9IjE0NDAiIGhlaWdodD0iNTQwIi8+CgkJPGcgaWQ9Imljb25zOC1kcmFnLWFuZC1kcm9wIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSg2NTUuMDAwMDAwLCA2NjcuMDAwMDAwKSI+CgkJCTxwYXRoIGlkPSJTaGFwZSIgY2xhc3M9InN0MSIgZD0iTTMuMiwwYzAsMC0wLjEsMC0wLjEsMEMzLjEsMCwzLDAsMi45LDBDMi44LDAsMi43LDAuMSwyLjYsMC4xQzIuNCwwLjIsMi4xLDAuMiwxLjgsMC4zCgkJCQlDMS43LDAuNCwxLjUsMC41LDEuNCwwLjZDMS4yLDAuOCwxLDAuOSwwLjksMUMwLjgsMS4xLDAuNywxLjMsMC42LDEuNEMwLjUsMS42LDAuMywxLjgsMC4yLDIuMUMwLjIsMi4yLDAuMiwyLjQsMC4xLDIuNQoJCQkJQzAuMSwyLjcsMCwyLjksMCwzLjF2MTMuMWMwLDEuNywxLjQsMy4xLDMuMiwzLjFzMy4yLTEuNCwzLjItMy4xVjYuNGgxM2MxLjcsMCwzLjEtMS40LDMuMS0zLjJTMjEuMSwwLDE5LjQsMEgzLjUKCQkJCUMzLjQsMCwzLjQsMCwzLjIsMEMzLjMsMCwzLjIsMCwzLjIsMHogTTMyLjEsMEMzMC40LDAsMjksMS40LDI5LDMuMnMxLjQsMy4yLDMuMSwzLjJoNi42YzEuNywwLDMuMS0xLjQsMy4xLTMuMlM0MC40LDAsMzguNywwCgkJCQlIMzIuMXogTTUxLjQsMGMtMS43LDAtMy4xLDEuNC0zLjEsMy4yczEuNCwzLjIsMy4xLDMuMmg2LjZjMS43LDAsMy4xLTEuNCwzLjEtMy4yUzU5LjgsMCw1OC4xLDBINTEuNHogTTcwLjcsMAoJCQkJYy0xLjcsMC0zLjEsMS40LTMuMSwzLjJzMS40LDMuMiwzLjEsMy4yaDYuNmMxLjcsMCwzLjEtMS40LDMuMS0zLjJTNzkuMSwwLDc3LjQsMEg3MC43eiBNOTAsMGMtMS43LDAtMy4xLDEuNC0zLjEsMy4yCgkJCQlzMS40LDMuMiwzLjEsMy4yaDEzdjkuOGMwLDEuNywxLjQsMy4xLDMuMiwzLjFjMS44LDAsMy4yLTEuNCwzLjItMy4xVjMuMWMwLTAuMi0wLjEtMC40LTAuMS0wLjZjMC0wLjIsMC0wLjMtMC4xLTAuNQoJCQkJYy0wLjEtMC4yLTAuMi0wLjQtMC40LTAuN2MtMC4xLTAuMS0wLjItMC4zLTAuMy0wLjRjLTAuMi0wLjItMC4zLTAuMy0wLjUtMC40Yy0wLjItMC4xLTAuMy0wLjItMC41LTAuMwoJCQkJYy0wLjItMC4xLTAuNS0wLjEtMC44LTAuMmMtMC4xLDAtMC4yLTAuMS0wLjMtMC4xYy0wLjEsMC0wLjEsMC0wLjIsMGMwLDAtMC4xLDAtMC4xLDBjMCwwLDAsMC0wLjEsMGMtMC4xLDAtMC4xLDAtMC4yLDBIOTB6CgkJCQkgTTMuMiwyNS44Yy0xLjgsMC0zLjIsMS40LTMuMiwzLjF2Ni42YzAsMS43LDEuNCwzLjEsMy4yLDMuMXMzLjItMS40LDMuMi0zLjF2LTYuNkM2LjQsMjcuMiw1LDI1LjgsMy4yLDI1Ljh6IE0xMDYuMiwyNS44CgkJCQljLTEuOCwwLTMuMiwxLjQtMy4yLDMuMXY2LjZjMCwxLjcsMS40LDMuMSwzLjIsMy4xYzEuOCwwLDMuMi0xLjQsMy4yLTMuMXYtNi42QzEwOS41LDI3LjIsMTA4LDI1LjgsMTA2LjIsMjUuOHogTTMuMiw0NS4xCgkJCQljLTEuOCwwLTMuMiwxLjQtMy4yLDMuMXY2LjZDMCw1Ni41LDEuNCw1OCwzLjIsNThzMy4yLTEuNCwzLjItMy4xdi02LjZDNi40LDQ2LjUsNSw0NS4xLDMuMiw0NS4xeiBNNDEuOSw0NS4xCgkJCQlDMzQuOCw0NS4xLDI5LDUwLjksMjksNTh2NjEuMmMwLDcuMSw1LjgsMTIuOSwxMi45LDEyLjloNzcuM2M3LjEsMCwxMi45LTUuOCwxMi45LTEyLjlWNThjMC03LjEtNS44LTEyLjktMTIuOS0xMi45SDQxLjl6CgkJCQkgTTQxLjksNTEuNWg3Ny4zYzMuNiwwLDYuNCwyLjgsNi40LDYuNHY2MS4yYzAsMy42LTIuOCw2LjQtNi40LDYuNEg0MS45Yy0zLjYsMC02LjQtMi44LTYuNC02LjRWNTgKCQkJCUMzNS40LDU0LjQsMzguMyw1MS41LDQxLjksNTEuNXogTTMuMiw2NC40Yy0xLjgsMC0zLjIsMS40LTMuMiwzLjF2Ni42YzAsMS43LDEuNCwzLjEsMy4yLDMuMXMzLjItMS40LDMuMi0zLjF2LTYuNgoJCQkJQzYuNCw2NS44LDUsNjQuNCwzLjIsNjQuNHogTTc0LDcwLjhWMTAzbDcuOC02LjZsNC45LDExLjVsNC4xLTEuOGwtNS4yLTExLjNsMTAuOS0xLjRMNzQsNzAuOHogTTMuMiw4My43CgkJCQljLTEuOCwwLTMuMiwxLjQtMy4yLDMuMXYxMi43YzAsMC4xLDAsMC4xLDAsMC4yYzAsMCwwLDAsMCwwLjFjMCwwLDAsMC4xLDAsMC4xYzAsMC4xLDAsMC4xLDAsMC4yYzAsMC4xLDAuMSwwLjIsMC4xLDAuMwoJCQkJYzAuMSwwLjMsMC4xLDAuNSwwLjIsMC44YzAuMSwwLjIsMC4yLDAuMywwLjMsMC41YzAuMSwwLjIsMC4yLDAuNCwwLjQsMC41YzAuMSwwLjEsMC4zLDAuMiwwLjQsMC4zYzAuMiwwLjEsMC40LDAuMywwLjcsMC40CgkJCQljMC4xLDAuMSwwLjMsMC4xLDAuNSwwLjFjMC4yLDAsMC40LDAuMSwwLjYsMC4xaDE2LjNjMS43LDAsMy4xLTEuNCwzLjEtMy4ycy0xLjQtMy4yLTMuMS0zLjJoLTEzdi05LjhDNi40LDg1LjEsNSw4My43LDMuMiw4My43CgkJCQl6Ii8+CgkJPC9nPgoJCTxyZWN0IGlkPSJSZWN0YW5nbGUtQ29weS00IiB4PSI0IiB5PSIxNzciIGNsYXNzPSJzdDIiIHdpZHRoPSIxNDMyIiBoZWlnaHQ9IjgyOSIvPgoJPC9nPgo8L2c+Cjwvc3ZnPgo=" alt="Drop files here or click to upload">
                    <span>Drop files here or click to upload</span>
                </button>
            </div>
            <input type="file" class="ccm-file-uploader-container-dropzone-file-element d-none" multiple="multiple">
        </div>
    </div>
</template>

<script>
/* global CCM_DISPATCHER_FILENAME, ccmi18n_fileuploader, CCM_SECURITY_TOKEN, ConcreteAlert, ConcreteEvent, NProgress, _ */
export default {
    data: () => ({
        dropzone: null,
        uploadedFiles: [],
        isUploadInProgress: false,
        itemTemplate: `
            <div class="ccm-file-upload-wrapper">
                <div class="ccm-file-upload-item-wrapper">
                    <div class="ccm-file-upload-item">
                        <div class="ccm-file-upload-item-inner">
                            <div class="ccm-file-upload-image-wrapper">
                                <img data-dz-thumbnail="">
                            </div>
                            <div class="ccm-file-upload-progress-text">
                                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                    <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="ccm-file-upload-progress-text-value"></text>
                                </svg>
                            </div>
                            <svg viewbox="0 0 120 120" width="120" height="120" class="ccm-file-upload-progress">
                                <circle stroke="#4A90E2" stroke-width="5" fill="transparent" r="52" cx="60" cy="60"></circle>
                            </svg>
                        </div>
                    </div>
                    <div data-dz-size="" class="ccm-file-upload-label"></div>
                </div>
            </div>
        `
    }),
    props: {
        uploadDirectoryId: {
            type: Number,
            required: false,
            default: null
        },
        maxFiles: {
            type: Number,
            required: false,
            default: null
        },
        options: {
            type: Object,
            default: () => ({})
        },
        replaceFileId: {
            type: Number,
            required: false,
            default: null
        }
    },
    computed: {
        filesInQueue() {
            if (this.dropzone) {
                return this.dropzone.files.length
            }
            return 0
        },
        dropzoneSettings() {
            const me = this
            return {
                url: `${CCM_DISPATCHER_FILENAME}/ccm/system/file/upload`,
                previewTemplate: this.itemTemplate,
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 4,
                paramName: 'files',
                maxFiles: this.maxFiles,
                autoQueue: true,

                addedfiles: function () {
                    me.refresh()
                },

                removedfile: function () {
                    me.refresh()
                },

                error: function (files, message) {
                    me.isUploadInProgress = false

                    ConcreteAlert.error({
                        title: 'Error',
                        message: message
                    })
                },

                success: function (data, response) {
                    response.files.forEach(function (file) {
                        if (typeof file.fID !== 'undefined') {
                            const fileAlreadyAdded = _.findWhere(me.uploadedFiles, { fID: file.fID }) !== undefined

                            if (!fileAlreadyAdded) {
                                me.uploadedFiles.push(file)
                            }
                        }
                    })
                },

                sending: function (file, xhr, formData) {
                    formData.append('responseFormat', 'dropzone')
                    formData.append('ccm_token', CCM_SECURITY_TOKEN)
                    formData.append('currentFolder', me.uploadDirectoryId)
                    if (me.replaceFileId) {
                        formData.append('fID', me.replaceFileId)
                    }
                },

                totaluploadprogress: function (progress) {
                    NProgress.set(progress / 100)
                },

                queuecomplete: function () {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        if (me.uploadedFiles.length !== 0) {
                            const fileIds = []
                            me.uploadedFiles.forEach(function(file) {
                                fileIds.push(file.fID)
                            })
                            ConcreteEvent.publish('FileManagerSelectFile', { fID: fileIds })
                            me.uploadedFiles = []
                        }
                        me.uploadComplete()
                    }
                    me.refresh()
                },

                init: function () {
                    me.dropzone = this
                    me.refresh()
                },

                uploadprogress: function (file, progress) {
                    me.isUploadInProgress = true

                    const $fileElement = $(file.previewElement)
                    const circle = $fileElement.find('circle').get(0)
                    const radius = circle.r.baseVal.value
                    const circumference = radius * 2 * Math.PI

                    circle.style.strokeDasharray = `${circumference} ${circumference}`
                    circle.style.strokeDashoffset = `${circumference}`
                    circle.style.strokeDashoffset = circumference - progress / 100 * circumference

                    $fileElement.find('.ccm-file-upload-progress-text-value').html(parseInt(progress) + '%')
                    $fileElement.addClass('in-progress')
                }
            }
        }
    },
    mounted() {
        const me = this
        const settings = me.dropzoneSettings

        $(me.$refs.dropzoneElement).dropzone(settings)
        $(window).on('resize', _.throttle(me.refresh, 100))

        ConcreteEvent.subscribe('FileUploaderUploadSelectedFiles', function(e) {
            me.dropzone.options.autoProcessQueue = true
            me.dropzone.processQueue()
        })
    },
    watch: {
        filesInQueue() {
            const filesInQueue = this.filesInQueue
            ConcreteEvent.publish('FileUploaderFilesReadyToUpload', filesInQueue)
        },
        isUploadInProgress(val, oldVal) {
            if (oldVal === false) {
                NProgress.start()
            } else {
                NProgress.done()
            }

            this.refresh()

            this.$emit('uploadProgressStateChange', val)
        }
    },
    methods: {
        refresh() {
            const $container = $(this.$refs.dropzoneElement)
            const containerWidth = $container.prop('scrollWidth')
            let thumbnailWidth = containerWidth

            if (containerWidth >= 450 && containerWidth < 700) {
                thumbnailWidth = thumbnailWidth / 2
            } else if (containerWidth >= 700 && containerWidth < 900) {
                thumbnailWidth = thumbnailWidth / 4
            } else if (containerWidth >= 900) {
                thumbnailWidth = thumbnailWidth / 6
            }

            thumbnailWidth -= 42 // padding + border (see css declaration of .ccm-file-upload-item-wrapper)

            $container.find('.ccm-file-upload-item-wrapper').css('width', `${thumbnailWidth}px`)
        },
        reset() {
            this.isUploadInProgress = false

            const $container = $(this.$refs.dropzoneElement)

            $container
                .removeClass('dz-started')
                .find('.ccm-file-upload-wrapper')
                .remove()

            this.dropzone.removeAllFiles(true)
        },
        uploadComplete() {
            this.$emit('update:uploadCompleted')

            ConcreteAlert.notify({
                title: 'Complete',
                message: ccmi18n_fileuploader.uploadSuccessfulMessage
            })
        }
    }
}
</script>
