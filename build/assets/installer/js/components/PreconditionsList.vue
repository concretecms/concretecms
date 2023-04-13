<template>
    <table class="table requirements-table">
        <tbody>
        <component @precondition-failed="preconditionFailed"
                   :key="executedPrecondition.precondition.key"
                   v-for="executedPrecondition in preconditions"
                   :is="executedPrecondition.precondition.component"
                   :precondition="executedPrecondition" />
        </tbody>
    </table>
</template>
<script>
import Precondition from "./Preconditions/Precondition";
import RequestUrlsPrecondition from "./Preconditions/RequestUrlsPrecondition";
import CookiesPrecondition from "./Preconditions/CookiesPrecondition";
export default {
    components: {
        Precondition,
        RequestUrlsPrecondition,
        CookiesPrecondition
    },
    methods: {
        preconditionFailed(precondition) {
            this.$emit('precondition-failed', precondition)
        }
    },
    props: {
        preconditions: {
            type: Array,
            required: true
        }
    },
    data: () => ({
    }),
    mounted() {
    }
}
</script>
