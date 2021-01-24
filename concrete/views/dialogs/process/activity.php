<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div v-cloak id="ccm-dialog-process-list">

    <div v-if="runningProcesses.length">
        <div v-if="runningProcesses.length">
            <task-process-list
                    <?php if ($eventSource) { ?> event-source="<?= h($eventSource) ?>" <?php } ?>
                    :processes="runningProcesses">
            </task-process-list>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: '#ccm-dialog-process-list',
                components: config.components,
                data: {
                    'runningProcesses': <?=json_encode($runningProcesses)?>,
                }
            })
        })
    });
</script>