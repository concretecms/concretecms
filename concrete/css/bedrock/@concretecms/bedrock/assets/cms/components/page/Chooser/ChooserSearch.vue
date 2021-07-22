<template>
    <div class="ccm-page-chooser-search-view">
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
        <div v-if="keywords">
            <PageList
                :keywords="keywords"
                :routePath="routePath"
                @click="page => choosePage(page)"/>

        </div>
    </div>
</template>

<script>
import Icon from '../../Icon'
import PageList from './../PageList'

export default {
    components: { Icon, PageList },
    data: () => ({
        searchText: '',
        keywords: '',
        routePath: '/ccm/system/page/chooser/search/'
    }),
    methods: {
        search () {
            this.keywords = this.searchText
        },
        choosePage (page) {
            window.ConcreteEvent.publish('SitemapSelectPage', {
                instance: this,
                cID: page.cID,
                title: page.name
            })
        }
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
