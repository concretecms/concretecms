<template>
    <div class="d-flex">
        <input type="number" class="form-control form-control-sm" v-model="size" @change="componentUpdated">
        <select class="form-select form-select-sm ms-1" style="width: 80px;" v-model="unit" @change="componentUpdated">
            <option value="rem">rem</option>
            <option value="em">em</option>
            <option value="px">px</option>
        </select>
    </div>
</template>

<script>

export default {
    components: {},
    data() {
        return {
            size: this.styleValue.value.size,
            unit: this.styleValue.value.unit
        }
    },
    methods: {
        componentUpdated: function () {
            this.$emit('update', {
                variable: this.styleValue.style.variable,
                value: {
                    size: this.size,
                    unit: this.unit
                }
            })
        }
    },
    computed: {

    },
    props: {
        styleValue: {
            type: Object
        }
    }
}
</script>
