<template>
    <select @change="componentUpdated" class="selectpicker" v-model="selectedFont">
        <option v-for="font in webFonts" :value="font" :data-content="'<span style=\'font-family: ' + font + '\'>' + font + '</span>'">{{ font }}</option>
        <option v-for="font in standardFonts" :value="font" :data-content="'<span style=\'font-family: ' + font + '\'>' + font + '</span>'">{{ font }}</option>
    </select>
</template>

<script>

export default {
    components: {},
    data() {
        return {
            selectedFont: this.styleValue.value.fontFamily
        }
    },
    mounted() {
        $('select.selectpicker').selectpicker()
        WebFont.load({
            google: {
                families: ['Abril Fatface', 'Oswald', 'Oxygen']
            }
        });
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
        webFonts: function() {
            return [
                'Abril Fatface',
                'Oxygen',
                'Oswald',
            ]
        },
        standardFonts: function () {
            return [
/*                this.styleValue.value.fontFamily, */
                'Helvetica',
                'Arial',
                'Arial Black',
                'Verdana',
                'Tahoma',
                'Trebuchet MS',
                'Impact',
                'Times New Roman',
                'Didot',
                'Georgia',
                'Garamond',
                'American Typewriter',
                'Andale Mono',
                'Courier New',
                'Lucida Console',
                'Monaco',
                'Brush Script MT',
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
