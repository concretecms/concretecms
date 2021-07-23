<template>
    <div>
        <chooser-header :resultsFormFactor.sync="formFactor" :title="title"/>

        <files :selectedFiles.sync="selectedFiles"
               :resultsFormFactor="formFactor"
               routePath="/ccm/system/file/chooser/recent"
               :multipleSelection="multipleSelection"/>
    </div>
</template>

<script>
/* eslint-disable no-new */
import ChooserHeader from './Header'
import Files from './Files'

export default {
    components: {
        ChooserHeader,
        Files
    },
    data: () => ({
        selectedFiles: [],
        formFactor: 'grid'
    }),
    props: {
        resultsFormFactor: {
            type: String,
            required: false,
            default: 'grid', // grid | list
            validator: value => ['grid', 'list'].indexOf(value) !== -1
        },
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
        },
        formFactor(value) {
            this.$emit('update:resultsFormFactor', value)
        }
    },
    mounted() {
        this.formFactor = this.resultsFormFactor
    }
}
</script>
