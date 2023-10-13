<template>
    <tr>
        <td>
            <i v-if="precondition.result.state === 1" class="text-success fas fa-check"></i>
            <i v-else-if="precondition.result.state === 2" class="text-warning fas fa-exclamation-triangle"></i>
            <i v-else-if="precondition.result.state === 4" class="text-danger fas fa-exclamation-circle"></i>
        </td>
        <td class="w-100"><span :class="{'text-danger': precondition.result.state === 4}">{{precondition.precondition.name}}</span></td>
        <td><i v-if="precondition.result.message" class="fas fa-question-circle launch-tooltip" :title="precondition.result.message"></i></td>
    </tr>
</template>
<script>
export default {
    components: {
    },
    props: {
        precondition: {
            type: Object,
            required: true
        }
    },
    mounted() {
        this.createTooltips()
        if (this.precondition.result.state !== 1) {
            this.$emit('precondition-failed', this.precondition)
        }
    },
    methods: {
        createTooltips() {
            this.$el.querySelectorAll('.launch-tooltip').forEach((o) => {
                new bootstrap.Tooltip(o)
            })
        }
    },
    data: () => ({
    })
}
</script>