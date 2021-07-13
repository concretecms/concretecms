<template>
    <div>
        <svg v-if="isLoading" class="ccm-loader-dots"><use xlink:href="#icon-loader-circles" /></svg>
        <div v-if="!isLoading">
            <div class="ccm-image-cell-grid container-fluid pl-0" v-if="resultsFormFactor === 'grid'">
                <div v-for="row in rows" class="row text-center" :key="row.index">
                    <div class="col-md-3" v-for="file in row" :key="(file.fID || file.treeNodeID) + 'grid'">
                        <div class="ccm-image-cell" @click="onItemClick(file)">
                            <label :for="'file-' + (file.fID || file.treeNodeID)"><span v-html="file.resultsThumbnailImg"></span></label>
                            <div class="ccm-image-cell-title">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" v-if="multipleSelection && !file.isFolder" v-model="selectedFiles" :id="'file-' + file.fID" :value="file.fID">
                                    <input class="form-check-input" type="radio" v-if="!multipleSelection && !file.isFolder" v-model="selectedFiles" :id="'file-' + file.fID" :value="file.fID">
                                    <label class="form-check-label" :for="'file-' + (file.fID || file.treeNodeID)">{{file.title}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="resultsFormFactor === 'list'">
                <table class="table ccm-image-chooser-list-view ccm-search-results-table">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th :class="getSortColumnClassName('fv.fvTitle')">
                            <a v-if="enableSort" href="#" @click.prevent="sortBy('fv.fvTitle')">Name</a>
                            <span v-else>Name</span>
                        </th>
                        <th :class="getSortColumnClassName(dateSortColumn)">
                            <a v-if="enableSort" href="#" @click.prevent="sortBy(dateSortColumn)">Uploaded</a>
                            <span v-else>Uploaded</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-for="file in fileList" :key="(file.fID || file.treeNodeID) + 'list'" @click="onItemClick(file)">
                            <td>
                                <input type="checkbox" v-if="multipleSelection && !file.isFolder" v-model="selectedFiles" :id="'file-' + file.fID" :value="file.fID">
                                <input type="radio" v-if="!multipleSelection && !file.isFolder" v-model="selectedFiles" :id="'file-' + file.fID" :value="file.fID">
                            </td>
                            <td class="ccm-image-chooser-icon"><span v-html="file.resultsThumbnailImg" width="32" height="32"></span></td>
                            <td>{{file.fID}}</td>
                            <td>{{file.title}}</td>
                            <td>{{file.fvDateAdded}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination
                v-if="haveToPaginate"
                v-model="currentPage"
                :total-rows="pagination.total"
                :per-page="pagination.per_page"/>
        </div>
    </div>
</template>
<style lang='scss' scoped>
/* stylelint-disable selector-pseudo-element-no-unknown */
@import '../../../../../assets/cms/scss/bootstrap-overrides';

.ccm-image-cell > label::v-deep i {
  color: $gray-400;
  font-size: 100px;
  margin: 2px;
  padding: 0.5rem;
}

.ccm-image-chooser-icon::v-deep i {
  color: $gray-400;
  font-size: 32px;
}
</style>
<script>
/* global CCM_DISPATCHER_FILENAME, ConcreteAjaxRequest */
/* eslint-disable no-new */
import Pagination from '../../Pagination'

export default {
    components: {
        Pagination
    },
    data: () => ({
        currentPage: 1,
        rows: false,
        fileList: [],
        selectedFiles: [],
        sortByColumn: '',
        sortByDirection: 'desc',
        pagination: null,
        queryParams: {
            pagination_page: 'ccm_paging_p',
            sort_column: 'ccm_order_by',
            sort_direction: 'ccm_order_by_direction'
        },
        viewIsLoading: false
    }),
    props: {
        enableSort: {
            type: Boolean,
            required: false,
            default: false
        },
        enablePagination: {
            type: Boolean,
            required: false,
            default: false
        },
        resultsFormFactor: {
            type: String,
            required: false,
            default: 'grid' // grid | list
        },
        additionalQueryParams: {
            type: Array,
            required: false
        },
        routePath: {
            type: String,
            required: true
        },
        multipleSelection: {
            type: Boolean,
            default: true
        }
    },
    computed: {
        isLoading() {
            return this.rows === false
        },
        isFolderItemList() {
            if (this.fileList.length > 0) {
                const firstRow = _.first(this.fileList)

                return !_.isUndefined(firstRow.treeNodeID)
            }

            return false
        },
        dateSortColumn() {
            return this.isFolderItemList ? 'dateModified' : 'f.fDateAdded'
        },
        fetchRoute() {
            let routePath = CCM_DISPATCHER_FILENAME + this.$props.routePath
            let qs = '?'
            if (this.enableSort && this.sortByColumn !== '') {
                routePath += `${qs}${this.queryParams.sort_column}=${this.sortByColumn}&${this.queryParams.sort_direction}=${this.sortByDirection}`
                qs = '&'
            }

            if (this.enablePagination && this.pagination) {
                routePath += `${qs}${this.queryParams.pagination_page}=${this.currentPage}&itemsPerPage=${this.pagination.per_page}`
                qs = '&'
            }

            if (typeof this.$props.additionalQueryParams === 'object') {
                for (var item of this.$props.additionalQueryParams) {
                    routePath += qs + encodeURIComponent(item.key) + '=' + encodeURIComponent(item.value)
                    qs = '&'
                }
            }

            return routePath
        },
        haveToPaginate() {
            return this.enablePagination && this.pagination && this.pagination.total_pages > 1
        }
    },
    methods: {
        getFiles() {
            const my = this
            my.rows = false
            my.fileList = []
            my.selectedFiles = [] // Reset Selected Files
            my.viewIsLoading = true

            new ConcreteAjaxRequest({
                url: this.fetchRoute,
                success: r => {
                    my.rows = []
                    r.data = r.data || {}
                    if (r.data.length) {
                        my.fileList = r.data
                        let currentRow = []
                        r.data.forEach(function(image, i) {
                            currentRow.push(image)
                            if ((i + 1) % 4 === 0) {
                                my.rows.push(currentRow)
                                currentRow = []
                            }
                        })

                        if (currentRow.length) {
                            my.rows.push(currentRow)
                        }
                    }

                    if (r.meta) {
                        if (r.meta.pagination) {
                            my.pagination = r.meta.pagination
                        }

                        if (r.meta.query_params) {
                            my.queryParams = r.meta.query_params
                        }
                    }

                    // Prevent re-fetching data
                    // as changing pagination & queryParams data will fire `fetchRoute` watcher
                    my.$nextTick(() => {
                        my.viewIsLoading = false
                    })
                }
            })
        },
        sortBy(column) {
            if (column === this.sortByColumn || (this.sortByColumn === '' && column === this.dateSortColumn)) {
                this.sortByDirection = this.sortByDirection === 'asc' ? 'desc' : 'asc'
            }

            this.sortByColumn = column
        },
        getSortColumnClassName(column) {
            let className = ''
            if (this.enableSort) {
                if (column === this.sortByColumn || (this.sortByColumn === '' && column === this.dateSortColumn)) {
                    className = `ccm-results-list-active-sort-${this.sortByDirection}`
                }
            }

            return className
        },
        onItemClick(file) {
            if (file.isFolder) {
                this.$emit('folderClick', file.treeNodeID)
            }
        }
    },
    watch: {
        selectedFiles(value) {
            this.$emit('update:selectedFiles', Array.isArray(value) ? value : [value])
        },
        routePath() {
            // Reset Pagination if base route has changed
            this.currentPage = 1
        },
        fetchRoute: {
            immediate: true,
            handler() {
                if (!this.viewIsLoading) {
                    this.getFiles()
                }
            }
        }
    }
}
</script>

<style lang="scss" scoped>
  button {
    .label {
      margin: 0 10px;
    }
  }
</style>
