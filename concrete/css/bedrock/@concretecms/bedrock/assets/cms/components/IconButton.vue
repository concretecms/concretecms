<template functional>
    <button
        v-bind="{type: props.buttonType, style: props.style}"
        @click='listeners.click'
        class='btn'
        :class="[$options.classMap[props.type] || $options.defaultClass, props.buttonClass]"
        :disabled="props.disabled"
        >
        <Icon v-bind="{icon: props.icon, type: props.iconType, color: props.iconColor}" v-if="props.labelPosition === 'right'" />
        <span class="label" v-if="$options.methods.showSlot(children)">
            <slot />
        </span>
        <Icon v-bind="{icon: props.icon, type: props.iconType, color: props.iconColor}" v-if="props.labelPosition !== 'right'" />
    </button>
</template>

<script>
import Icon from './Icon'

export const types = {
    add: 'add',
    save: 'save',
    delete: 'delete',
    cancel: 'cancel',
    outline: 'outline',
    floating: 'floating'
}

export default {
    classMap: {
        [types.add]: 'btn-success',
        [types.save]: 'btn-primary',
        [types.delete]: 'btn-danger',
        [types.cancel]: 'btn-outline-secondary',
        [types.outline]: 'btn-outline-secondary',
        [types.floating]: 'btn-outline'
    },
    defaultClass: 'btn-outline-primary',
    props: {
        type: {
            type: String,
            default: types.add
        },
        disabled: {
            type: Boolean,
            default: false
        },
        labelPosition: {
            type: String,
            default: 'right'
        },
        icon: {
            type: String,
            required: true
        },
        iconType: {
            type: String
        },
        iconColor: {
            type: String
        },
        buttonType: {
            type: String,
            default: 'button'
        },
        buttonClass: [String, Array, Object]
    },
    components: {
        Icon
    },
    methods: {
        showSlot(children) {
            if (children && children.length) {
                // Handle blank children
                if (children[0].tag === undefined && !children[0].text.trim()) {
                    return false
                }

                return true
            }

            return false
        }
    }
}
</script>

<style lang="scss" scoped>

button {
  .label {
    margin: 0 10px;
  }
}

</style>
