<template>
    <div class="mb-3">
        <select :name="inputName" data-select="theme-colors" data-width="100%">
            <option v-for="color in colorCollection.colors" :selected="selectedColor == color.variable" :data-content="dataContentAttribute(color)" :value="color.variable">{{ color.name }}</option>
        </select>
    </div>
</template>

<script>
export default {
    data() {
        return {
            selectedColor: ''
        }
    },
    props: {
        colorCollection: {
            type: Object,
            required: true
        },
        inputName: {
            type: String,
            required: true,
        },
        color: {
            type: String,
            required: false,
        },
    },
    watch: {
        selectedColor: {
            handler(value) {
                this.$emit('change', value)
            }
        }
    },
    mounted() {
        if (this.color) {
            this.selectedColor = this.color
        }

        var $el = this.$el
        setTimeout(function() {
            $($el.querySelector('select[data-select=theme-colors]')).selectpicker()
        }, 5)
    },
    methods: {
        dataContentAttribute: function(color) {
            return "<span style='background-color: var(--bs-" + color.variable + ")' class='btn me-2 p-2'></span> " + color.name
        }
    }
}
</script>
