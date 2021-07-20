<template>
    <div class="ms-auto d-flex dropend">
        <a href="javascript:void(0)" data-bs-auto-close="false" data-bs-toggle="dropdown" class="btn btn-sm btn-secondary">
            <slot name="icon"></slot>
        </a>
        <div class="dropdown-menu">
            <form class="p-2 flyout-menu-form">
                <slot name="content"></slot>
                <!--
                <div class="mt-2">
                    <button type="button" @click="save" class="btn-sm float-end btn btn-primary">Save</button>
                </div>
                //-->
            </form>
        </div>
    </div>
</template>

<style lang="scss">
form.flyout-menu-form {
    min-width: 350px;
    div.ccm-item-selector-group {
        display: grid;
    }
    div.ccm-item-selector-choose {
        display: grid;
    }
}
</style>

<script>
export default {
    components: {},
    data() {
        return {
            dropdown: null
        }
    },
    mounted() {
        // I had to use this weird approach otherwise after my dropdown was manually hidden in the save()
        // method below it would never come back if you clicked the button. So you have to use JS to manage
        // the instantiation if you're going to use JS to manage the hiding.
        //this.dropdown = new bootstrap.Dropdown(this.$el.querySelector('[data-bs-toggle=dropdown]'))
        // We don't actually need this right now, because I'm not using the Save buttons. I like the idea of the save buttons
        // but everything is already working with the direct reload of the frame by changing the form controls
        // so making Save work for the Type component would actually be more work.
    },
    methods: {
        /*
        save() {
            this.$emit('save')
            // close the dropdown
            this.dropdown.hide()
        }
         */
    },
    computed: {

    },
    props: {
        label: {
            type: String
        }
    }
}
</script>
