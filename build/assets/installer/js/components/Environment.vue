<template>
    <form class="w-100" ref="environmentForm">
        <div class="card card-default mb-4">
            <div class="card-header">{{ lang.site }}</div>
            <div id="site" class="">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{  lang.siteName }}</label>
                                <input type="text" class="form-control form-control-lg" v-model="environment.siteName" autofocus="autofocus" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{  lang.email }}</label>
                                <input type="email" class="form-control form-control-lg" v-model="environment.email" required="required">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{  lang.password }}</label>
                                <input type="password" class="form-control form-control-lg" v-model="environment.password" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{  lang.confirmPassword }}</label>
                                <input type="password" class="form-control form-control-lg" v-model="environment.confirmPassword" required="required">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-default mb-4">
            <div class="card-header">{{ lang.database }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{  lang.dbServer }}</label>
                            <input type="text" class="form-control form-control-lg" v-model="environment.dbServer" required="required">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{  lang.dbUsername }}</label>
                            <input type="text" class="form-control form-control-lg" v-model="environment.dbUsername">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{  lang.dbPassword }}</label>
                            <input type="password" class="form-control form-control-lg" v-model="environment.dbPassword">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{  lang.dbDatabase }}</label>
                            <input type="text" class="form-control form-control-lg" v-model="environment.dbDatabase" required="required">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-default mb-4">
            <div class="card-header">
                {{lang.privacyPolicy}}
            </div>
            <div class="card-body">
                <p class="text-muted">{{lang.privacyPolicyExplanation}}</p>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" required v-model="environment.privacyPolicy" id="privacyPolicy">
                    <label class="form-check-label" for="privacyPolicy"><span v-html="lang.privacyPolicyLabel"></span></label>
                </div>
            </div>
        </div>
        <div class="card card-default">
            <div class="card-header">{{ lang.advancedOptions }}</div>
            <div class="card-body container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-label">
                                <input type="checkbox" class="form-check-input" id="canonicalUrlChecked" v-model="environment.hasCanonicalUrl">
                                <label class="form-check-label" for="canonicalUrlChecked">
                                    {{lang.mainCanonicalUrl}}
                                </label>
                            </div>
                            <input v-model="environment.canonicalUrl" class="form-control form-control-lg" type="url" pattern="https?:.+" :placeholder="lang.urlPlaceholder" :disabled="!environment.hasCanonicalUrl">
                        </div>
                        <div class="mb-3">
                            <div class="form-label">
                                <input type="checkbox" class="form-check-input" id="alternativeCanonicalUrlChecked" v-model="environment.hasAlternativeCanonicalUrl">
                                <label class="form-check-label" for="alternativeCanonicalUrlChecked">
                                    {{lang.alternativeCanonicalUrl}}
                                </label>
                            </div>
                            <input v-model="environment.alternativeCanonicalUrl" class="form-control form-control-lg" type="url" pattern="https?:.+" :placeholder="lang.urlPlaceholder" :disabled="!environment.hasAlternativeCanonicalUrl">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{lang.sessionHandler}}</label>
                            <select class="form-control form-control-lg" v-model="environment.sessionHandler">
                                <option value="">{{lang.sessionHandlerDefault}}</option>
                                <option value="database">{{lang.sessionHandlerDatabase}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{lang.language}}</label>
                            <select v-model="environment.siteLocaleLanguage" class="form-select form-select-lg">
                                <option v-for="(language, code) in languages" :value="code">{{ language }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{lang.country}}</label>
                            <select v-model="environment.siteLocaleCountry" class="form-select form-select-lg">
                                <option v-for="(country, code) in countries" :value="code">{{ country }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{lang.timezone}}</label>
                            <select v-model="environment.timezone" class="form-select form-select-lg">
                                <option v-for="(timezone, code) in timezones" :value="code">{{ timezone }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button class="float-start btn btn-secondary btn-lg" type="button" @click="$emit('previous')">
                {{lang.back}}
            </button>

            <button class="float-end btn btn-primary btn-lg" type="button" @click="next">
                {{lang.next}}
            </button>
        </div>

    </form>
</template>
<script>

export default {
    components: {
    },
    methods: {
        next() {
            if (this.$refs.environmentForm.checkValidity()) {
                this.$emit('next', this.environment)
            } else {
                this.$refs.environmentForm.reportValidity()
            }
        }
    },
    computed: {
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
        countries: {
            type: Object,
            required: true
        },
        lang: {
            type: Object,
            required: true
        }
    },
    data: () => ({
        environment: {
            siteName: '',
            email: '',
            password: '',
            confirmPassword: '',
            dbServer: '',
            dbUsername: '',
            dbPassword: '',
            dbDatabase: '',
            privacyPolicy: 0,
            hasCanonicalUrl: 0,
            canonicalUrl: '',
            hasAlternativeCanonicalUrl: 0,
            alternativeCanonicalUrl: '',
            sessionHandler: '',
            siteLocaleLanguage: '',
            siteLocaleCountry: '',
            timezone: ''
        }
    }),
    mounted() {
        this.environment.siteLocaleLanguage = this.siteLocaleLanguage
        this.environment.siteLocaleCountry = this.siteLocaleCountry
        this.environment.timezone = this.timezone
    }
}
</script>
