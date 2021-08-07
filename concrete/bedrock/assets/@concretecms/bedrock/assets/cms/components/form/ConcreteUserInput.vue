<template>
    <div class="ccm-item-selector-group">
        <input type="hidden" :name="inputName" :value="selectedUserID" />

        <div class="ccm-item-selector-choose" v-if="!selectedUser && !isLoading">
            <button type="button" @click="openChooser" class="btn btn-secondary">
                {{chooseText}}
            </button>
        </div>

        <div v-if="isLoading">
            <div class="btn-group">
                <div class="btn btn-secondary"><svg class="ccm-loader-dots"><use xlink:href="#icon-loader-circles" /></svg></div>
                <button type="button" @click="reset" class="ccm-item-selector-reset btn btn-secondary">
                    <i class="fa fa-times-circle"></i>
                </button>
            </div>
        </div>

        <div class="ccm-item-selector-loaded" v-if="selectedUser !== null">
            <div class="btn-group">
                <div class="btn btn-secondary">
                    <span v-html="selectedUser.avatar"></span>
                    <span class="ccm-item-selector-title">{{selectedUser.displayName}}</span>
                </div>
                <button type="button" @click="reset" class="ccm-item-selector-reset btn btn-secondary">
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
            selectedUser: null /* json object */,
            selectedUserID: 0 /* integer */
        }
    },
    props: {
        inputName: {
            type: String,
            required: true
        },
        userId: {
            type: Number
        },
        chooseText: {
            type: String
        }
    },
    watch: {
        selectedUserID: {
            immediate: true,
            handler(value) {
                if (value > 0) {
                    this.loadUser(value)
                } else {
                    this.selectedUser = null
                }
                this.$emit('change', value)
            }
        }
    },
    mounted() {
        if (this.userId) {
            this.selectedUserID = this.userId
        }
    },
    methods: {
        chooseFile: function(selectedUsers) {
            this.selectedUserID = selectedUsers[0]
        },
        openChooser: function() {
            var my = this
            window.ConcreteUserManager.launchDialog(function(r) {
                my.loadUser(r.uID)
            })
        },
        loadUser(userId) {
            var my = this
            my.isLoading = true
            window.ConcreteUserManager.getUserDetails(userId, function(r) {
                my.isLoading = false
                my.selectedUser = r.users[0]
                my.selectedUserID = userId
            })
        },
        reset() {
            this.isLoading = false
            this.selectedUserID = null
        }
    }
}
</script>
