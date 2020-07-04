<template>
    <div class="row">
        <div class="col-5">
            Date
        </div>
        <div class="col-6">
            Description
        </div>
        <div class="col-1">
            <a href="javascript:void(0)" class="ccm-hover-icon">
                <svg width="20" height="20"><use xlink:href="#icon-minus-circle" /></svg>
            </a>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
export default {
    props: {
        rule: Object
    },
    data: () => ({

    }),
    methods: {},
    watch: {

    },
}
</script>

