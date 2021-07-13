<template>
    <div>
        <label class="control-label">{{ styleValue.style.name }}</label>
        <select @change="componentUpdated" class="form-select form-control" v-model="selectedFont"
                :style="{'font-family': selectedFont}">
            <option v-for="font in fonts" :value="font">{{ font }}</option>
        </select>
    </div>
</template>

<script>

export default {
    components: {},
    data() {
        return {
            selectedFont: this.styleValue.value.fontFamily
        }
    },
    methods: {
        componentUpdated: function () {
            this.$emit('update', {
                variable: this.styleValue.style.variable,
                value: {
                    fontFamily: this.selectedFont
                }
            })
        }
    },
    computed: {
        fonts: function () {
            var fonts = [
                this.styleValue.value.fontFamily,
                'Helvetica',
                'Verdana',
                'Georgia'
            ]
            return fonts
        }
    },
    props: {
        styleValue: {
            type: Object
        }
    }
}
</script>
