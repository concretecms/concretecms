<template>
    <div v-if="showControl">
    <button
        type="button"
        class="btn btn-secondary p-2 me-3 dropdown-toggle"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false">

    <i class="far fa-folder"></i>

        <span class="ms-3 me-3">Favorites</span>

    </button>

    <ul class="dropdown-menu">
        <li v-for="(label, value) in options" :value="value">
            <a class="dropdown-item" :href="getFolderLink(value)">
                {{label}}
            </a>
        </li>
    </ul>

    </div>
</template>

<script>
export default {

    data() {
        return {
            options: {},
        }
    },
    props: {
    },
    watch: {

    },
    mounted() {
        this.refresh()
        this.bindEvents()
    },
    computed: {
        showControl: function() {
            return Object.keys(this.options).length > 0
        }
    },
    methods: {
        bindEvents() {
            var my = this
            ConcreteEvent.subscribe('FileManagerRefreshFavoriteFolderList', function () {
                // fetch user favorite folders and render list
                new ConcreteAjaxRequest({
                    url: CCM_DISPATCHER_FILENAME + "/ccm/system/file/get_favorite_folders",
                    loader: false,
                    success: function (response) {
                        if (Object.keys(response.favoriteFolders).length) {
                            my.options = response.favoriteFolders
                        } else {
                            my.options = {}
                        }
                    }
                })
            })
        },
        refresh() {
            var my = this
            new ConcreteAjaxRequest({
                url: CCM_DISPATCHER_FILENAME + "/ccm/system/file/get_favorite_folders",
                loader: false,
                success: function(response) {
                    if (Object.keys(response.favoriteFolders).length) {
                        my.options = response.favoriteFolders
                    }
                }
            })
        },
        getFolderLink(value) {
            return CCM_DISPATCHER_FILENAME + "/dashboard/files/search/folder/" + value
        }
    }
}
</script>