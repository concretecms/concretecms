<template>
    <select @change="componentUpdated" class="form-select form-select-sm" v-model="fontStyle"
            :style="{'font-Style': fontStyle}">
      <option v-for="fontStyleValue in fontStyleValues" :value="fontStyleValue">{{ fontStyleValue }}</option>
    </select>
</template>

<script>

export default {
  components: {},
  data() {
    return {
      fontStyle: this.styleValue.value.fontStyle
    }
  },
  methods: {
    componentUpdated: function () {
      this.$emit('update', {
        variable: this.styleValue.style.variable,
        value: {
          fontStyle: this.fontStyle
        }
      })
    }
  },
  computed: {
    fontStyleValues: function () {
      return [
        'normal',
        'italic',
        'oblique'
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
