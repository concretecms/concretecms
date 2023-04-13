<template>
    <tr>
        <td>
            <i v-if="requestUrlsSuccess === null" class="fas fa-spinner fa-spin"></i>
            <i v-else-if="requestUrlsSuccess" class="text-success fas fa-check"></i>
            <i v-else class="text-danger fas fa-exclamation-circle"></i>
        </td>
        <td class="w-100"><span :class="{'text-danger': requestUrlsSuccess === false || ajaxFailed}">{{precondition.precondition.name}}</span></td>
        <td><i v-if="requestUrlsSuccess === false || ajaxFailed" class="fas fa-question-circle launch-tooltip" :title="failureMessage"></i></td>
    </tr>
</template>
<script>
export default {
    components: {
    },
    props: {
        precondition: {
            type: Object,
            required: true
        }
    },
    mounted() {
        var my = this
        $.ajax({
            cache: false,
            dataType: 'json',
            method: 'GET',
            url: my.precondition.precondition.ajax_url
        })
            .done(function(data) {
                if (data.response === 400) {
                    my.requestUrlsSuccess = true
                } else {
                    my.requestUrlsSuccess = false
                }
            })
            .fail(function(xhr, textStatus, errorThrown) {
                my.requestUrlsSuccess = false
                my.ajaxFailed = true
            });

    },
    computed: {
        failureMessage() {
            if (this.ajaxFailed) {
                return this.precondition.precondition.ajax_fail_message
            } else if (!this.requestUrlsSuccess) {
                return this.precondition.precondition.error_message
            }
        }
    },
    watch: {
        requestUrlsSuccess: function(value) {
            if (value === false) {
                this.createTooltips()
                this.$emit('precondition-failed', this.precondition)
            }
        }
    },
    methods: {
        createTooltips() {
            this.$nextTick(() => {
                this.$el.querySelectorAll('.launch-tooltip').forEach((o) => {
                    new bootstrap.Tooltip(o)
                })
            })
        }
    },
    data: () => ({
        requestUrlsSuccess: null,
        ajaxFailed: false
    })
}
</script>