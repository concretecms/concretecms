<template>
    <div class="ccm-search-results-pagination">
        <nav :aria-label="ariaLabel">
            <ul class="pagination">
                <li :class="{'page-item': true, 'disabled': prevDisabled}">
                    <a class="page-link" href="#" :aria-label="labelPrevPage" @click.prevent="onClick('prev', $event)">
                        <span aria-hidden="true">{{prevText}}</span>
                    </a>
                </li>
                <li v-for="page in pageList" :key="page.number" :class="{'page-item': true, 'active': page.number === currentPage}"><a class="page-link" href="#" @click.prevent="onClick(page.number)">{{page.text || page.number}}</a></li>
                <li :class="{'page-item': true, 'disabled': nextDisabled}">
                    <a class="page-link" href="#" :aria-label="labelNextPage" @click.prevent="onClick('next', $event)">
                        <span aria-hidden="true">{{nextText}}</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
/**
 * @vue-data {Number} targetNumberOfLinks - Number of pages to display around the current page (including the current page).
 * @vue-data {Number} currentPage - The page that is being displayed.
 * @vue-data {Number} localNumberOfPages - Total number of pages that can be displayed.
 * @vue-prop {String} ariaLabel - Nav aria-label attribute content
 * @vue-prop {String} nextText - Text on the next button
 * @vue-prop {String} labelNextPage - Aria-label attribute on the next button
 * @vue-prop {String} prevText - Text on the previous button
 * @vue-prop {String} labelPrevPage - Aria-label attribute on the previous button
 * @vue-prop {String} mode - Type of paging to display. Modes: 'paging' is regular with page numbers, 'cursor' is next-prev navigation.
 * @vue-prop {Number,String} perPage - Number of results displayed per page
 * @vue-prop {Number,String} totalRows - Total number of results. Use -1 to only display the current page.
 */
export default {
    model: {
        prop: 'value',
        event: 'input'
    },
    props: {
        ariaLabel: {
            type: String
        },
        mode: {
            type: String,
            default: 'paging',
            validator: value => ['paging', 'cursor'].indexOf(value) > -1
        },
        nextCursor: {
            type: [Number, String],
            default: null,
            // Matches a valid curor, which contains page numbers separated by
            // a pipe character or an empty string. Examples: 16 and 11|24|80.
            validator: value => !value || typeof value === 'number' || !!value.match(/^\d+(\|\d+)*$/)
        },
        nextText: {
            type: String,
            default: 'Next →'
        },
        labelNextPage: {
            type: String,
            default: 'Next'
        },
        prevCursor: {
            type: [Number, String, null],
            default: null,
            // Matches a valid curor, which contains page numbers separated by
            // a pipe character or an empty string. Examples: 16 and 11|24|80.
            validator: value => !value || typeof value === 'number' || !!value.match(/^(\d+(\|\d+)*)?$/)
        },
        labelPrevPage: {
            type: String,
            default: 'Previous'
        },
        prevText: {
            type: String,
            default: '← Previous'
        },
        perPage: {
            type: [Number, String],
            default: 20,
            validator: value => value >= 1
        },
        totalRows: {
            type: [Number, String],
            default: 0,
            validator: value => value >= -1
        },
        value: {
            type: [Number, String],
            default: null,
            validator: value => typeof value === 'string' || value >= -1
        }
    },
    data () {
        let currentPage = parseInt(this.value)

        if (this.mode === 'paging') {
            currentPage = currentPage > 0 ? currentPage : 1
        } else {
            currentPage = this.value
        }

        return {
            targetNumberOfLinks: 7,
            currentPage,
            localNumberOfPages: 1
        }
    },
    computed: {
        nextDisabled () {
            return this.mode === 'paging'
                ? this.currentPage === this.localNumberOfPages
                : this.nextCursor === null
        },

        prevDisabled () {
            return this.mode === 'paging'
                ? this.currentPage === 1
                : this.prevCursor === null
        },

        numberOfPages () {
            return Math.max(1, Math.ceil(this.totalRows / this.perPage))
        },

        /**
         * Create the list of pages that should be displayed
         * @returns {{number: *}[]}
         */
        pageList () {
            // If totalRows is -1 we won't figure out nr of pages and don't
            // display the current page number. This happens in 'cusrsor' mode.
            if (this.totalRows === -1 || this.mode === 'cursor') {
                return []
            }

            let startPage = 1
            const nrOfPages = Math.min(this.localNumberOfPages, this.targetNumberOfLinks)
            if (this.localNumberOfPages > nrOfPages) {
                // Set start page to current page minus half of the maximum number of links.
                // This keeps the current page in the middle (unless this sets start page to less then 1).
                startPage = Math.max(1, this.currentPage - Math.ceil((nrOfPages - 1) / 2))

                // Check that startPage hasn't reached the end of the list
                startPage = Math.min(this.localNumberOfPages - nrOfPages + 1, startPage)
            }

            const endPage = startPage + nrOfPages - 1

            const pages = [...new Array(nrOfPages)].map((val, i) => ({ number: startPage + i }))

            // Add start ellipsis
            if (startPage > 3) {
                pages.unshift({ number: -1, text: '...' })
            }

            // Add second page
            if (startPage === 3) {
                pages.unshift({ number: 2 })
            }

            // Add first page
            if (startPage > 1) {
                pages.unshift({ number: 1 })
            }

            // Add end ellipsis
            if (endPage < this.localNumberOfPages - 2) {
                pages.push({ number: -2, text: '...' })
            }

            // Add second to last number
            if (endPage === this.localNumberOfPages - 2) {
                pages.push({ number: this.localNumberOfPages - 1 })
            }

            // Add last number
            if (endPage < this.localNumberOfPages) {
                pages.push({ number: this.localNumberOfPages })
            }

            return pages
        }
    },
    watch: {
        value (newValue, oldValue) {
            this.currentPage = this.mode === 'paging' ? Math.max(1, newValue) : newValue
        },
        currentPage (newValue, oldValue) {
            this.$emit('input', this.mode === 'paging' ? (newValue > 0 ? newValue : null) : newValue)
        }
    },
    created () {
        this.localNumberOfPages = this.numberOfPages
    },
    methods: {
        onClick (num) {
            if (this.mode === 'paging') {
                // Prev/next
                if (num === 'prev') {
                    num = Math.max(1, this.currentPage - 1)
                } else if (num === 'next') {
                    // Gets corrected if too high
                    num = this.currentPage + 1
                }

                if (num < 1) {
                    // Ignore ellipsis clicked
                    return
                } else if (num > this.numberOfPages && this.totalRows > 0) {
                    // Only when totalRows is set limit num because we then also
                    // know the total nr of rows.
                    num = this.numberOfPages
                }
            } else {
                // Prev/next
                if (num === 'prev') {
                    num = this.prevCursor
                } else if (num === 'next') {
                    num = this.nextCursor
                }
            }

            // Update the v-model
            this.currentPage = num

            // Emit event triggered by user interaction
            this.$emit('change', this.currentPage)
        }
    }
}
</script>
