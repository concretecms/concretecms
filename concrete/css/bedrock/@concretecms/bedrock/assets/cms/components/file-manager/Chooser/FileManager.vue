<template>
  <div>
    <chooser-header :resultsFormFactor.sync="formFactor"
                    :breadcrumb-items="breadcrumbItems"
                    @breadcrumbItemClick="activeFolder = $event.folderId"
                    :title="title"/>

    <files :selectedFiles.sync="selectedFiles"
           :resultsFormFactor="formFactor"
           :routePath="routePath + activeFolder"
           :enable-pagination="true"
           :enable-sort="true"
           :multipleSelection="multipleSelection"
           @folderClick="activeFolder = $event"/>
  </div>
</template>

<script>
/* global CCM_DISPATCHER_FILENAME, ConcreteAjaxRequest */
/* eslint indent: [2, 2, {"SwitchCase": 1}] */
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
    selectedFiles: [],
    breadcrumbItems: [],
    routePath: '/ccm/system/file/chooser/get_folder_files/',
    formFactor: 'grid'
  }),
  props: {
    resultsFormFactor: {
      type: String,
      required: false,
      default: 'grid', // grid | list
      validator: value => ['grid', 'list'].indexOf(value) !== -1
    },
    startFolder: {
      type: String,
      required: true
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
  created() {
    this.activeFolder = this.$props.startFolder
    this.fetchBreadcrumb(this.activeFolder)
  },
  methods: {
    fetchBreadcrumb(folderId = '') {
      new ConcreteAjaxRequest({
        url: `${CCM_DISPATCHER_FILENAME}/ccm/system/file/chooser/get_breadcrumb/${folderId}`,
        success: r => {
          this.breadcrumbItems = r.data
        }
      })
    }
  },
  watch: {
    activeFolder(value) {
      this.fetchBreadcrumb(value)
    },
    selectedFiles(value) {
      this.$emit('update:selectedFiles', value)
    },
    formFactor(value) {
      this.$emit('update:resultsFormFactor', value)
    },
    startFolder(value) {
      this.activeFolder = value
      this.fetchBreadcrumb(value)
    }
  },
  mounted() {
    this.formFactor = this.resultsFormFactor
    this.activeFolder = this.$props.startFolder
    this.fetchBreadcrumb(this.activeFolder)
  }
}
</script>
