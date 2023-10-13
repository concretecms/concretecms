<template>
    <div>
        <div class="text-center position-relative">
            <div class="position-absolute w-100" style="top: -8px">
                <div class="spinner-border text-primary" style="width: 64px; height: 64px;" role="status">
                </div>
            </div>
            <img :src="logo" style="max-height: 48px" class="bg-primary rounded-circle">
        </div>
        <div>
            <h3 class="text-center mb-4 mt-3">{{  lang.stepPerformInstallation }}</h3>
        </div>
        <div id="interstitial-message">
            <div class="mb-3" v-if="installError || currentProgress">
                <div class="alert alert-danger" v-if="installError">
                    <span v-html="installError"></span>
                </div>
                <div v-else>
                    <div class="lead text-center">
                        {{currentProgress}}
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">{{ lang.interstitial.whileYouWait }}</div>
                <div class="card-body">
                    <h4 class="">{{ lang.interstitial.forums }}</h4>
                    <p>
                        <span v-html="lang.interstitial.forumsMessage"></span>
                    </p>

                    <h4 class="">{{ lang.interstitial.userDocumentation }}</h4>
                    <p>
                        <span v-html="lang.interstitial.userDocumentationMessage"></span>
                    </p>

                    <h4 class="">{{ lang.interstitial.screencasts }}</h4>
                    <p>
                        <span v-html="lang.interstitial.screencastsMessage"></span>
                    </p>

                    <h4 class="">{{ lang.interstitial.developerDocumentation }}</h4>
                    <p>
                        <span v-html="lang.interstitial.developerDocumentationMessage"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    components: {},
    methods: {
        initProgressBar() {
            NProgress.configure({showSpinner: false});
        }
    },
    computed: {},
    props: {
        logo: {
            type: String,
            required: true
        },
        startingPointRoutineUrl: {
            type: String,
            required: true
        },
        beginInstallationUrl: {
            type: String,
            required: true
        },
        installOptions: {
            type: Object,
            required: true
        },
        lang: {
            type: Object,
            required: true
        }
    },
    data: () => ({
        routines: null,
        currentProgress: null,
        currentRoutine: null,
        installError: null
    }),
    watch: {
        currentRoutine: function(routineIndex) {
            var my = this
            if (this.routines.length > routineIndex) {
                var startingPoint = this.installOptions.startingPoint
                var routine = this.routines[routineIndex]
                var url = this.startingPointRoutineUrl + '/' + startingPoint
                my.currentProgress = routine.text

                $.ajax({
                    cache: false,
                    dataType: 'json',
                    method: 'post',
                    data: {
                        routine: routine,
                        options: this.installOptions,
                    },
                    url: url,
                    success(r) {
                        if (r.error) {
                            my.installError = r.message
                        } else {
                            my.currentRoutine++
                            NProgress.set(my.currentRoutine / my.routines.length)
                        }
                    }
                })
            } else {
                NProgress.done();
                my.currentProgress = my.lang.installationComplete
                my.$emit('installation-complete')
            }
        }
    },
    mounted() {
        this.initProgressBar()
        var my = this
        my.currentProgress = this.lang.loadingInstallationRoutines
        $.ajax({
            cache: false,
            dataType: 'json',
            method: 'post',
            data: this.installOptions,
            url: my.beginInstallationUrl,
            success(r) {
                my.routines = r
                my.currentRoutine = 0
            }
        })
    }
}
</script>
