<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-vue-app="help">
    <concrete-help-modal :items='<?=json_encode($items, JSON_HEX_APOS)?>'>
        <template #sidebar>
            <?php View::element('help/resources')?>
        </template>
    </concrete-help-modal>
</div>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-vue-app=help]',
                components: config.components
            })
        })
    })
</script>