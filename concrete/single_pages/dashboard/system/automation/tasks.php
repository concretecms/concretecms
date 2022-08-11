<?php

defined('C5_EXECUTE') or die("Access Denied."); ?>

<div data-view="automated-tasks" v-cloak>

    <form>

        <table class="table">
            <thead>
            <tr>
                <th></th>
                <th><?= t('Name') ?></th>
                <th><?= t('Description') ?></th>
                <th><?= t('Last Started') ?></th>
                <th><?= t('Last Completed') ?></th>
                <th><?= t('Last Run By') ?></th>
            </tr>
            </thead>
            <tbody v-for="taskSet in taskSets">
            <tr>
                <th class="table-light"></th>
                <th colspan="5" class="table-light text-muted">{{taskSet.name}}</th>
            </tr>
            <tr :key="'task-' + task.id" v-for="task in taskSet.tasks">
                <td><input type="radio" :id="task.id" v-model="selectedTask" :value="task"></td>
                <td class="w-25"><label :for="task.id" class="mb-0">{{task.name}}</label></td>
                <td class="text-muted small">{{task.description}}</td>
                <td class="text-muted small">{{task.dateLastStartedFormatted}}</td>
                <td class="text-muted small">{{task.dateLastCompletedFormatted}}</td>
                <td class="text-muted small">{{task.lastRunBy ? task.lastRunBy.name : ''}}</td>
            </tr>
            </tbody>
        </table>

        <div v-if="selectedTask !== null && selectedTask.inputDefinition !== null" class="modal fade" role="dialog" tabindex="-1" id="task-options">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="mt-2 modal-title"><?= t('Task Options') ?></h4>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group" v-for="field in selectedTask.inputDefinition.fields">
                            <label class="control-label">{{field.label}}</label>
                            <select v-if="field.type === 'select'" :name="field.key" class="form-control">
                                <option value="" v-if="!field.isRequired"><?=t('** None')?></option>
                                <option v-for="(option, optionValue) in field.options" :value="optionValue">{{option}}</option>
                            </select>
                            <div v-else-if="field.type === 'boolean'">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" :name="field.key" :id="field.key + '1'" value="" checked>
                                    <label class="form-check-label" :for="field.key + '1'">
                                        <?=t('No')?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" :name="field.key" :id="field.key + '2'" value="1">
                                    <label class="form-check-label" :for="field.key + '2'">
                                        <?=t('Yes')?>
                                    </label>
                                </div>
                            </div>
                            <input v-else :name="field.key" class="form-control"/>
                            <div class="help-block" v-if="field.description">{{field.description}}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="modal-footer d-flex justify-content-between w-100">
                            <button type="button" data-bs-dismiss="modal" class="btn btn-secondary border float-start"><?php echo t('Cancel') ?></button>
                            <button type="button" @click="runTask(true)" class="btn btn-primary float-end"><?php echo t('Run Task') ?></button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div v-if="selectedTask !== null && schedulingEnabled">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mt-2"><?= t('Run Task') ?></h4>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="radio" v-model="scheduleTask" :value="false" id="runNow"
                                   class="form-check-input" checked>
                            <label class="form-check-label" for="runNow"><?= t('Now') ?></label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="cron" v-model="scheduleTask" :value="true" class="form-check-input">
                            <label class="form-check-label" for="cron"><?= t('Schedule Recurring Task') ?></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" v-if="scheduleTask">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="control-label"><?= t('Cron Expression') ?></label>
                        <input type="text" class="form-control" v-model="cronExpression" placeholder="0 8 * * *"/>
                    </div>
                    <div class="help-block"><?= t(
                            'Cron is a time-based scheduler in Unix-like operating systems. You can describe when this task will run using a short string. <a href="https://crontab.cronhub.io/" target="_blank">Generate a cron-tab online</a>.'
                        ) ?></div>
                </div>
            </div>
        </div>

        <div v-if="executedProcesses.length > 0">
            <hr class="mt-3"/>
            <h4 class="mt-3"><?= t('Executing...') ?></h4>
            <running-process-list :processes="executedProcesses"></running-process-list>
        </div>


        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button v-if="selectedTask !== null && selectedTask.inputDefinition !== null" data-bs-toggle="modal" data-bs-target="#task-options" type="button" class="btn btn-outline-primary float-end">
                    <?=t('Set Task Options')?>
                </button>
                <button @click="runTask" type="button" class="btn btn-primary float-end"
                        v-else-if="selectedTask !== null"><?= t('Run Task') ?></button>
            </div>
        </div>

    </form>

</div>

<script type="text/javascript">
    $(function () {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: 'div[data-view=automated-tasks]',
                components: config.components,
                data: {
                    selectedTask: null,
                    schedulingEnabled: <?= $schedulingEnabled ? 'true' : 'false'?>,
                    taskSets: <?=json_encode($taskSets)?>,
                    scheduleTask: false,
                    executedProcess: null,
                    executedProcesses: [],
                    cronExpression: null
                },
                watch: {
                    'executedProcess': function (value) {
                        this.executedProcesses.push(value)
                    }
                },
                methods: {
                    runTask() {
                        const my = this;
                        if (this.selectedTask) {
                            if (this.selectedTask.inputDefinition !== null) {
                                const optionsModal = document.getElementById('task-options');
                                const modal = bootstrap.Modal.getOrCreateInstance(optionsModal);
                                if (modal) {
                                    modal.hide();
                                }
                            }

                            var $form = $('div[data-view=automated-tasks] form')
                            var data = $form.serializeArray()
                            data.push({'name': 'id', 'value': my.selectedTask.id})
                            data.push({'name': 'ccm_token', 'value': '<?=$token->generate('execute')?>'})
                            data.push({'name': 'scheduleTask', 'value': my.scheduleTask ? 1 : 0})
                            data.push({'name': 'cronExpression', 'value': my.cronExpression})

                            new ConcreteAjaxRequest({
                                url: '<?=URL::to('/ccm/system/tasks/execute')?>',
                                data: data,
                                success: function (r) {
                                    if (r.status === 'task_scheduled') {
                                        window.location.href = '<?=URL::to('/dashboard/system/automation/schedule')?>'
                                    } else if (r.status === 'completed') {
                                        window.location.reload();
                                    } else if (r.status === 'process_started') {
                                        window.location.href = '<?=URL::to('/dashboard/system/automation/activity')?>'
                                    }
                                }
                            })
                        }
                    }
                }
            })
        })
    });
</script>
