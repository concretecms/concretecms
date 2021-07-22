<template>
    <select @change="componentUpdated" class="form-select form-select-sm" v-model="textDecoration"
            :style="{'text-decoration': textDecoration}">
      <option v-for="decorationValue in decorationValues" :value="decorationValue">{{ decorationValue }}</option>
    </select>
</template>

<script>

export default {
  components: {},
  data() {
    return {
      textDecoration: this.styleValue.value.textDecoration
    }
  },
  methods: {
    componentUpdated: function () {
      this.$emit('update', {
        variable: this.styleValue.style.variable,
        value: {
          textDecoration: this.textDecoration
        }
      })
    }
  },
  computed: {
    decorationValues: function () {
      return [
        'none',
        'underline',
        'line-through',
        'overline',
        'underline overline'
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
