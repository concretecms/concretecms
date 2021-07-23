<template functional>
    <i v-if='$options.methods.isFontAwesome(props.type)' class="icon"
        :class="[{
            fas: props.type === props.iconTypes.fas,
            far: props.type === props.iconTypes.far,
            fab: props.type === props.iconTypes.fab,
        }, (props.icon || []).indexOf('fa-') === 0 ? props.icon : `fa-${props.icon}`]"
        :style="{ color: props.color }"
    />
    <svg v-else-if='$options.methods.isSvg(props.type)' viewport='0 0 20 20' width='20px' height='20px'>
        <use :xlink:href='`${props.spritePath}#icon-${props.icon}`' :style='`fill: ${props.color}`'></use>
    </svg>
    <span v-else>Invalid icon type.</span>
</template>

<script>
import Vue from 'vue'
import { icons, types } from './iconlist'

// Reexport the icons and types to make them easy to get at
export { icons, types }

// Export our component definition
export default {
    props: {
        spritePath: {
            type: String,
            default: Vue.config.spritePath || '/concrete/images/icons/bedrock/sprites.svg'
        },
        icon: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: types.fas,
            validator: type => types[type] === type
        },
        color: {
            type: String,
            default: 'currentColor'
        },
        iconTypes: {
            default: () => types
        },
        iconList: {
            default: () => icons
        }
    },
    methods: {
        /**
         * Filters for checking types, these have to be methods because you can't use piped filters with v-if
         */
        isFontAwesome: type => [types.fas, types.far, types.fab].indexOf(type) >= 0,
        isSvg: type => type === types.svg
    }
}
</script>
