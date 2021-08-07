<template>
    <flyout-menu :label="styleValue.style.name" @save="componentUpdated">
        <template v-slot:icon>
            T
        </template>
        <template v-slot:content>
            <div class="mb-2">
                <label class="form-label">Font Family</label>
                <div class="d-flex">
                    <font-family-page-customizer-widget @update="componentUpdated" v-if="fontFamilySubTypeValue" :style-value="fontFamilySubTypeValue"></font-family-page-customizer-widget>
                    <div class="ms-1 d-flex align-items-center" v-if="colorSubTypeValue">
                        <color-page-customizer-widget @update="componentUpdated" :style-value="colorSubTypeValue"></color-page-customizer-widget>
                    </div>
                </div>
            </div>
            <div class="mb-2" v-if="fontSizeSubTypeValue">
                <label class="form-label">Size</label>
                <size-page-customizer-widget @update="componentUpdated" :style-value="fontSizeSubTypeValue"></size-page-customizer-widget>
            </div>
            <div class="mb-2" v-if="fontWeightSubTypeValue">
                <label class="form-label">Font Weight</label>
                <font-weight-page-customizer-widget @update="componentUpdated" :style-value="fontWeightSubTypeValue"></font-weight-page-customizer-widget>
            </div>
            <div class="mb-2" v-if="fontStyleSubTypeValue">
                <label class="form-label">Font Style</label>
                <font-style-page-customizer-widget @update="componentUpdated" :style-value="fontStyleSubTypeValue"></font-style-page-customizer-widget>
            </div>
            <div class="mb-2" v-if="textDecorationSubTypeValue">
                <label class="form-label">Text Decoration</label>
                <text-decoration-page-customizer-widget @update="componentUpdated" :style-value="textDecorationSubTypeValue"></text-decoration-page-customizer-widget>
            </div>
            <div class="mb-2" v-if="textTransformSubTypeValue">
                <label class="form-label">Text Transform</label>
                <text-transform-page-customizer-widget @update="componentUpdated" :style-value="textTransformSubTypeValue"></text-transform-page-customizer-widget>
            </div>
        </template>
    </flyout-menu>
</template>

<script>
import ColorPageCustomizerWidget from './ColorPageCustomizerWidget'
import FontFamilyPageCustomizerWidget from './FontFamilyPageCustomizerWidget'
import SizePageCustomizerWidget from './SizePageCustomizerWidget'
import FontWeightPageCustomizerWidget from './FontWeightPageCustomizerWidget'
import FontStylePageCustomizerWidget from './FontStylePageCustomizerWidget'
import TextTransformPageCustomizerWidget from './TextTransformPageCustomizerWidget'
import TextDecorationPageCustomizerWidget from './TextDecorationPageCustomizerWidget'
import FlyoutMenu from './flyout/FlyoutMenu'

export default {
    components: {
        FlyoutMenu,
        ColorPageCustomizerWidget,
        FontFamilyPageCustomizerWidget,
        SizePageCustomizerWidget,
        FontWeightPageCustomizerWidget,
        FontStylePageCustomizerWidget,
        TextTransformPageCustomizerWidget,
        TextDecorationPageCustomizerWidget
    },
    data() {
        return {
            fontFamilySubTypeValue: null,
            colorSubTypeValue: null,
            fontSizeSubTypeValue: null,
            fontWeightSubTypeValue: null,
            fontStyleSubTypeValue: null,
            textDecorationSubTypeValue: null,
            textTransformSubTypeValue: null
        }
    },
    methods: {
        componentUpdated: function (data) {
            this.$emit('update', data)
        }
    },
    mounted() {
        var my = this
        this.styleValue.value.values.forEach(function(styleValue) {
            if (styleValue.style.type === 'color') {
                my.colorSubTypeValue = styleValue
            }
            if (styleValue.style.type === 'font_family') {
                my.fontFamilySubTypeValue = styleValue
            }
            if (styleValue.style.type === 'size') {
                my.fontSizeSubTypeValue = styleValue
            }
            if (styleValue.style.type === 'font_weight') {
                my.fontWeightSubTypeValue = styleValue
            }
            if (styleValue.style.type === 'font_style') {
                my.fontStyleSubTypeValue = styleValue
            }
            if (styleValue.style.type === 'text_decoration') {
                my.textDecorationSubTypeValue = styleValue
            }
            if (styleValue.style.type === 'text_transform') {
                my.textTransformSubTypeValue = styleValue
            }
        })
    },
    computed: {},
    props: {
        styleValue: {
            type: Object
        }
    }
}
</script>
