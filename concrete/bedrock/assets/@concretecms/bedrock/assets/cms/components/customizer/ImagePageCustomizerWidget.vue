<template>
    <flyout-menu :label="styleValue.style.name">
        <template v-slot:icon>
            <i :class="{'fas': true, 'fa-image': true, 'text-black-50': !imageURL && !imageFileID, 'text-primary': imageFileID > 0}"></i>
        </template>
        <template v-slot:content>
            <concrete-file-input :file-id="imageFileID" @change="value => imageFileID = value" choose-text="Choose Image"
                                 input-name="imageFileID"></concrete-file-input>
            <div class="mt-2" v-if="imageURL">
                <small class="text-muted">Currently using {{ imageURL }}</small>
            </div>
        </template>
    </flyout-menu>
</template>

<script>
import ConcreteFileInput from '../../components/form/ConcreteFileInput'
import FlyoutMenu from './flyout/FlyoutMenu'

export default {
    components: {
        ConcreteFileInput,
        FlyoutMenu
    },
    watch: {
        imageFileID: function() {
            this.componentUpdated()
        }
    },
    data() {
        return {
            imageURL: this.styleValue.value.imageURL, // this is the default one passed in,
            imageFileID: this.styleValue.value.imageFileID
        }
    },
    methods: {
        componentUpdated: function () {
            this.$emit('update', {
                variable: this.styleValue.style.variable,
                value: {
                    imageURL: this.imageURL,
                    imageFileID: this.imageFileID
                }
            })
        }
    },
    mounted() {

    },
    props: {
        styleValue: {
            type: Object
        }
    }
}
</script>
