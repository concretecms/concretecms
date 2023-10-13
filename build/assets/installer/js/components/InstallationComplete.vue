<template>
<div>
    <div class="text-center">
        <img :src="logo" style="max-height: 48px" class="bg-primary rounded-circle">
    </div>
    <div>
        <h3 class="text-center mb-4 mt-3">{{  lang.stepInstallationComplete }}</h3>
    </div>
    <div class="row">
        <div class="col-md-6 offset-md-3 text-center">
            <div class="mb-4 text-start"><span v-html="lang.installationCompleteMessage"></span></div>
            <a :href="installationCompleteUrl" class="btn btn-lg btn-primary">{{ lang.editYourSite }}</a>
        </div>
    </div>
</div>
</template>
<script>

export default {
    components: {},
    methods: {
    },
    computed: {},
    props: {
        installationCompleteUrl: {
            type: String,
            required: true
        },
        logo: {
            type: String,
            required: true
        },
        lang: {
            type: Object,
            required: true
        }
    },
    data: () => ({
    }),
    mounted() {
    }
}
</script>
