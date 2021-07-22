<template>
    <div class="ccm-item-selector-group">
        <input type="hidden" :name="inputName" :value="selectedEventOccurrenceId"/>

        <div class="ccm-item-selector-choose" v-if="!selectedEventOccurrence && !isLoading">
            <button type="button" @click="openChooser" class="btn btn-secondary">
                {{chooseText}}
            </button>
        </div>

        <div v-if="isLoading">
            <div class="btn-group">
                <div class="btn btn-secondary">
                    <svg class="ccm-loader-dots">
                        <use xlink:href="#icon-loader-circles"/>
                    </svg>
                </div>
                <button type="button" @click="selectedEventOccurrenceId = null"
                        class="ccm-item-selector-reset btn btn-secondary">
                    <i class="fa fa-times-circle"></i>
                </button>
            </div>
        </div>

        <div class="ccm-item-selector-loaded" v-if="selectedEventOccurrence !== null">
            <div class="btn-group">
                <span class="btn btn-secondary">
                    <span class="ccm-item-selector-title">{{selectedEventOccurrence.title}}</span>
                </span>
                <button type="button" @click="selectedEventOccurrenceId = null"
                        class="ccm-item-selector-reset btn btn-secondary">
                    <i class="fa fa-times-circle"></i>
                </button>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    data() {
        return {
            isLoading: false,
            selectedEventOccurrence: null /* json object */,
            selectedEventOccurrenceId: 0 /* integer */
        }
    },
    props: {
        calendarId: {
            type: Number,
            required: true
        },
        inputName: {
            type: String,
            required: true
        },
        eventOccurrenceId: {
            type: Number
        },
        chooseText: {
            type: String
        }
    },
    watch: {
        selectedEventOccurrenceId: {
            handler(value) {
                if (value > 0) {
                    this.loadOccurrence(value)
                } else {
                    this.selectedEventOccurrence = null
                }
                this.$emit('change', value)
            }
        }
    },
    mounted() {
        if (this.eventOccurrenceId) {
            this.selectedEventOccurrenceId = this.eventOccurrenceId
        }
    },
    methods: {
        chooseOccurrence: function (selectedEventOccurrences) {
            this.selectedEventOccurrenceId = selectedEventOccurrences[0]
        },
        openChooser: function () {
            var my = this
            $.fn.dialog.open({
                title: my.chooseText,
                href: CCM_DISPATCHER_FILENAME + '/ccm/calendar/dialogs/choose_event?caID=' + my.calendarId,
                width: '90%',
                modal: true,
                height: '70%'
            })
            ConcreteEvent.unsubscribe('CalendarEventSearchDialogSelectEvent')
            ConcreteEvent.subscribe('CalendarEventSearchDialogSelectEvent', function(e, data) {
                $.fn.dialog.closeTop()
                my.loadOccurrence(data.occurrenceID)
            })
        },
        loadOccurrence(eventOccurrenceId) {
            var my = this
            my.isLoading = true
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: CCM_DISPATCHER_FILENAME + '/ccm/calendar/event_occurrence/get_json',
                data: { eventOccurrenceID: eventOccurrenceId },
                error: function(r) {
                    ConcreteAlert.dialog('Error', r.responseText)
                },
                success: function(r) {
                    my.selectedEventOccurrence = r
                    my.selectedEventOccurrenceId = r.id
                    my.isLoading = false
                }
            })
        }

    }
}
</script>
