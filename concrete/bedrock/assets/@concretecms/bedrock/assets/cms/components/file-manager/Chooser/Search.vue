<template>
    <div>
        <chooser-header :resultsFormFactor.sync="formFactor" :title="title"/>

        <div class="row mb-3">
            <div class="col-md-4 ms-auto">
                <form @submit.prevent="search">
                    <div class="ccm-header-search-form-input input-group">
                        <input type="text" class="form-control border-end-0" placeholder="Search" autocomplete="false" v-model="searchText">
                        <button type="submit" class="input-group-icon">
                            <svg width="16" height="16">
                                <use xlink:href="#icon-search"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div v-show="!keywords" class="text-center mt-5">
            <span class="search-icon my-4">
                <Icon icon="search" type="fas" color="#f4f4f4"/>
            </span>
            <p><b>Let's get some info on what you're looking for.</b></p>
        </div>
        <div>
            <files v-if="keywords"
                :selectedFiles.sync="selectedFiles"
                :resultsFormFactor="formFactor"
                :routePath="routePath + keywords"
                :enable-pagination="true"
                :enable-sort="true"
                :multipleSelection="multipleSelection"/>
        </div>
    </div>
</template>

<script>
/* eslint-disable no-new */
import Icon from '../../Icon'
import ChooserHeader from './Header'
import Files from '../Chooser/Files'

export default {
    components: {
        Icon,
        ChooserHeader,
        Files
    },
    data: () => ({
        searchText: '',
        keywords: '',
        selectedFiles: [],
        routePath: '/ccm/system/file/chooser/search/',
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
        search () {
            this.keywords = this.searchText
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

<style lang="scss" scoped>
.search-icon {
  display: inline-block;

  .icon {
    font-size: 7rem;
  }
}
</style>
