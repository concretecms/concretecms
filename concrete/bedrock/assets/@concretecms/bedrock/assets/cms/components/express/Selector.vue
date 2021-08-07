<template>
  <div>
    <div class="row mb-3">
      <div class="col-md-4 ms-auto">
        <form @submit.prevent="getExpressEntries">
          <div class="ccm-header-search-form-input input-group">
            <!--suppress HtmlFormInputWithoutLabel -->
            <input type="text" class="form-control border-end-0" placeholder="Search" autocomplete="false"
                   v-model="keywords">

            <button type="submit" class="input-group-icon">
              <svg width="16" height="16">
                <use xlink:href="#icon-search"/>
              </svg>
            </button>
          </div>
        </form>
      </div>
    </div>

    <div>
      <div>
        <svg v-if="isLoading" class="ccm-loader-dots">
          <use xlink:href="#icon-loader-circles"/>
        </svg>
        <div v-if="!isLoading">
          <table class="table ccm-image-chooser-list-view ccm-search-results-table">
            <thead>
            <tr>
              <th></th>
              <th :class="getSortColumnClassName('e.exEntryDateCreated')">
                <a href="#" @click.prevent="sortBy('exEntryDateCreated')">
                  Date Added
                </a>
              </th>
              <th :class="getSortColumnClassName('e.exEntryDateModified')">
                <a href="#" @click.prevent="sortBy('e.exEntryDateModified')">
                  Date Modified
                </a>
              </th>
              <th>
                <span>Name</span>
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="expressEntry in expressEntryList" @click="onItemClick(expressEntry)">
              <td><!--suppress HtmlFormInputWithoutLabel -->
                <input v-model="selectedExpressEntries" type="radio" :id="'expressEntry-' + expressEntry.exEntryID"
                       :value="expressEntry.exEntryID">
              </td>
              <td>{{ expressEntry.exEntryDateCreated }}</td>
              <td>{{ expressEntry.exEntryDateModified }}</td>
              <td>{{ expressEntry.label }}</td>
            </tr>
            </tbody>
          </table>

          <Pagination
              v-if="haveToPaginate"
              v-model="currentPage"
              :total-rows="pagination.total"
              :per-page="pagination.per_page"/>
        </div>
      </div>
    </div>

    <div class="dialog-buttons">
      <button class="btn btn-secondary" data-dialog-action="cancel">
        Cancel
      </button>

      <button type="button" @click="selectEntry" :disabled="selectedEntry === null" class="btn btn-primary">
        Select
      </button>
    </div>
  </div>
</template>

<script>
/* global CCM_DISPATCHER_FILENAME, ConcreteAjaxRequest */
/* eslint-disable indent */
import Pagination from '../Pagination'
import Icon from '../Icon'

export default {
  components: {
    Icon,
    Pagination
  },
  props: {
    entityId: {
      type: String,
      required: true
    }
  },
  data: () => ({
    keywords: '',
    selectedEntry: null,
    currentPage: 1,
    rows: false,
    expressEntryList: [],
    selectedExpressEntries: [],
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
  computed: {
    isLoading() {
      return this.rows === false
    },
    fetchRoute() {
      let routePath = CCM_DISPATCHER_FILENAME + '/ccm/system/express/entry/get_json?exEntityID=' + this.entityId

      if (this.sortByColumn !== '') {
        routePath += `&${this.queryParams.sort_column}=${this.sortByColumn}&${this.queryParams.sort_direction}=${this.sortByDirection}`
      }

      if (this.pagination) {
        routePath += `&${this.queryParams.pagination_page}=${this.currentPage}&itemsPerPage=${this.pagination.per_page}`
      }

      if (this.keywords) {
        routePath += '&keyword=' + encodeURI(this.keywords)
      }

      return routePath
    },
    haveToPaginate() {
      return this.pagination && this.pagination.total_pages > 1
    }
  },
  methods: {
    selectEntry() {
      ConcreteEvent.publish('SelectExpressEntry', {
        exEntryID: this.selectedEntry.exEntryID
      })
    },
    onItemClick(value) {
      this.selectedEntry = value
    },
    getExpressEntries() {
      const my = this
      my.rows = false
      my.expressEntryList = []
      my.selectedExpressEntries = [] // Reset Selected ExpressEntries
      my.viewIsLoading = true

      return new ConcreteAjaxRequest({
        url: this.fetchRoute,
        success: r => {
          my.rows = []
          r.data = r.data || {}
          if (r.data.length) {
            my.expressEntryList = r.data
            let currentRow = []
            r.data.forEach(function (image, i) {
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
      this.sortByDirection = this.sortByDirection === 'asc' ? 'desc' : 'asc'
      this.sortByColumn = column
    },
    getSortColumnClassName() {
      return `ccm-results-list-active-sort-${this.sortByDirection}`
    }
  },
  watch: {
    fetchRoute: {
      immediate: true,
      handler() {
        if (!this.viewIsLoading) {
          this.getExpressEntries()
        }
      }
    }
  },
  mounted() {
    this.getExpressEntries()
  }
}
</script>

<style lang="scss" scoped>
/* stylelint-disable selector-pseudo-element-no-unknown */
@import '../../../../assets/cms/scss/bootstrap-overrides';

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

.search-icon {
  display: inline-block;

  .icon {
    font-size: 7rem;
  }
}

button {
  .label {
    margin: 0 10px;
  }
}
</style>
