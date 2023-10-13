<template>
    <form class="w-100" ref="environmentForm">
        <div class="text-center">
            <img :src="logo" style="max-height: 48px" class="bg-primary rounded-circle">
        </div>
        <div>
            <h3 class="text-center mb-4 mt-3">{{  lang.stepEnvironment }}</h3>
        </div>
        <div class="card card-default mb-5">
            <div class="card-header">{{ lang.site }}</div>
            <div id="site" class="">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{  lang.siteName }}</label>
                                <input type="text" class="form-control form-control-lg" v-model="site.name" autofocus="autofocus" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{  lang.email }}</label>
                                <input type="email" class="form-control form-control-lg" v-model="adminUser.email" required="required">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{  lang.password }}</label>
                                <input type="password" class="form-control form-control-lg" v-model="adminUser.password" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{  lang.confirmPassword }}</label>
                                <input type="password" class="form-control form-control-lg" v-model="adminUser.confirmPassword" required="required">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-default mb-5">
            <div class="card-header">{{ lang.database }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{  lang.dbServer }}</label>
                            <input type="text" class="form-control form-control-lg" v-model="database.server" required="required">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{  lang.dbUsername }}</label>
                            <input type="text" class="form-control form-control-lg" v-model="database.username">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{  lang.dbPassword }}</label>
                            <input type="password" class="form-control form-control-lg" v-model="database.password">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{  lang.dbDatabase }}</label>
                            <input type="text" class="form-control form-control-lg" v-model="database.database" required="required">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-default mb-5">
            <div class="card-header">
                {{lang.privacyPolicy}}
            </div>
            <div class="card-body">
                <p class="text-muted">{{lang.privacyPolicyExplanation}}</p>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" required v-model="site.privacyPolicy" id="privacyPolicy">
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
                                <input type="checkbox" class="form-check-input" id="canonicalUrlChecked" v-model="site.hasCanonicalUrl">
                                <label class="form-check-label" for="canonicalUrlChecked">
                                    {{lang.mainCanonicalUrl}}
                                </label>
                            </div>
                            <input v-model="site.canonicalUrl" class="form-control form-control-lg" type="url" pattern="https?:.+" :placeholder="lang.urlPlaceholder" :disabled="!site.hasCanonicalUrl">
                        </div>
                        <div class="mb-3">
                            <div class="form-label">
                                <input type="checkbox" class="form-check-input" id="alternativeCanonicalUrlChecked" v-model="site.hasAlternativeCanonicalUrl">
                                <label class="form-check-label" for="alternativeCanonicalUrlChecked">
                                    {{lang.alternativeCanonicalUrl}}
                                </label>
                            </div>
                            <input v-model="site.alternativeCanonicalUrl" class="form-control form-control-lg" type="url" pattern="https?:.+" :placeholder="lang.urlPlaceholder" :disabled="!site.hasAlternativeCanonicalUrl">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{lang.sessionHandler}}</label>
                            <select class="form-control form-control-lg" v-model="session.handler">
                                <option value="">{{lang.sessionHandlerDefault}}</option>
                                <option value="database">{{lang.sessionHandlerDatabase}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{lang.language}}</label>
                            <select v-model="localization.siteLocaleLanguage" class="form-select form-select-lg">
                                <option v-for="(language, code) in languages" :value="code">{{ language }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{lang.country}}</label>
                            <select v-model="localization.siteLocaleCountry" class="form-select form-select-lg">
                                <option v-for="(country, code) in countries" :value="code">{{ country }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{lang.timezone}}</label>
                            <select v-model="localization.timezone" class="form-select form-select-lg">
                                <option v-for="(timezone, code) in timezones" :value="code">{{ timezone }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ccm-install-actions">
            <button class="me-auto btn btn-secondary" type="button" @click="$emit('previous')">
                {{lang.back}}
            </button>

            <button class="ms-auto btn btn-primary" type="button" @click="next">
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
                this.$emit('update-install-options', {
                    site: this.site,
                    adminUser: this.adminUser,
                    database: this.database,
                    session: this.session,
                    localization: this.localization
                })
                this.$emit('next')
            } else {
                this.$refs.environmentForm.reportValidity()
            }
        }
    },
    computed: {
    },
    props: {
        logo: {
            type: String,
            required: true
        },
        timezones: {
            type: Object,
            required: true
        },
        installOptions: {
            type: Object,
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
        site: {
            name: 'test',
            privacyPolicy: 1,
            hasCanonicalUrl: 0,
            canonicalUrl: '',
            hasAlternativeCanonicalUrl: 0,
            alternativeCanonicalUrl: ''
        },
        adminUser: {
            email: 'andrew@concrete5.org',
            password: 'password',
            confirmPassword: 'password'
        },
        database: {
            server: 'localhost',
            username: 'root',
            password: '',
            database: 'concrete'
        },
        session: {
            handler: ''
        },
        localization: {
            siteLocaleLanguage: '',
            siteLocaleCountry: '',
            timezone: ''
        }
    }),
    mounted() {
        if (this.installOptions.site) {
            this.site = this.installOptions.site
        }
        if (this.installOptions.adminUser) {
            this.adminUser = this.installOptions.adminUser
        }
        if (this.installOptions.database) {
            this.database = this.installOptions.database
        }
        if (this.installOptions.session) {
            this.session = this.installOptions.session
        }
        if (this.installOptions.localization) {
            this.localization = this.installOptions.localization
        }
    }
}
</script>
