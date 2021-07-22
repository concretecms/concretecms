<template>
    <div>
        <chooser-header :resultsFormFactor.sync="formFactor"
                        :breadcrumb-items="breadcrumbItems"
                        @breadcrumbItemClick="activeFolder = $event.folderId"
                        :title="title"/>

        <div class="form-group row row-cols-auto g-0 align-items-center justify-content-end">
            <div class="col-auto">
                <label for="searchPresets" class="me-2">Search Presets</label>
            </div>
            <div class="col-auto">
                <select id="searchPresets" class="form-select search-presets-menu" v-model="activeSearchPreset">
                    <option value="" selected>Select a Preset</option>
                    <option v-for="searchPreset in searchPresets" :key="searchPreset.id" :value="searchPreset.id">
                        {{searchPreset.presetName}}
                    </option>
                </select>
            </div>
        </div>
        <div class="mt-3" v-show="activeSearchPreset">
            <files v-if="activeSearchPreset"
                :selectedFiles.sync="selectedFiles"
                :resultsFormFactor="formFactor"
                :routePath="routePath + activeSearchPreset"
                :enable-pagination="true"
                :multipleSelection="multipleSelection"
                @folderClick="activeFolder = $event"/>
        </div>
    </div>
</template>

<style lang="scss" scoped>
.search-presets-menu {
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
        activeFolder: '',
        searchPresets: [],
        activeSearchPreset: '',
        selectedFiles: [],
        breadcrumbItems: [],
        routePath: '/ccm/system/file/chooser/get_search_preset/',
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
        fetchSearchPresets() {
            new ConcreteAjaxRequest({
                url: `${CCM_DISPATCHER_FILENAME}/ccm/system/file/chooser/get_search_presets`,
                success: r => {
                    this.searchPresets = r
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

        this.fetchSearchPresets()
    }
}
</script>
