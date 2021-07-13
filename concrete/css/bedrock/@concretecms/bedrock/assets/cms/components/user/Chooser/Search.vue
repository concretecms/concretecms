<template>
    <div class="ccm-ui">
        <div class="row mb-3">
            <div class="col-md-6 ml-auto">
                <form @submit.prevent="search">
                    <div class="ccm-header-search-form-input input-group">
                        <input type="text" class="form-control border-right-0" placeholder="Search" autocomplete="false" v-model="searchText">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-icon">
                                <svg width="16" height="16">
                                    <use xlink:href="#icon-search"/>
                                </svg>
                            </button>
                        </div>
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
            <users v-if="keywords"
                :selectedUsers.sync="selectedUsers"
                :multipleSelection="multipleSelection"
                :routePath="this.routePath + keywords"/>
        </div>
    </div>
</template>

<script>
import Icon from '../../Icon'
import Users from '../Chooser/Users'

export default {
    components: {
        Icon,
        Users
    },
    data: () => ({
        keywords: '',
        searchText: '',
        selectedUsers: [],
        routePath: '/ccm/system/user/chooser/search/'
    }),
    methods: {
        search () {
            this.keywords = this.searchText
        }
    },
    props: {
        multipleSelection: {
            type: Boolean,
            default: true
        }
    },
    watch: {
        selectedUsers: function(value) {
            this.$emit('update:selectedUsers', value)
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
