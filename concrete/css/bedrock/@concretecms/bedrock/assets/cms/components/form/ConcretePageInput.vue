<template>
    <div class="ccm-item-selector-group">
        <input type="hidden" :name="inputName" :value="selectedPageId" />

        <div class="ccm-item-selector-choose" v-if="!selectedPage && !isLoading">
            <button type="button" @click="openChooser" class="btn btn-secondary">
                {{chooseText}}
            </button>
        </div>

        <div v-if="isLoading">
            <div class="btn-group">
                <div class="btn btn-secondary"><svg class="ccm-loader-dots"><use xlink:href="#icon-loader-circles" /></svg></div>
                <button type="button" @click="selectedPageId = null" class="ccm-item-selector-reset btn btn-secondary">
                    <i class="fa fa-times-circle"></i>
                </button>
            </div>
        </div>

        <div class="ccm-item-selector-loaded" v-if="selectedPage !== null">
            <div class="btn-group">
                <a :href="selectedPage.url" target="_blank" class="btn btn-secondary">
                    <span class="ccm-item-selector-title">{{selectedPage.name}}</span>
                </a>
                <button type="button" @click="selectedPageId = null" class="ccm-item-selector-reset btn btn-secondary">
                    <i class="fa fa-times-circle"></i>
                </button>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    data() {
        return {
            isLoading: false,
            selectedPage: null /* json object */,
            selectedPageId: 0 /* integer */
        }
    },
    props: {
        inputName: {
            type: String,
            required: true
        },
        pageId: {
            type: Number
        },
        chooseText: {
            type: String
        },
        includeSystemPages: {
            type: Boolean,
            default: false
        },
        askIncludeSystemPages: {
            type: Boolean,
            default: false
        }
    },
    watch: {
        selectedPageId: {
            handler(value) {
                if (value > 0) {
                    this.loadPage(value)
                } else {
                    this.selectedPage = null
                }
                this.$emit('change', value)
            }
        }
    },
    mounted() {
        if (this.pageId) {
            this.selectedPageId = this.pageId
        }
    },
    methods: {
        choosePage: function(selectedPages) {
            this.selectedPageId = selectedPages[0]
        },
        openChooser: function() {
            var my = this
            window.ConcretePageAjaxSearch.launchDialog(
                function(data) {
                    my.loadPage(data.cID)
                },
                {
                    includeSystemPages: my.includeSystemPages,
                    askIncludeSystemPages: my.askIncludeSystemPages
                }
            )
        },
        loadPage(cID) {
            var my = this
            my.isLoading = true
            window.ConcretePageAjaxSearch.getPageDetails(cID, function(r) {
                my.selectedPage = r.pages[0]
                my.selectedPageId = r.pages[0].cID
                my.isLoading = false
            })
        }

    }
}
</script>
