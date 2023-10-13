<template>
    <tr>
        <td>
            <i v-if="cookiesEnabled" class="text-success fas fa-check"></i>
            <i v-else class="text-danger fas fa-exclamation-circle"></i>
        </td>
        <td class="w-100"><span :class="{'text-danger': !cookiesEnabled}">{{precondition.precondition.name}}</span></td>
        <td><i v-if="!cookiesEnabled" class="fas fa-question-circle launch-tooltip" :title="precondition.message_failed"></i></td>
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
        if (this.testCookies()) {
            this.cookiesEnabled = true
        } else {
            this.cookiesEnabled = false
            this.createTooltips()
            this.$emit('precondition-failed', this.precondition)
        }
    },
    methods: {
        createTooltips() {
            this.$el.querySelectorAll('.launch-tooltip').forEach((o) => {
                new bootstrap.Tooltip(o)
            })
        },
        testCookies() {
            if (typeof navigator.cookieEnabled === 'boolean') {
                return navigator.cookieEnabled;
            }
            var COOKIE_NAME = 'CONCRETECMS_INSTALL_TEST', COOKIE_VALUE = 'ok_' + Math.random();
            document.cookie = COOKIE_NAME + '=' + COOKIE_VALUE;
            if (document.cookie.indexOf(COOKIE_NAME + '=' + COOKIE_VALUE) < 0) {
                return false;
            }
            document.cookie = COOKIE_NAME + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT';
            return true;
        }
    },
    data: () => ({
        cookiesEnabled: null
    })
}
</script>