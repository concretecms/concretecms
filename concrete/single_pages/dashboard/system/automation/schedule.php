<?php

defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($enabled) { ?>
<div id="schedule" v-cloak>

</div>
<script type="text/javascript">
    $(function () {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: '#schedule',
                components: config.components,
                data: {
                },
                methods: {
                }
            })
        })
    });
</script>
<?php } else { ?>

    <p><?=t('You must enable task schedule to use this page.')?></p>

<?php } ?>

