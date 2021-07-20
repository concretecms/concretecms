<template>
    <div class="">
        <input :id="'color-picker-' + styleValue.style.variable"/>
    </div>
</template>

<script>

export default {
    data() {
        return {
            color: this.styleValue.value
        }
    },
    mounted() {
        var my = this
        $('#color-picker-' + this.styleValue.style.variable).spectrum({
            showAlpha: true,
            preferredFormat: 'rgb',
            allowEmpty: true,
            color: this.color,
            change: function (r) {
                var color = r.toRgb()
                my.$emit('update', {
                    variable: my.styleValue.style.variable,
                    value: {
                        r: color.r,
                        g: color.g,
                        b: color.b,
                        a: color.a
                    }
                })
            }
        })
    },
    props: {
        styleValue: {
            type: Object
        }
    }
}
</script>
