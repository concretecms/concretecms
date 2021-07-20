<template>
    <select @change="componentUpdated" class="form-select form-select-sm" v-model="textTransform"
            :style="{'text-transform': textTransform}">
        <option v-for="transformValue in transformValues" :value="transformValue">{{ transformValue }}</option>
    </select>
</template>

<script>

export default {
    components: {},
    data() {
        return {
          textTransform: this.styleValue.value.textTransform
        }
    },
    methods: {
        componentUpdated: function () {
            this.$emit('update', {
                variable: this.styleValue.style.variable,
                value: {
                    textTransform: this.textTransform
                }
            })
        }
    },
    computed: {
        transformValues: function () {
            return [
                'none',
                'uppercase',
                'lowercase',
                'capitalize',
                'full-width',
                'full-width-kana',
            ]
        }
    },
    props: {
        styleValue: {
            type: Object
        }
    }
}
</script>
