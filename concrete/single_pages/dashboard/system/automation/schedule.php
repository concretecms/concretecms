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
                            <div v-if="scheduledTask.notes" class="text-muted small mt-1 scheduled-task-notes">{{scheduledTask.notes}}</div>
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
                                <a href="#" class="ccm-hover-icon me-2" title="<?=h(t('Edit'))?>" @click.stop.prevent="editScheduledTask(scheduledTask)">
                                    <icon icon="pencil-alt"></icon>
                                </a>
                                <a href="#" class="ccm-hover-icon" @click.stop="deleteScheduledTask(scheduledTask)">
                                    <icon icon="trash"></icon>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </transition-group>
            <div class="modal fade" tabindex="-1" role="dialog" id="edit-scheduled-task">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="post" @submit.prevent="editScheduledTaskSubmit">
                            <div class="modal-header">
                                <h5 class="modal-title"><?=t('Edit Scheduled Task')?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="control-label" for="edit-scheduled-task-cron"><?=t('Cron Expression')?></label>
                                    <input type="text" id="edit-scheduled-task-cron" class="form-control" v-model="editingCronExpression" placeholder="0 8 * * *">
                                    <div class="help-block"><?=t('Cron is a time-based scheduler. You can describe when this task will run using a short string. <a href="https://crontab.cronhub.io/" target="_blank">Generate a cron-tab online</a>.')?></div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="edit-scheduled-task-notes-input"><?=t('Notes')?></label>
                                    <textarea id="edit-scheduled-task-notes-input" class="form-control" rows="3" v-model="editingNotes"></textarea>
                                    <div class="help-block"><?=t('Optional. Notes help you identify this scheduled task later on.')?></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=t('Cancel')?></button>
                                <button type="submit" class="btn btn-primary"><?=t('Save')?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                    'scheduledTasks': <?=json_encode($scheduledTasks)?>,
                    'editingScheduledTaskId': null,
                    'editingCronExpression': '',
                    'editingNotes': ''
                },
                methods: {
                    editScheduledTask(scheduledTask) {
                        this.editingScheduledTaskId = scheduledTask.id
                        this.editingCronExpression = scheduledTask.cronExpression
                        this.editingNotes = scheduledTask.notes || ''
                        $('#edit-scheduled-task').modal('show')
                    },
                    editScheduledTaskSubmit() {
                        var my = this
                        var scheduledTaskId = my.editingScheduledTaskId
                        new ConcreteAjaxRequest({
                            url: <?=json_encode($view->action('update', $token->generate('update')))?>,
                            data: {
                                scheduledTaskId: scheduledTaskId,
                                cronExpression: my.editingCronExpression,
                                notes: my.editingNotes
                            },
                            success: function (r) {
                                $('#edit-scheduled-task').modal('hide')
                                my.scheduledTasks.forEach(function(scheduledTask) {
                                    if (scheduledTask.id == scheduledTaskId) {
                                        scheduledTask.cronExpression = r.cronExpression
                                        scheduledTask.nextRunDate = r.nextRunDate
                                        scheduledTask.notes = r.notes
                                    }
                                })
                            }
                        })
                    },
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

