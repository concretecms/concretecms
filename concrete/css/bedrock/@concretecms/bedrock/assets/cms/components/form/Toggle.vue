<template functional>
    <div class='ccm-toggle' :class='[props.value ? "on" : "off", props.class]'>
        <span class='affirmative' @click='$options.methods.handleClick(true, props, listeners)'>
            <span class='title'>{{props.affirmativeTitle}}</span>
            <span class='icon'>
                <Icon :type='props.value ? "fas" : "far"' :icon='props.value ? "dot-circle" : "circle"' />
            </span>
        </span>
        <span class='negative' @click='$options.methods.handleClick(false, props, listeners)'>
            <span class='title'>{{props.negativeTitle}}</span>
            <span class='icon'>
                <Icon :type='!props.value ? "fas" : "far"' :icon='!props.value ? "dot-circle" : "circle"' />
            </span>
        </span>
    </div>
</template>

<style lang="scss" scoped>
.ccm-toggle {
  display: flex;

  &.off .affirmative {
    cursor: pointer;
  }

  &.on .negative {
    cursor: pointer;
  }

  .affirmative {
    margin-right: 2px;
  }

  .affirmative,
  .negative {
    align-items: center;
    display: flex;

    > .title {
      margin-right: 8px;
    }

    > .icon {
      align-items: center;
      display: flex;
      height: 30px;
      justify-content: center;
      width: 30px;
    }
  }
}
</style>

<script>
import Icon from '../Icon'

export default {
    components: {
        Icon
    },
    props: {
        value: {
            type: Boolean,
            required: true
        },
        affirmativeTitle: {
            type: String,
            default: 'Yes'
        },
        negativeTitle: {
            type: String,
            default: 'No'
        }
    },
    methods: {
        handleClick(newValue, props, listeners) {
            if (props.value !== newValue) {
                if (listeners.change) {
                    listeners.change(newValue)
                }
                if (listeners.input) {
                    listeners.input(newValue)
                }
            }
        }
    }
}
</script>
