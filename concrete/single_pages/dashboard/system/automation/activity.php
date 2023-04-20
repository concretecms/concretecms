<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="ccm-dashboard-header-buttons">
    <button data-dialog="clear-processes" class="btn btn-secondary">
        <?=t('Clear Processes')?>
    </button>
</div>

<div v-cloak id="process-list">
    <div v-if="runningProcesses.length">
        <h3><?=t('Currently Running')?></h3>
        <div v-if="runningProcesses.length">
            <running-process-list :processes="runningProcesses"></running-process-list>
        </div>
    </div>

    <div :class="{'mt-4': runningProcesses.length > 0}">
        <h3><?=t('History')?></h3>
        <div v-if="completedProcesses.length">
            <completed-process-list :processes="completedProcesses"
                delete-action="<?=$view->action('delete', $token->generate('delete'))?>"
                details-action="<?=$view->action('details', $token->generate('details'))?>">
            </completed-process-list>
        </div>
        <div v-else>
            <p><?=t('The process history is empty.')?></p>
        </div>
    </div>

</div>

<div class="ccm-dashboard-dialog-wrapper">
    <div data-dialog-wrapper="clear-processes">
        <form method="post" action="<?= $controller->action('clear_processes') ?>">
            <?php $token->output('clear_processes') ?>
            <p><?=t('Are you sure you want to clear all processes? This will clear out all queued messages, batches, processes and process histories. This cannot be undone.') ?></p>
            <div class="dialog-buttons">
                <button class="btn btn-secondary" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                <button class="btn btn-danger" onclick="$('div[data-dialog-wrapper=clear-processes] form').submit()"><?= t('Clear') ?></button>
            </div>
        </form>
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
                        completed.sort((a, b) => (a.dateCompleted < b.dateCompleted) ? 1 : -1)
                        return completed
                    },
                    runningProcesses: function() {
                        var running = [];
                        this.processes.forEach(function(process) {
                            if (!process.dateCompleted) {
                                running.push(process)
                            }
                        })
                        running.sort((a, b) => (a.dateStarted < b.dateStarted) ? 1 : -1)
                        return running
                    }
                }
            })
        })
    });
</script>