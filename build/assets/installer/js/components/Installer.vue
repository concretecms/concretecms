<template>
    <div class="ccm-ui">
        <div class="ccm-install-version">
            <small>{{ concreteVersion }}</small>
        </div>

        <div class="alert alert-danger mb-5" v-if="environmentErrors.length > 0">
            <span v-html="environmentErrors.join('<br>')"></span>
        </div>

        <div class="alert alert-warning mb-5" v-if="environmentWarnings.length > 0">
            <span v-html="environmentWarnings.join('<br>')"></span>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="ignoreWarnings" v-model="ignoreWarnings">
                <label class="form-check-label" for="ignoreWarnings">{{ i18n.ignoreWarnings }}</label>
            </div>
        </div>

        <transition name="install-step" mode="out-in">
            <choose-language
                v-if="step === 'language'"
                :logo="logo"
                :load-strings-url="loadStringsUrl"
                :locales="locales"
                :locale="locale"
                :online-locales="onlineLocales"
                :lang="lang"
                @set-locale="setLocale"
                @set-language-strings="setLanguageStrings"
                @set-preconditions="setPreconditions"
                @set-starting-points="setStartingPoints"
                @next="next"
            />
            <preconditions
                v-else-if="step === 'requirements'"
                :locale="selectedLocale"
                :logo="logo"
                :lang="lang"
                :preconditions="loadedPreconditions"
                :reload-preconditions-url="reloadPreconditionsUrl"
                @previous="previous"
                @next="next"
            />
            <choose-content
                v-else-if="step === 'content'"
                :locale="selectedLocale"
                :lang="lang"
                :logo="logo"
                :starting-points="loadedStartingPoints"
                :starting-point="startingPoint"
                @select-starting-point="selectStartingPoint"
                @previous="previous"
            />

            <environment
                v-else-if="step === 'environment'"
                :lang="lang"
                :logo="logo"
                :languages="languages"
                :countries="countries"
                :timezones="timezones"
                :install-options="installOptions"
                @previous="previous"
                @update-install-options="updateInstallOptions"
                @next="validateInstallOptions(true)"
            />

            <confirm-installation
                v-else-if="step === 'confirm'"
                :lang="lang"
                :logo="logo"
                :starting-points="loadedStartingPoints"
                :install-options="installOptions"
                @previous="previous"
                @next="next"
            />

            <perform-installation
                v-else-if="step === 'perform_installation'"
                :begin-installation-url="beginInstallationUrl"
                :lang="lang"
                :logo="logo"
                :install-options="installOptions"
                :starting-point-routine-url="startingPointRoutineUrl"
                @installation-complete="step='installation_complete'"
            />
            <installation-complete
                :logo="logo"
                :installation-complete-url="installationCompleteUrl"
                v-else-if="step === 'installation_complete'"
                :lang="lang"
            />
        </transition>

    </div>
</template>
<script>
import ChooseLanguage from "./ChooseLanguage"
import Preconditions from "./Preconditions"
import Environment from "./Environment"
import ChooseContent from "./ChooseContent"
import PerformInstallation from "./PerformInstallation"
import ConfirmInstallation from "./ConfirmInstallation"
import InstallationComplete from "./InstallationComplete"

export default {
    components: {
        ChooseLanguage,
        Preconditions,
        Environment,
        ChooseContent,
        PerformInstallation,
        ConfirmInstallation,
        InstallationComplete
    },
    watch: {
        environmentErrors: function() {
            window.scrollTo(0, 0)
        },
        environmentWarnings: function(warnings) {
            if (warnings.length) {
                window.scrollTo(0, 0)
            }
        }
    },
    methods: {
        selectStartingPoint(startingPoint) {
            this.startingPoint = startingPoint
            this.next()
        },
        translateOptionPreconditionsToErrorsAndWarnings() {
            this.environmentWarnings = []
            this.optionsPreconditions.forEach((precondition) => {
                if (precondition.result.state === 4) { // failed
                    if (precondition.precondition.is_optional) {
                        if (!this.ignoreWarnings) {
                            this.environmentWarnings.push(precondition.result.message)
                        }
                    } else {
                        this.environmentErrors.push(precondition.result.message)
                    }
                } else if (precondition.result.state === 2) { // warning
                    if (!this.ignoreWarnings) {
                        this.environmentWarnings.push(precondition.result.message)
                    }
                }
            })
        },
        updateInstallOptions(options) {
            this.installOptions = options
            this.installOptions.locale = this.selectedLocale
            this.installOptions.startingPoint = this.startingPoint
        },
        validateInstallOptions(proceedToNextStep) {
            var my = this
            $.fn.dialog.showLoader()

            $.ajax({
                cache: false,
                dataType: 'json',
                method: 'post',
                data: this.installOptions,
                url: my.validateEnvironmentUrl,
                success(r) {
                    $.fn.dialog.hideLoader()
                    if (r.error && r.error.error) {
                        my.environmentErrors = r.error.errors
                    } else {
                        my.environmentErrors = []
                    }
                    my.optionsPreconditions = r.preconditions
                    my.translateOptionPreconditionsToErrorsAndWarnings()
                    if (proceedToNextStep) {
                        if (!my.environmentErrors.length && (!my.environmentWarnings.length || my.ignoreWarnings)) {
                            my.next()
                        }
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
        setStartingPoints(startingPoints) {
            this.loadedStartingPoints = startingPoints
        },
        previous() {
            if (this.step === 'confirm') {
                this.step = 'environment'
            } else if (this.step === 'environment') {
                this.step = 'content'
            } else if (this.step === 'content') {
                this.step = 'requirements'
            } else if (this.step === 'requirements') {
                this.step = 'language'
            }
            this.environmentWarnings = []
            this.environmentErrors = []
        },
        next() {
            if (this.step === 'language') {
                this.step = 'requirements'
            } else if (this.step === 'requirements') {
                this.step = 'content'
            } else if (this.step === 'content') {
                this.step = 'environment'
            } else if (this.step === 'environment') {
                this.step = 'confirm'
            } else if (this.step === 'confirm') {
                this.step = 'perform_installation'
            }
            this.environmentWarnings = []
            this.environmentErrors = []
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
            if (this.step === 'installation_complete') {
                return this.i18n.stepInstallationComplete
            }
            if (this.step === 'perform_installation') {
                return this.i18n.stepPerformInstallation
            }
            if (this.step === 'content') {
                return this.i18n.stepContent
            }
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
        installationCompleteUrl: {
            type: String,
            required: true
        },
        logo: {
            type: String,
            required: true
        },
        defaultStartingPoint: {
            type: String,
            required: false
        },
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
        beginInstallationUrl: {
            type: String,
            required: true
        },
        startingPointRoutineUrl: {
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
        startingPoints: {
            type: Array,
            required: false
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
        loadedStartingPoints: [],
        environmentWarnings: [],
        environmentErrors: [],
        optionsPreconditions: [],
        ignoreWarnings: false,
        startingPoint: null,
        installOptions: {}
    }),
    mounted() {
        this.selectedLocale = this.locale
        this.i18n = this.lang
        if (this.preconditions) {
            this.loadedPreconditions = this.preconditions
        }
        if (this.startingPoints) {
            this.loadedStartingPoints = this.startingPoints
        }
        if (this.otherStartingPoints) {
            this.loadedOtherStartingPoints = this.otherStartingPoints
        }
        if (this.defaultStartingPoint) {
            this.startingPoint = this.defaultStartingPoint
        }
        if (!this.locale) {
            this.step = 'language'
        } else {
            this.step = 'requirements'
        }
        this.installOptions = {
            localization: {
                siteLocaleLanguage: this.siteLocaleLanguage,
                siteLocaleCountry: this.siteLocaleCountry,
//                timezone: this.timezone
                timezone: 'America/Los_Angeles'
            }
        }
    }
}
</script>
