<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div v-cloak id="process-list">

    <div v-if="runningProcesses.length">
        <h3><?=t('Currently Running')?></h3>
        <div v-if="runningProcesses.length">
            <task-process-list :processes="runningProcesses"
                               current-process-id="<?=$processID?>"
                               details-action="<?=$view->action('details', $token->generate('details'))?>">

            </task-process-list>
        </div>
    </div>

    <div class="mt-4">
        <div v-if="completedProcesses.length">
            <h3><?=t('History')?></h3>
            <task-process-list :processes="completedProcesses"
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

                mounted() {
                    <?php if ($eventSource) { ?>
                        const my = this
                        const messageUrl = new URL('<?=$eventSource?>')
                        messageUrl.searchParams.append('topic', 'https://global.concretecms.com/task/processes/{processId}')
                        const messageEventSource = new EventSource(messageUrl)
                        messageEventSource.onmessage = event => {
                            var data = JSON.parse(event.data)
                            if (data.processId) {
                                my.processes.forEach(function(process) {
                                    if (process.id === data.processId) {
                                        process.details.push(data.message)
                                    }
                                })
                            }
                        }

                        const closeUrl = new URL('<?=$eventSource?>')
                        closeUrl.searchParams.append('topic', 'https://global.concretecms.com/task/close-process/{processId}')
                        const closeEventSource = new EventSource(closeUrl)
                        closeEventSource.onmessage = event => {
                            var data = JSON.parse(event.data)
                            my.processes.forEach(function(process) {
                                if (process.id == data.process.id) {
                                    process.dateCompleted = data.process.dateCompleted
                                }
                            })
                        }

                        const progressUrl = new URL('<?=$eventSource?>')
                        progressUrl.searchParams.append('topic', 'https://global.concretecms.com/batches/{batchId}')
                        const progressEventSource = new EventSource(progressUrl)
                        progressEventSource.onmessage = event => {
                            const data = JSON.parse(event.data)
                            const total = data.batch.totalJobs
                            const progress = total - data.batch.pendingJobs
                            const percent = Math.round(progress / total * 100)

                            my.processes.forEach(function(process) {
                                if (process.batch && process.batch.id == data.batch.id) {
                                    process.progress = percent
                                }
                            })
                        }

                <?php } ?>
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