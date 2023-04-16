<template>
    <form>
        <div class="text-center">
            <img :src="logo" style="max-height: 48px" class="bg-primary rounded-circle">
        </div>
        <div>
            <h3 class="text-center mb-4 mt-3">{{  lang.stepContent }}</h3>
        </div>

        <div>
            <h5 class="text-center mb-4 mt-2">Choose a Theme</h5>
        </div>

        <div class="row row-cols-1 row-cols-md-2 mb-3 text-center">
            <div class="col" v-for="startingPoint in featuredStartingPoints">
                <div class="card mb-4 rounded-3">
                    <img :src="startingPoint.thumbnail" class="card-img-top">
                    <div class="card-body">
                        <h1 class="card-title">{{startingPoint.name}}</h1>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li v-for="descriptionLine in startingPoint.descriptionLines">{{ descriptionLine}}</li>
                        </ul>
                        <button type="button" @click="selectedStartingPoint = startingPoint.identifier" :class="{'active': selectedStartingPoint === startingPoint.identifier, 'w-100': true, 'btn': true, 'btn-lg btn-outline-primary': true}">
                            <span v-if="selectedStartingPoint === startingPoint.identifier">
                                {{lang.selected}} <i class="fa fa-check-circle"></i>
                            </span>
                            <span v-else>
                                {{lang.select}} {{startingPoint.name}}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default">
            <div class="card-header">{{ lang.otherStartingPoints }}</div>
            <div id="starting-point" class="container">
                <div class="card-body">
                    <div :class="{'align-items-center': true, 'd-flex': true, 'mb-4': i + 1 < otherStartingPoints.length}" v-for="(startingPoint, i) in otherStartingPoints">
                        <div class="me-3">
                            <div><b>{{ startingPoint.name }}</b></div>
                            <div class="text-muted">{{ startingPoint.description }}</div>
                        </div>
                        <button type="button" @click="selectedStartingPoint = startingPoint.identifier" :class="{'col-3': true, 'active': selectedStartingPoint === startingPoint.identifier, 'ms-auto': true, 'btn': true, 'btn-outline-primary': true}">
                            <span v-if="selectedStartingPoint === startingPoint.identifier">
                                {{lang.selected}}
                            </span>
                            <span v-else>
                                {{lang.select}}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="ccm-install-actions">
            <button class="me-auto btn btn-secondary" type="button" @click="$emit('previous')">
                {{lang.back}}
            </button>

            <button class="ms-auto btn btn-primary" type="button" :disabled="!selectedStartingPoint" @click="$emit('select-starting-point', selectedStartingPoint)">
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
        startingPoint: {
            type: String,
            required: false
        },
        featuredStartingPoints: {
            type: Array,
            required: true
        },
        otherStartingPoints: {
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
        this.selectedStartingPoint = this.startingPoint
    }
}
</script>
