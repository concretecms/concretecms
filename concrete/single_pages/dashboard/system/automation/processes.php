<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div v-cloak id="processes">


</div>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: '#proceses',
                components: config.components,
                data: {
                    

                },

                computed: {
                },

                watch: {
                },
                methods: {
                }
            })
        })
    });
</script>