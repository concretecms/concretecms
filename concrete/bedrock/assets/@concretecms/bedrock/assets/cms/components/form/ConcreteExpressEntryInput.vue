<template>
  <div class="ccm-item-selector-group">
    <input type="hidden" :name="inputName" :value="selectedEntryId" />

    <div class="ccm-item-selector-choose" v-if="!selectedEntry && !isLoading">
      <button type="button" @click="openChooser" class="btn btn-secondary">
        {{chooseText}}
      </button>
    </div>

    <div v-if="isLoading">
      <div class="btn-group">
        <div class="btn btn-secondary"><svg class="ccm-loader-dots"><use xlink:href="#icon-loader-circles" /></svg></div>
        <button type="button" @click="selectedEntryId = null" class="ccm-item-selector-reset btn btn-secondary">
          <i class="fa fa-times-circle"></i>
        </button>
      </div>
    </div>

    <div class="ccm-item-selector-loaded" v-if="selectedEntry !== null">
      <div class="btn-group">
        <a :href="selectedEntry.url" target="_blank" class="btn btn-secondary">
          <span class="ccm-item-selector-title">{{selectedEntry.label}}</span>
        </a>
        <button type="button" @click="selectedEntryId = null" class="ccm-item-selector-reset btn btn-secondary">
          <i class="fa fa-times-circle"></i>
        </button>
      </div>
    </div>

  </div>
</template>

<script>
/* eslint-disable indent */
export default {
  data() {
    return {
      isLoading: false,
      selectedEntry: null /* json object */,
      selectedEntryId: 0 /* integer */
    }
  },
  props: {
    inputName: {
      type: String,
      required: true
    },
    entryId: {
      type: String
    },
    entityId: {
      type: String
    },
    chooseText: {
      type: String
    }
  },
  watch: {
    selectedEntryId: {
      handler(value) {
        if (value > 0) {
          this.loadEntry(value)
        } else {
          this.selectedEntry = null
        }
        this.$emit('change', value)
      }
    }
  },
  mounted() {
    if (this.entryId) {
      this.selectedEntryId = this.entryId
    }
  },
  methods: {
    chooseEntry: function(selectedExpressEntries) {
      this.selectedEntryId = selectedExpressEntries[0]
    },
    openChooser: function() {
      var my = this
      window.ConcreteExpressEntryAjaxSearch.launchDialog(this.entityId, function(data) {
        my.loadEntry(data.exEntryID)
      })
    },
    loadEntry(entryId) {
      var my = this
      my.isLoading = true
      window.ConcreteExpressEntryAjaxSearch.getEntryDetails(entryId, function(r) {
        my.selectedEntry = r.entries[0]
        my.selectedEntryId = r.entries[0].exEntryID
        my.isLoading = false
      })
    }

  }
}
</script>
