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
        // this.dropdown = new bootstrap.Dropdown(this.$el.querySelector('[data-bs-toggle=dropdown]'))
        // We don't need to instantiate it because the attribute on the dom node handles showing and hiding

        this.$el.querySelector('[data-bs-toggle=dropdown]').addEventListener('show.bs.dropdown', function() {
            $(this).closest('.ccm-panel-content').css('overflow', 'visible')
        })
        this.$el.querySelector('[data-bs-toggle=dropdown]').addEventListener('hide.bs.dropdown', function() {
            $(this).closest('.ccm-panel-content').css('overflow', 'auto')
        })
        // Without the above lines, our flyout menus are hidden inside the panels. We can't remove the auto overflow
        // From panel content because without that our panels aren't scrollable!
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
