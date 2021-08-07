<template>
    <div>
        <svg v-if="isLoading" class="ccm-loader-dots"><use xlink:href="#icon-loader-circles" /></svg>
        <div v-if="!isLoading">
            <table class="ccm-search-results-table">
                <thead>
                    <tr>
                        <th></th>
                        <th @click="sortBy('name')" :class="getSortColumnClassName('name')"><a href="javascript:void(0)">Username</a></th>
                        <th @click="sortBy('email')" :class="getSortColumnClassName('email')"><a href="javascript:void(0)">Email</a></th>
                        <th @click="sortBy('dateAdded')" :class="getSortColumnClassName('dateAdded')"><a href="javascript:void(0)">Date</a></th>
                        <th>Status</th>
                        <th @click="sortBy('totalLogins')" :class="getSortColumnClassName('totalLogins')"><a href="javascript:void(0)"># Logins</a></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in sortedUserList" :key="user.id + 'list'">
                        <td>
                            <input type="checkbox" v-if="multipleSelection" v-model="selectedUsers" :id="'user-' + user.id" :value="user.id">
                            <input type="radio" v-if="!multipleSelection" v-model="selectedUsers" :id="'user-' + user.id" :value="user.id">
                        </td>
                        <td>{{user.name}}</td>
                        <td>{{user.email}}</td>
                        <td>{{user.dateAdded}}</td>
                        <td>{{user.status}}</td>
                        <td>{{user.totalLogins}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
/* eslint-disable no-new */
export default {
    data() {
        return {
            isLoading: true,
            userList: [],
            selectedUsers: [],
            sortByColumn: 'dateAdded',
            sortByDirection: 'desc'
        }
    },
    props: {
        routePath: {
            type: String,
            required: true
        },
        multipleSelection: {
            type: Boolean,
            default: true
        }
    },
    methods: {
        getUsers() {
            const my = this
            my.isLoading = true
            new ConcreteAjaxRequest({
                url: CCM_DISPATCHER_FILENAME + this.$props.routePath,
                error: function(r) {
                    ConcreteAlert.dialog(ccmi18n.error, ConcreteAjaxRequest.errorResponseToString(r))
                },
                success: function (r) {
                    if (r.data.length) {
                        my.userList = r.data
                    }

                    my.isLoading = false
                }
            })
        },
        sortBy(column) {
            if (column === this.sortByColumn) {
                this.sortByDirection = this.sortByDirection === 'asc' ? 'desc' : 'asc'
            }

            this.sortByColumn = column
        },
        getSortColumnClassName(column) {
            let className = ''
            if (column === this.sortByColumn) {
                className = `ccm-results-list-active-sort-${this.sortByDirection}`
            }

            return className
        }
    },
    computed: {
        sortedUserList() {
            const sorted = _.sortBy(this.userList, this.sortByColumn)

            if (this.sortByDirection === 'desc') {
                return sorted.reverse()
            }

            return sorted
        }
    },
    watch: {
        selectedUsers(value) {
            if (!Array.isArray(value)) {
                value = [value]
            }

            this.$emit('update:selectedUsers', value)
        },
        routePath() {
            this.getUsers()
        }
    },
    mounted() {
        this.getUsers()
    }
}
</script>
