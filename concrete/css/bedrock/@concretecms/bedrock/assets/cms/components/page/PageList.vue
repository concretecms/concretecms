<template>
    <div>
        <svg v-if="isLoading" class="ccm-loader-dots">
            <use xlink:href="#icon-loader-circles"/>
        </svg>
        <div v-else>
            <table class="table ccm-page-list-view ccm-search-results-table">
                <thead>
                <tr>
                    <th><span>Type</span></th>
                    <th :class="orderBy === 'cv.cvName' ? 'ccm-results-list-active-sort-' + orderByDirection : ''"><a href="#" @click="sortResults('cv.cvName', 'asc', $event)">Name</a></th>
                    <th :class="orderBy === 'cv.cvDatePublic' ? 'ccm-results-list-active-sort-' + orderByDirection : ''"><a href="#" @click="sortResults('cv.cvDatePublic', 'asc', $event)">Date</a></th>
                    <th :class="orderBy === 'c.cDateModified' ? 'ccm-results-list-active-sort-' + orderByDirection : ''"><a href="#" @click="sortResults('c.cDateModified', 'desc', $event)">Last Modified</a></th>
                    <th><span>Author</span></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="page in pageList" :key="page.cID + 'list'"
                    @click="choosePage(page)"
                    @mouseover="mouseOver = page.cID"
                    @mouseout="mouseOver = 0"
                    :class="{'ccm-search-select-hover': mouseOver === page.cID}">
                    <td>{{page.type}}</td>
                    <td>{{page.name}}</td>
                    <td>{{page.datePublic}}</td>
                    <td>{{page.dateModified}}</td>
                    <td>{{page.author}}</td>
                </tr>
                </tbody>
            </table>
            <Pagination
                :key="ccm.pagination_mode"
                v-if="ccm.pagination_show"
                v-model="currentPage"
                :total-rows="pagination.total"
                :per-page="pagination.per_page"
                :mode="ccm.pagination_mode"
                :prev-cursor="ccm.ccm_cursor_prev"
                :next-cursor="ccm.ccm_cursor_next"></Pagination>
        </div>
    </div>
</template>

<script>
import Pagination from '../Pagination'

/**
 * @vue-data {Number} currentPage - Number of the page that is displayed and highlighted in pagination.
 * @vue-data {String} latestSearchID - ID that helps to only display the most recently requested search result.
 * @vue-data {Number} mouseOver - Contains cID of the page row that the mouse hovers over for adding a hover class name.
 * @vue-data {String} orderBy - Sort column name.
 * @vue-data {String} orderByDirection - Sort direction desc/asc.
 * @vue-data {false|Array<Object>} pageList - Pages to display. When not yet initialised this is set to false.
 * @vue-data {Object} pagination - Data on pagination that is passed to pagination component.
 * @vue-prop {String} [keywords] - Optional search parameter
 * @vue-prop {String} routePath - Origin for pageList data which is fetched by ConcreteAjaxRequest.
 */
export default {
    components: { Pagination },
    data: () => ({
        currentPage: -1,
        latestSearchID: null,
        mouseOver: 0,
        orderBy: 'c.cDateModified',
        orderByDirection: 'desc',
        pageList: false,
        ccm: {
            pagination_mode: 'paging',
            pagination_show: false,
            ccm_cursor_prev: null,
            ccm_cursor_next: null
        },
        pagination: {
            total: 0,
            count: 0,
            per_page: 20,
            current_page: 1,
            total_pages: 0
        }
    }),
    props: {
        keywords: {
            type: String,
            required: true
        },
        routePath: {
            type: String,
            required: true
        }
    },
    computed: {
        isLoading () {
            return this.pageList === false
        }
    },
    methods: {
        /**
         * Sort search results by sortField
         * @param {String} sortField - Order by this field
         * @param {String} initialSortOrder - If the field wasn't sorted on yet, then use the initial order
         * @param {Event} event - The click event
         */
        sortResults (sortField, initialSortOrder, event) {
            if (this.orderBy === sortField) {
                this.orderByDirection = this.orderByDirection === 'desc' ? 'asc' : 'desc'
            } else {
                this.orderByDirection = initialSortOrder
            }

            this.orderBy = sortField

            this.fetchPages()

            // Cancel href click
            event.stopPropagation()
        },

        /**
         * Retrieves pages from the routePath url. On success this updates
         * this.pageList. That triggers Vue to rerender search result
         * @returns ConcreteAjaxRequest - The request that performs the fetch and updates this.pageList
         */
        fetchPages () {
            const currentSearch = Math.random().toString(36).slice(2)
            this.latestSearchID = currentSearch

            // Paging string is different when only prev/next is possible
            let pagingOrCursor = '&ccm_paging_p=' + this.currentPage
            if (this.ccm.pagination_mode === 'cursor') {
                pagingOrCursor = '&ccm_cursor=' + this.currentPage
            }

            return new ConcreteAjaxRequest({
                url: CCM_DISPATCHER_FILENAME + this.$props.routePath + this.keywords +
                        '?ccm_order_by=' + this.orderBy +
                        '&ccm_order_by_direction=' + this.orderByDirection +
                    pagingOrCursor +
                        '&itemsPerPage=' + this.pagination.per_page,
                success: response => {
                    // Do not update if the id is not the most recent search (the response might be a delayed result)
                    if (this.latestSearchID !== currentSearch) {
                        return
                    }

                    // Update ccm data
                    if (response.meta && response.meta.ccm) {
                        this.ccm.pagination_mode = response.meta.ccm.pagination_mode || 'paging'
                        this.ccm.pagination_show = !!response.meta.ccm.pagination_show
                        this.ccm.ccm_cursor_prev = response.meta.ccm.ccm_cursor_prev
                        this.ccm.ccm_cursor_next = response.meta.ccm.ccm_cursor_next
                    }

                    // Update pagination
                    if (response.meta && response.meta.pagination) {
                        this.pagination = response.meta.pagination
                    }

                    // Update page results
                    if (response.data.length) {
                        this.pageList = response.data
                    }
                }
            })
        },

        /**
         * After clicking signal the parent component a click took place on
         * a page row and send the cID of the clicked page.
         * @param {Object} page - Page object with all of the properties that the list has available
         */
        choosePage (page) {
            this.$emit('click', page)
        }
    },
    watch: {
        currentPage () {
            this.fetchPages()
        },

        keywords() {
            this.fetchPages()
        },

        routePath () {
            this.fetchPages()
        }
    },
    mounted () {
        this.fetchPages()
    }
}
</script>
