<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/instances/menu', ['instance' => $instance, 'action' => 'rules']);
        $element->render();
        ?>
    </div>
    <div class="col-8 pt-5" data-view="instance-rules">

        <board-instance-rule-list :rules="rules"></board-instance-rule-list>

    </div>
</div>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: 'div[data-view=instance-rules]',
                components: config.components,
                data: {
                    rules: <?=json_encode($rules)?>
                },
            })
        })
    });
</script>