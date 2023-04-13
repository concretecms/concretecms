<template>
    <form class="w-100">
        <div class="card mb-3" v-if="requiredPreconditionsLeft.length">
            <div class="card-header">{{ i18n.requiredPreconditions }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <preconditions-list @precondition-failed="preconditionFailed" :preconditions="requiredPreconditionsLeft" />
                    </div>
                    <div class="col-md-6">
                        <preconditions-list @precondition-failed="preconditionFailed" :preconditions="requiredPreconditionsRight" />
                    </div>
                </div>
            </div>
        </div>
        <div class="card" v-if="optionalPreconditionsLeft.length">
            <div class="card-header">{{ i18n.optionalPreconditions }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <preconditions-list @precondition-failed="preconditionFailed" :preconditions="optionalPreconditionsLeft" />
                    </div>
                    <div class="col-md-6">
                        <preconditions-list @precondition-failed="preconditionFailed" :preconditions="optionalPreconditionsRight" />
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-danger mt-3" v-if="showInstallErrors">
            {{i18n.installErrors}}
            <span v-html="i18n.installErrorsTrouble"></span>
        </div>

        <div v-if="showInstallErrors" class="mt-3 text-center">
            <button class="btn btn-danger btn-lg" type="button" @click="reloadPreconditions">
                {{i18n.runTestsAgain}}
            </button>
        </div>
        <div v-else class="mt-3">
            <button class="float-start btn btn-secondary btn-lg" type="button" @click="$emit('previous')">
                {{i18n.back}}
            </button>

            <button class="float-end btn btn-primary btn-lg" type="button" @click="$emit('next')">
                {{i18n.next}}
            </button>
        </div>

    </form>
</template>
<script>
import PreconditionsList from "./PreconditionsList";
export default {
    components: {
        PreconditionsList
    },
    methods: {
        reloadPreconditions() {
            window.location.href = this.reloadPreconditionsUrl + '/' + this.locale
        },
        returnSortedPreconditions(column, required) {
            let preconditions = []
            let num = 0
            this.preconditions.forEach((executedPrecondition) => {
                if ((!required && executedPrecondition.precondition.is_optional) || (required && !executedPrecondition.precondition.is_optional)) {
                    preconditions.push(executedPrecondition)
                    num++
                }
            })

            if (num > 0) {
                var segmentedPreconditions = []
                preconditions.forEach((executedPrecondition, i) => {
                    if (column === 'left' && (i % 2 === 0) || (column === 'right' && (i % 2) === 1)) {
                        segmentedPreconditions.push(executedPrecondition)
                    }
                })
                return segmentedPreconditions
            }
            return []
        },
        preconditionFailed() {
            this.showInstallErrors = true
        }
    },
    computed: {
        requiredPreconditionsLeft() {
            return this.returnSortedPreconditions('left', true)
        },
        requiredPreconditionsRight() {
            return this.returnSortedPreconditions('right', true)
        },
        optionalPreconditionsLeft() {
            return this.returnSortedPreconditions('left')
        },
        optionalPreconditionsRight() {
            return this.returnSortedPreconditions('right')
        },
    },
    props: {
        reloadPreconditionsUrl: {
            type: String,
            required: true
        },
        locale: {
            type: String,
            required: true
        },
        lang: {
            type: Object,
            required: true
        },
        preconditions: {
            type: Array,
            required: true
        }
    },
    data: () => ({
        i18n: {},
        showInstallErrors: false,
    }),
    mounted() {
        this.i18n = this.lang
    }
}
</script>
