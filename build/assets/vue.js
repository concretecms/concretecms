import Vue from 'vue'
import components from "@concretecms/bedrock/assets/cms/components"

// Announce that vue is ready
window.dispatchEvent(new CustomEvent('concrete.vue.ready', {
  detail: {
    Vue,
    components,
  }
}))
