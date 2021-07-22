<template>
    <div class="row">
        <div class="mb-3 col-sm-12">
            <header>
                <button type="button" @click="toggleFormFactor" v-if="showFormFactorSelector"
                        class="btn btn-sm float-end btn-secondary">
                    <i v-if="resultsFormFactor === 'grid'" class="fas fa-th"></i>
                    <i v-if="resultsFormFactor === 'list'" class="fas fa-list"></i>
                </button>
                <h5>{{title}}</h5>
                <breadcrumb v-if="breadcrumbItems" :breadcrumb-items="breadcrumbItems" @itemClick="onBreadcrumbItemClick" />
            </header>
        </div>
    </div>
</template>

<script>
import Breadcrumb from '../../Breadcrumb'

export default {
    components: {
        Breadcrumb
    },
    props: {
        title: {
            type: String,
            required: true
        },
        showFormFactorSelector: {
            type: Boolean,
            required: false,
            default: true
        },
        resultsFormFactor: {
            type: String,
            required: false,
            default: 'grid',
            validator: value => ['grid', 'list'].indexOf(value) !== -1
        },
        breadcrumbItems: {
            type: Array,
            required: false
        }
    },
    methods: {
        toggleFormFactor() {
            const my = this
            if (this.resultsFormFactor === 'grid') {
                my.$emit('update:resultsFormFactor', 'list')
            } else {
                my.$emit('update:resultsFormFactor', 'grid')
            }
        },
        onBreadcrumbItemClick(item) {
            this.$emit('breadcrumbItemClick', item)
        }
    }
}
</script>
