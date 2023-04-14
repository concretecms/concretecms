<template>
    <form>
        <div class="text-center">
            <img :src="logo" style="max-height: 48px" class="bg-primary rounded-circle">
        </div>
        <div>
            <h3 class="text-center mb-4 mt-3">{{  lang.stepContent }}</h3>
        </div>
        <div class="card card-default">
            <div class="card-header">{{ lang.startingPoint }}</div>
            <div id="starting-point" class="container">
                <div class="card-body">
                    <div :class="{'form-check': true, 'mb-3': i + 1 < startingPoints.length}" v-for="(startingPoint, i) in startingPoints">
                        <input type="radio" v-model="selectedStartingPoint" :value="startingPoint.handle" class="form-check-input" :id="'sp' + startingPoint.handle">
                        <div class="form-check-label">
                            <label :for="'sp' + startingPoint.handle"><b>{{ startingPoint.name }}</b></label>
                            <div class="text-muted">{{ startingPoint.description }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ccm-install-actions">
            <button class="me-auto btn btn-secondary" type="button" @click="$emit('previous')">
                {{lang.back}}
            </button>

            <button class="ms-auto btn btn-primary" type="button" @click="selectStartingPoint">
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
        selectStartingPoint() {
            this.$emit('select-starting-point', this.selectedStartingPoint)
        }
    },
    computed: {

    },
    props: {
        logo: {
            type: String,
            required: true
        },
        defaultStartingPoint: {
            type: String,
            required: false
        },
        startingPoints: {
            type: Array,
            required: true
        },
        lang: {
            type: Object,
            required: true
        }
    },
    data: () => ({
        selectedStartingPoint: ''
    }),
    mounted() {
        this.selectedStartingPoint = this.defaultStartingPoint
    }
}
</script>
