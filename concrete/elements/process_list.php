<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div v-cloak id="ccm-process-list-<?=$id?>">

    <div v-if="processListProcesses.length">
        <div v-if="processListProcesses.length">
            <task-process-list
                <?php if ($poll) { ?>poll<?php } ?>
                <?php if ($eventSource) { ?> event-source="<?= h($eventSource) ?>" <?php } ?>
                poll-token="<?=$pollToken?>"
                :processes="processListProcesses">
            </task-process-list>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: '#ccm-process-list-<?=$id?>',
                components: config.components,
                data: {
                    'processListProcesses': <?=json_encode($processes)?>,
                }
            })
        })
    });
</script>