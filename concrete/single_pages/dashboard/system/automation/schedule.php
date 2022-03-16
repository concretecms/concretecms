<?php

defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($enabled) { ?>
<div id="schedule" v-cloak>

    <div>
        <div v-if="scheduledTasks.length">
            <div class="p-2">
                <div class="row">
                    <div class="col-md-3">
                        <h5>Name</h5>
                    </div>
                    <div class="col-md-3">
                        <h5>Date Scheduled</h5>
                    </div>
                    <div class="col-md-2">
                        <h5>Expression</h5>
                    </div>
                    <div class="col-md-3">
                        <h5>Next Run Date</h5>
                    </div>
                </div>
            </div>
            <transition-group tag="div" class="process-card-wrapper" name="process-card-animation">
                <div class="card process-card"
                     v-for="scheduledTask in scheduledTasks" :key="scheduledTask.id">
                    <div class="row">
                        <div class="col-md-3">
                            <div>
                                {{scheduledTask.task.name}}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">{{scheduledTask.dateScheduledString}}</div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-muted">{{scheduledTask.cronExpression}}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">{{scheduledTask.nextRunDate}}</div>
                        </div>
                        <div class="col-md-1 d-flex">
                            <div class="ml-auto">
                                <a href="#" class="ccm-hover-icon" @click.stop="deleteScheduledTask(scheduledTask)">
                                    <icon icon="trash"></icon>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </transition-group>
            <div v-for="scheduledTask in scheduledTasks" class="modal fade" tabindex="-1" role="dialog" :id="'delete-scheduled-task-' + scheduledTask.id">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="post" @submit.prevent="deleteScheduledTaskSubmit(scheduledTask.id)">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Process</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <?=t('Delete this scheduled task?')?>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger"><?=t('Delete')?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <div v-else>
            <p><?=t('There are no tasks currently scheduled.')?></p>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(function () {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: '#schedule',
                components: config.components,
                data: {
                    'scheduledTasks': <?=json_encode($scheduledTasks)?>
                },
                methods: {
                    deleteScheduledTask(scheduledTask) {
                        var modalTarget = '#delete-scheduled-task-' + scheduledTask.id
                        $(modalTarget).modal('show')
                    },
                    deleteScheduledTaskSubmit(scheduledTaskId) {
                        var my = this
                        new ConcreteAjaxRequest({
                            url: <?=json_encode($view->action('delete', $token->generate('delete')))?>,
                            data: {
                                scheduledTaskId: scheduledTaskId,
                            },
                            success: function (r) {
                                var modalTarget = '#delete-scheduled-task-' + scheduledTaskId
                                $(modalTarget).modal('hide')
                                my.scheduledTasks.forEach(function(scheduledTask, i) {
                                    if (scheduledTask.id == scheduledTaskId) {
                                        my.scheduledTasks.splice(i, 1)
                                    }
                                })
                            }
                        })
                    }                }
            })
        })
    });
</script>
<?php } else { ?>

    <p><?=t('You must enable task schedule to use this page.')?></p>

<?php } ?>

