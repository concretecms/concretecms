<template>
    <div>
        <div class="text-center">
            <img :src="logo" style="max-height: 48px" class="bg-primary rounded-circle">
        </div>
        <div>
            <h3 class="text-center mb-4 mt-3">{{ lang.stepConfirm }}</h3>
        </div>

        <div class="card">
            <div class="card-header">{{ lang.confirm.site }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-end"><b>{{ lang.site }}</b></div>
                    <div class="col-md-8">{{ installOptions.site.name }}</div>
                </div>
                <div class="row mt-3" v-if="installOptions.site.hasCanonicalUrl">
                    <div class="col-md-4 text-end"><b>{{ lang.mainCanonicalUrl }}</b></div>
                    <div class="col-md-8">{{ installOptions.site.canonicalUrl }}</div>
                </div>
                <div class="row mt-3" v-if="installOptions.site.hasAlternativeCanonicalUrl">
                    <div class="col-md-4 text-end"><b>{{ lang.alternativeCanonicalUrl }}</b></div>
                    <div class="col-md-8">{{ installOptions.site.alternativeCanonicalUrl }}</div>
                </div>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card-header">{{ lang.confirm.adminUser }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-end"><b>{{ lang.email }}</b></div>
                    <div class="col-md-8">{{ installOptions.adminUser.email }}</div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 text-end"><b>{{ lang.password }}</b></div>
                    <div class="col-md-8">
                    <span v-if="showPassword">
                        {{ installOptions.adminUser.password }}
                    </span>
                        <span v-else>
                        <code>•••••••••••••</code>
                        <a href="#" @click.prevent="showPassword = true" class="text-secondary link-primary"><i
                            class="fa fa-eye"></i></a>
                    </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card-header">{{ lang.confirm.database }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-end"><b>{{ lang.dbServer }}</b></div>
                    <div class="col-md-8">{{ installOptions.database.server }}</div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 text-end"><b>{{ lang.dbUsername }}</b></div>
                    <div class="col-md-8">{{ installOptions.database.username }}</div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 text-end"><b>{{ lang.dbPassword }}</b></div>
                    <div class="col-md-8">
                    <span v-if="showDatabasePassword">
                        {{ installOptions.database.password }}
                    </span>
                        <span v-else>
                        <code>•••••••••••••</code>
                        <a href="#" @click.prevent="showDatabasePassword = true" class="text-secondary link-primary"><i
                            class="fa fa-eye"></i></a>
                    </span>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 text-end"><b>{{ lang.dbDatabase }}</b></div>
                    <div class="col-md-8">{{ installOptions.database.database }}</div>
                </div>
            </div>
        </div>

        <div class="card mt-5" v-if="installOptions.session.handler !== ''">
            <div class="card-header">{{ lang.confirm.session }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-end"><b>{{ lang.sessionHandler }}</b></div>
                    <div class="col-md-8">{{ installOptions.session.handler }}</div>
                </div>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card-header">{{ lang.confirm.localization }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-end"><b>{{ lang.locale }}</b></div>
                    <div class="col-md-8"><code>{{ installOptions.locale }}</code></div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 text-end"><b>{{ lang.language }}</b></div>
                    <div class="col-md-8">{{ installOptions.localization.siteLocaleLanguage }}</div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 text-end"><b>{{ lang.country }}</b></div>
                    <div class="col-md-8">{{ installOptions.localization.siteLocaleCountry }}</div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 text-end"><b>{{ lang.timezone }}</b></div>
                    <div class="col-md-8">{{ installOptions.localization.timezone }}</div>
                </div>
            </div>
        </div>

        <div class="card mt-5">
            <div class="card-header">{{ lang.confirm.content }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-end"><b>{{ lang.startingPoint }}</b></div>
                    <div class="col-md-8">{{ installOptions.startingPoint }}</div>
                </div>
            </div>
        </div>


        <div class="ccm-install-actions">
            <button class="me-auto btn btn-secondary" type="button" @click="$emit('previous')">
                {{ lang.back }}
            </button>

            <button class="ms-auto btn btn-primary" type="button" @click="$emit('next')">
                {{ lang.confirm.beginInstallation }}
            </button>
        </div>
    </div>
</template>
<script>

export default {
    components: {},
    methods: {},
    computed: {},
    props: {
        logo: {
            type: String,
            required: true
        },
        lang: {
            type: Object,
            required: true
        },
        installOptions: {
            type: Object,
            required: true
        }

    },
    data: () => ({
        showPassword: false,
        showDatabasePassword: false
    }),
    mounted() {
    }
}
</script>
