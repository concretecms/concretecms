<template>
    <div>
        <chooser-header :resultsFormFactor.sync="formFactor" :title="title"/>

        <div class="form-inline">
            <div class="form-group ml-auto">
                <label for="fileSetSelector" class="mr-2">File Set</label>
                <select id="fileSetSelector" class="form-control file-set-menu" v-model="activeSet">
                    <option value="" selected>Select a Set</option>
                    <option v-for="set in sets" :key="set.id" :value="set.id">
                        {{set.name}}
                    </option>
                </select>
            </div>
        </div>
        <div class="mt-3" v-show="activeSet">
            <files v-if="activeSet"
                :selectedFiles.sync="selectedFiles"
                :resultsFormFactor="formFactor"
                :routePath="routePath + activeSet"
                :enable-pagination="true"
                :enable-sort="true"
                :multipleSelection="multipleSelection"/>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.file-set-menu {
  width: 300px !important;
}
</style>

<script>
/* global CCM_DISPATCHER_FILENAME, ConcreteAjaxRequest */
/* eslint-disable no-new */
import ChooserHeader from './Header'
import Files from './Files'

export default {
    components: {
        ChooserHeader,
        Files
    },
    data: () => ({
        sets: [],
        activeSet: '',
        selectedFiles: [],
        routePath: '/ccm/system/file/chooser/get_file_set/',
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
    methods: {
        getSets() {
            new ConcreteAjaxRequest({
                url: `${CCM_DISPATCHER_FILENAME}/ccm/system/file/chooser/get_file_sets`,
                success: r => {
                    this.sets = r
                }
            })
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

        this.getSets()
    }
}
</script>
