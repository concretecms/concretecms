<template>
    <form class="w-100">
        <div class="form-group">
            <p class="lead">{{ i18n.chooseLanguage }}</p>
            <div class="input-group-lg input-group">
                <select v-model="selectedLocale" class="form-select form-select-lg">
                    <optgroup :label="i18n.installedLanguages" v-if="Object.entries(locales).length">
                        <option v-for="(locale, code) in locales" :value="code">{{ locale }}</option>
                    </optgroup>
                    <optgroup :label="i18n.availableLanguages" v-if="Object.entries(onlineLocales).length">
                        <option v-for="(locale, code) in onlineLocales" :value="code">{{ locale }}</option>
                    </optgroup>
                </select>
                <button type="button" class="btn btn-primary" @click="setLanguage">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </form>
</template>
<script>

export default {
    components: {
    },
    methods: {
        setLanguage() {
            var my = this
            $.fn.dialog.showLoader()
            $.ajax({
                cache: false,
                dataType: 'json',
                method: 'GET',
                url: my.loadStringsUrl + '/' + my.selectedLocale,
                success(r) {
                    my.$emit('set-locale', my.selectedLocale)
                    my.$emit('set-language-strings', r.i18n)
                    my.$emit('set-preconditions', r.preconditions)
                    my.$emit('next')
                },
                complete() {
                    $.fn.dialog.hideLoader()
                }
            })
        }
    },
    computed: {

    },
    props: {
        loadStringsUrl: {
            type: String,
            required: true
        },
        locale: {
            type: String,
            required: false
        },
        onlineLocales: {
            type: Object,
            required: true
        },
        locales: {
            type: Object,
            required: true
        },
        lang: {
            type: Object,
            required: true
        }
    },
    data: () => ({
        selectedLocale: null,
        i18n: {}
    }),
    mounted() {
        this.selectedLocale = this.locale
        if (!this.selectedLocale) {
            this.selectedLocale = 'en_US'
        }
        this.i18n = this.lang
    }
}
</script>
