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
                                <a href="#" data-bs-toggle="modal" data-bs-target="#scheduled-task-details"
                                   title="<?=h(t('View Task Options'))?>"
                                   @click.prevent="selectedScheduledTask = scheduledTask">
                                    {{scheduledTask.task.name}}
                                </a>
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
            <div class="modal fade" tabindex="-1" role="dialog" id="scheduled-task-details" aria-labelledby="scheduled-task-details-title">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="scheduled-task-details-title"><?=t('Task Options')?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?=h(t('Close'))?>"></button>
                        </div>
                        <div class="modal-body" v-if="selectedScheduledTask">
                            <h6>{{selectedScheduledTask.task.name}}</h6>
                            <dl v-if="selectedTaskOptions.length" class="mb-0">
                                <template v-for="option in selectedTaskOptions">
                                    <dt :key="option.key + '-label'" class="text-break">{{option.label}}</dt>
                                    <dd :key="option.key + '-value'" class="text-break">{{option.value}}</dd>
                                </template>
                            </dl>
                            <p v-else class="mb-0"><?=t('This task has no options.')?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=t('Close')?></button>
                        </div>
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
                    selectedScheduledTask: null
                },
                computed: {
                    selectedTaskOptions() {
                        if (!this.selectedScheduledTask) {
                            return []
                        }
                        const scheduledTask = this.selectedScheduledTask
                        const inputFields = scheduledTask.input && scheduledTask.input.fields || []
                        const definition = scheduledTask.task.inputDefinition
                        const fields = definition ? definition.fields.slice() : []
                        inputFields.forEach(inputField => {
                            if (!fields.some(field => field.key === inputField.key)) {
                                fields.push({key: inputField.key, label: inputField.key})
                            }
                        })
                        return fields.map(field => {
                            const inputField = inputFields.find(inputField => inputField.key === field.key)
                            let value = inputField ? inputField.value : ''
                            if (field.type === 'boolean') {
                                value = value === '1' || value === 1 || value === true ? <?=json_encode(t('Yes'))?> : <?=json_encode(t('No'))?>

                            } else if (field.type === 'select' && field.options && Object.prototype.hasOwnProperty.call(field.options, value)) {
                                value = field.options[value]
                            } else if (value === '' || value === null || typeof value === 'undefined') {
                                value = <?=json_encode(t('Not set'))?>

                            }
                            return {key: field.key, label: field.label, value: value}
                        })
                    }
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

