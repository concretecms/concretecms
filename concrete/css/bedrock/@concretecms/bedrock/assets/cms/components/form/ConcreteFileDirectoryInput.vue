<template>
    <div>
        <div class="ccm-directory-selector-container">
            <div class="form-group">
                <label :for="directorySelectInputId" v-if="inputLabel">{{inputLabel}}</label>
                <div v-if="showAddDirectoryButton" class="input-group">
                    <select :id="directorySelectInputId" :name="inputName" data-size="5" data-live-search="true" class="ccm-directory-selector form-control" v-model="selectedDirectoryID" ref="directoryInput" :disabled="disabled">
                        <option v-for="directory in directories" :class="`level-${directory.directoryLevel}`" :value="directory.directoryId" data-icon="fas fa-folder">
                            {{ directory.directoryName }}
                        </option>
                    </select>
                    <div class="input-group-append">
                        <button type="button"
                                :class="{'btn': true, 'btn-outline-secondary': true, 'ccm-create-new-directory-button': true, 'disabled': disabled === true}"
                                @click="toggleDirectoryInput" :disabled="disabled">
                            Create New Folder
                        </button>
                    </div>
                </div>
                <select v-else :id="directorySelectInputId" :name="inputName" class="ccm-directory-selector form-control" v-model="selectedDirectoryID" ref="directoryInput" :disabled="disabled">
                    <option v-for="directory in directories" :class="`level-${directory.directoryLevel}`" :value="directory.directoryId" data-icon="fas fa-folder">
                        {{ directory.directoryName }}
                    </option>
                </select>
            </div>
        </div>
        <div v-if="showAddDirectoryButton" v-show="showAddDirectoryInput" class="ccm-new-directory-name-container">
            <div class="form-group">
                <label :for="directoryInputId">Name</label>
                <div class="input-group">
                    <input type="text"
                           :id="directoryInputId"
                           placeholder="Please enter a name..." class="ccm-new-directory-name form-control"
                           v-model="newDirectoryName" @keyup.enter.stop.prevent="createDirectory" :disabled="disabled">
                    <div class="input-group-append">
                        <button type="button"
                                :class="{'btn': true, 'btn-outline-secondary': true, 'disabled': disabled === true}"
                                @click.stop.prevent="createDirectory" :disabled="disabled">
                            Add
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
/* eslint-disable no-new */
/* global CCM_DISPATCHER_FILENAME, CCM_SECURITY_TOKEN, ConcreteAjaxRequest, _ */
export default {
    data: () => ({
        showAddDirectoryInput: false,
        selectedDirectoryID: 0,
        directorySelectInputId: _.uniqueId('input-'),
        directoryInputId: _.uniqueId('input-'),
        directories: [],
        newDirectoryName: ''
    }),
    props: {
        inputLabel: {
            type: String
        },
        inputName: {
            type: String,
            required: true
        },
        directoryId: {
            type: Number
        },
        showAddDirectoryButton: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    watch: {
        selectedDirectoryID() {
            $(this.$refs.directoryInput).selectpicker('val', this.selectedDirectoryID)

            this.$emit('change', parseInt(this.selectedDirectoryID))
        },
        directories() {
            const me = this
            me.$nextTick(() => {
                $(me.$refs.directoryInput).selectpicker('refresh')

                if (me.directories.length > 0) {
                    const isSelectedOptionInDirList = _.findWhere(me.directories, { directoryId: me.selectedDirectoryID }) !== undefined

                    if (!isSelectedOptionInDirList) {
                        me.selectedDirectoryID = _.first(me.directories).directoryId
                    }
                } else {
                    me.selectedDirectoryID = 0
                }

                me.showAddDirectoryInput = false
                me.newDirectoryName = ''
            })
        },
        disabled() {
            const me = this
            me.$nextTick(function () {
                $(me.$refs.directoryInput).selectpicker('refresh')
            })
        }
    },
    created() {
        this.fetchDirectories()
    },
    mounted() {
        $(this.$refs.directoryInput).selectpicker()

        if (this.directoryId) {
            this.selectedDirectoryID = this.directoryId
        }
    },
    methods: {
        createDirectory() {
            const me = this
            if (!me.showAddDirectoryInput || me.disabled) {
                return
            }

            new ConcreteAjaxRequest({
                url: `${CCM_DISPATCHER_FILENAME}/ccm/system/file/create_directory?_=${Date.now()}`,
                data: {
                    ccm_token: CCM_SECURITY_TOKEN,
                    directoryName: me.newDirectoryName,
                    currentFolder: me.selectedDirectoryID
                },
                success: function (r) {
                    // re-fetch the directories and select the new folder
                    me.fetchDirectories()
                    me.selectedDirectoryID = r.directoryId
                }
            })
        },
        fetchDirectories() {
            const me = this
            new ConcreteAjaxRequest({
                url: `${CCM_DISPATCHER_FILENAME}/ccm/system/file/fetch_directories?_=${Date.now()}`,
                loader: false,
                data: {
                    ccm_token: CCM_SECURITY_TOKEN
                },
                success: function (r) {
                    me.directories = r.directories
                }
            })
        },
        toggleDirectoryInput() {
            const me = this
            if (!me.showAddDirectoryButton || me.disabled) {
                return
            }

            me.showAddDirectoryInput = !me.showAddDirectoryInput

            me.$emit('toggleDirectoryInput', me.showAddDirectoryInput)
        }
    }
}
</script>
