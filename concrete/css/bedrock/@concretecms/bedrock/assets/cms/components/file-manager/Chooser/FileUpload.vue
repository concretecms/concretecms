<template>
    <div>
        <chooser-header :show-form-factor-selector="false" :title="title"/>

        <uploader/>
    </div>
</template>

<script>
/* eslint-disable no-new */
import ChooserHeader from './Header'
import Uploader from '../Uploader'

export default {
    components: {
        ChooserHeader,
        Uploader
    },
    data: () => ({
        selectedFiles: []
    }),
    props: {
        title: {
            type: String,
            required: true
        },
        multipleSelection: {
            type: Boolean,
            default: true
        }
    },
    watch: {
        selectedFiles(value) {
            this.$emit('update:selectedFiles', value)
        }
    }
}
</script>
