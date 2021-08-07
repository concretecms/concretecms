<template>
    <div class="ccm-block-edit">
        <i class="slot-pinned fas fa-thumbtack" v-if="!slotData.isLocked && slotType === RenderedSlot.SLOT_TYPE_PINNED"></i>
        <i class="slot-pinned fas fa-pencil-ruler" v-if="!slotData.isLocked && slotType === RenderedSlot.SLOT_TYPE_CUSTOM"></i>
        <i class="slot-pinned fas fa-lock" v-if="slotData.isLocked"></i>
        <div :class="'ccm-board-slot-' + slotData.slot + '-content'"><slot></slot></div>
    </div>
</template>

<script>
/* globals ConcreteMenu */
/* eslint-disable no-new */
import '../js/in-context-menu'
// import Vue from 'vue'

export default {
    props: {
        slotData: Object
    },
    data: () => ({
        menu: null,
        slotType: null,
        RenderedSlot: {
            SLOT_TYPE_AUTOMATIC: 'S',
            SLOT_TYPE_PINNED: 'P',
            SLOT_TYPE_CUSTOM: 'C'
        }
    }),
    methods: {},
    watch: {
        slotType: {
            immediate: true,
            handler: function(value) {
                if (this.menu) {
                    if (value === this.RenderedSlot.SLOT_TYPE_PINNED) {
                        this.menu.$menu.find('a[data-menu-action=unpin-item]').show()
                    } else if (value === this.RenderedSlot.SLOT_TYPE_CUSTOM) {
                        this.menu.$menu.find('a[data-menu-action=delete-custom-slot]').show()
                    } else if (value === this.RenderedSlot.SLOT_TYPE_AUTOMATIC) {
                        this.menu.$menu.find('a[data-menu-action=pin-item]').show()
                        this.menu.$menu.find('a[data-menu-action=replace-slot]').show()
                    }
                }
            }
        }
    },
    mounted() {
        this.slotType = this.slotData.slotType

        var my = this

        const menuSelector = 'div[data-menu-board-instance-slot-id=' + my.slotData.slot + ']'
        if ($(menuSelector).find('a').length) {
            /* eslint-disable-next-line no-unused-vars */
            const menu = new ConcreteMenu($(this.$el), {
                highlightClassName: 'ccm-block-highlight',
                menuActiveClass: 'ccm-block-highlight',
                menu: menuSelector
            })

            menu.$menu.find('a').hide()

            menu.$menu.find('a[data-menu-action=pin-item]').on('click', function () {
                new ConcreteAjaxRequest({
                    url: CCM_DISPATCHER_FILENAME + '/ccm/system/board/instance/pin_slot',
                    data: {
                        slot: my.slotData.slot,
                        boardInstanceID: my.slotData.boardInstanceID,
                        bID: my.slotData.bID,
                        action: 'pin'
                    },
                    success: function (r) {
                        /*
                        menu.$menu.find('a').hide()
                        my.slotType = r.slotType
                        */
                        window.location.reload()
                    }
                })
            })

            menu.$menu.find('a[data-menu-action=unpin-item],a[data-menu-action=delete-custom-slot').on('click', function () {
                new ConcreteAjaxRequest({
                    url: CCM_DISPATCHER_FILENAME + '/ccm/system/board/instance/clear_slot',
                    data: {
                        boardInstanceSlotRuleID: my.slotData.boardInstanceSlotRuleID,
                        boardInstanceID: my.slotData.boardInstanceID
                    },
                    success: function (r) {
                        /*
                        menu.$menu.find('a').hide()
                        my.slotType = r.slotType
                         */
                        window.location.reload()
                    }
                })
            })

            menu.$menu.find('a[data-menu-action=replace-slot]').on('click', function () {
                $.fn.dialog.open({
                    href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/boards/custom_slot/replace?boardInstanceID=' +
                        my.slotData.boardInstanceID + '&slot=' + my.slotData.slot,
                    width: '90%',
                    height: '80%',
                    title: 'Replace Slot'
                })
            })

            my.menu = menu

            ConcreteEvent.subscribe('SaveCustomSlot', function (e, data) {
                /*
                // This is hideous. Horrendous. Refactor this to include the BoardSlot component and the chooser
                // of slot data within the same component, using the bootstrap-vue modal component
                if (data.slot === my.slotData.slot) {
                    const res = Vue.compile('<div>' + data.content + '</div>')
                    const element = my.$el.querySelector('.ccm-board-slot-' + data.slot + '-content')
                    new Vue({
                        el: element,
                        render: res.render,
                        staticRenderFns: res.staticRenderFns,
                        state: my.$state
                    })
                }
                 */
                window.location.reload()
            })
        }
    }
}
</script>

<style lang="scss" scoped>
  i.slot-pinned {
    position: absolute;
    right: 0;
    top: 0;
    z-index: 600; // $index-level-inline-commands;
  }
</style>
