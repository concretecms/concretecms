<template functional>
    <div class='menubutton-wrapper dropdown'>
        <IconButton
            @click='listeners.click'
            v-bind="{
                type: props.active ? $options.buttonTypes.save : $options.buttonTypes.outline,
                icon: props.icon,
                iconType: props.iconType
            }">
            {{ props.title }}
        </IconButton>
        <slot></slot>
    </div>
</template>
<script>
import ContextMenu from './ContextMenu'
import MenuItem from './MenuItem'
import MenuDivider from './MenuDivider'
import IconButton, { types as buttonTypes } from '../IconButton'
import { icons, types } from '../iconlist'

export default {
    buttonTypes,
    components: {
        IconButton,
        ContextMenu,
        MenuItem,
        MenuDivider,
        ...IconButton.components
    },
    props: {
        title: String,
        icon: {
            type: String,
            default: icons.fas.ellipsisH
        },
        iconType: {
            type: String,
            default: types.fas
        },
        active: {
            type: Boolean,
            required: true
        }
    }
}
</script>
