<template>
    <div class="ccm-ui">
        <div class="ccm-install-version">
            <span class="badge bg-info">{{ concreteVersion }}</span>
        </div>
        <div class="ccm-install-title">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">{{ i18n.title }}</li>
                <li class="breadcrumb-item active">{{ stepTitle }}</li>
            </ul>
        </div>

        <div class="alert alert-danger" v-if="environmentErrors.length > 0">
            <span v-html="environmentErrors.join('<br>')"></span>
        </div>

        <transition name="install-step" mode="out-in">
            <choose-language
                v-if="step === 'language'"
                :load-strings-url="loadStringsUrl"
                :locales="locales"
                :locale="locale"
                :online-locales="onlineLocales"
                :lang="lang"
                @set-locale="setLocale"
                @set-language-strings="setLanguageStrings"
                @set-preconditions="setPreconditions"
                @next="next"
            />
            <preconditions
                v-else-if="step === 'requirements'"
                :locale="selectedLocale"
                :lang="lang"
                :preconditions="loadedPreconditions"
                :reload-preconditions-url="reloadPreconditionsUrl"
                @previous="previous"
                @next="next"
            />
            <environment
                v-else-if="step === 'environment'"
                :lang="lang"
                :languages="languages"
                :site-locale-language="siteLocaleLanguage"
                :site-locale-country="siteLocaleCountry"
                :countries="countries"
                :timezone="timezone"
                :timezones="timezones"
                @previous="previous"
                @next="validateEnvironment"
            />
            <starting-point
                v-else-if="step === 'starting_point'"
                :lang="lang"
            />
        </transition>

    </div>
</template>
<script>
import ChooseLanguage from "./ChooseLanguage";
import Preconditions from "./Preconditions";
import Environment from "./Environment";
import StartingPoint from "./StartingPoint";
export default {
    components: {
        ChooseLanguage,
        Preconditions,
        Environment,
        StartingPoint
    },
    methods: {
        mergeEnvironment(environment) {
            environment.locale = this.selectedLocale
            return environment
        },
        validateEnvironment(environment) {
            environment = this.mergeEnvironment(environment)
            var my = this
            $.fn.dialog.showLoader()

            $.ajax({
                cache: false,
                dataType: 'json',
                method: 'post',
                data: environment,
                url: my.validateEnvironmentUrl,
                success(r) {
                    $.fn.dialog.hideLoader()
                    if (r.error && r.error.error) {
                        window.scrollTo(0, 0)
                        my.environmentErrors = r.error.errors
                    } else {
                        my.environmentErrors = []
                    }
                },
                complete() {
                    $.fn.dialog.hideLoader()
                }
            })
        },
        setLocale(locale) {
            this.selectedLocale = locale
        },
        setLanguageStrings(i18n) {
            this.i18n = i18n
        },
        setPreconditions(preconditions) {
            this.loadedPreconditions = preconditions
        },
        previous() {
            if (this.step === 'requirements') {
                this.step = 'language'
            } else if (this.step === 'environment') {
                this.step = 'requirements'
            }
        },
        next() {
            if (this.step === 'environment') {
                this.step = 'starting_point'
            } else if (this.step === 'requirements') {
                this.step = 'environment'
            } else if (this.step === 'language') {
                this.step = 'requirements'
            }
        },
        returnSortedPreconditions(column, required) {
            let preconditions = []
            let num = 0
            this.loadedPreconditions.forEach((precondition) => {
                if ((!required && precondition.is_optional) || (required && !precondition.is_optional)) {
                    preconditions.push(precondition)
                    num++
                }
            })

            if (num > 0) {
                var segmentedPreconditions = []
                preconditions.forEach((precondition, i) => {
                    if (column === 'left' && (i % 2 === 0) || (column === 'right' && (i % 2) === 1)) {
                        segmentedPreconditions.push(precondition)
                    }
                })
                return segmentedPreconditions
            }
            return []
        }
    },
    computed: {
        stepTitle() {
            if (this.step === 'language') {
                return this.i18n.stepLanguage
            }
            if (this.step === 'requirements') {
                return this.i18n.stepRequirements
            }
            if (this.step === 'environment') {
                return this.i18n.stepEnvironment
            }
        }
    },
    props: {
        timezones: {
            type: Object,
            required: true
        },
        timezone: {
            type: String,
            required: false
        },
        validateEnvironmentUrl: {
            type: String,
            required: true
        },
        loadStringsUrl: {
            type: String,
            required: true
        },
        reloadPreconditionsUrl: {
            type: String,
            required: true
        },
        locale: {
            type: String,
            required: false
        },
        concreteVersion: {
            type: String,
            required: true
        },
        preconditions: {
            type: Array,
            required: false
        },
        locales: {
            type: Object,
            required: true
        },
        countries: {
            type: Object,
            required: true
        },
        siteLocaleLanguage: {
            type: String,
            required: false
        },
        siteLocaleCountry: {
            type: String,
            required: false
        },
        languages: {
            type: Object,
            required: true
        },
        lang: {
            type: Object,
            required: true
        },
        onlineLocales: {
            type: Object,
            required: true
        },

    },
    data: () => ({
        step: null,
        selectedLocale: null,
        i18n: {},
        loadedPreconditions: [],
        environmentErrors: []
    }),
    mounted() {
        this.selectedLocale = this.locale
        this.i18n = this.lang
        if (this.preconditions) {
            this.loadedPreconditions = this.preconditions
        }
        if (!this.locale) {
            this.step = 'language'
        } else {
            this.step = 'requirements'
        }
    }
}
</script>
