<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div v-cloak id="process-list">

    <div v-if="runningProcesses.length">
        <h3><?=t('Currently Running')?></h3>
        <div v-if="runningProcesses.length">
            <task-process-list :processes="runningProcesses"
                               <?php if ($eventSource) { ?>event-source="<?=$eventSource?>"<?php } ?>
                               current-process-id="<?=$processID?>"
                               details-action="<?=$view->action('details', $token->generate('details'))?>">

            </task-process-list>
        </div>
    </div>

    <div class="mt-4">
        <div v-if="completedProcesses.length">
            <h3><?=t('History')?></h3>
            <task-process-list :processes="completedProcesses"
                               <?php if ($eventSource) { ?>event-source="<?=$eventSource?>"<?php } ?>
                               current-process-id="<?=$processID?>"
                               delete-action="<?=$view->action('delete', $token->generate('delete'))?>"
                               details-action="<?=$view->action('details', $token->generate('details'))?>">

            </task-process-list>
        </div>
        <div v-else>
            <p><?=t('The process history is empty.')?></p>
        </div>
    </div>

</div>


<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: '#process-list',
                components: config.components,
                data: {
                    'processes': <?=json_encode($processes)?>,
                },

                computed: {
                    completedProcesses: function() {
                        var completed = [];
                        this.processes.forEach(function(process) {
                            if (process.dateCompleted) {
                                completed.push(process)
                            }
                        })
                        return completed
                    },
                    runningProcesses: function() {
                        var running = [];
                        this.processes.forEach(function(process) {
                            if (!process.dateCompleted) {
                                running.push(process)
                            }
                        })
                        return running
                    }
                }
            })
        })
    });
</script>