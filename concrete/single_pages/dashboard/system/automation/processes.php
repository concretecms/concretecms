<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div v-cloak id="process-list">

    <div v-if="runningProcesses.length">
        <h3><?=t('Currently Running')?></h3>
        <div v-if="runningProcesses.length">
            <task-process-list :processes="runningProcesses"></task-process-list>
        </div>
    </div>

    <div class="mt-4">
        <div v-if="completedProcesses.length">
            <h3><?=t('History')?></h3>
            <task-process-list :processes="completedProcesses"
                               delete-action="<?=$view->action('delete', $token->generate('delete'))?>">

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
                },

                watch: {
                },
                methods: {
                    deleteProcess(process) {
                        var modalTarget = '#delete-process-' + process.id
                        $(modalTarget).modal('show')
                    },
                    toggleProcess(process) {
                        if (!this.openProcesses.includes(process.id)) {
                            this.openProcesses.push(process.id)
                        } else {
                            this.openProcesses.splice(this.openProcesses.indexOf(process.id), 1)
                        }
                    }
                }
            })
        })
    });
</script>