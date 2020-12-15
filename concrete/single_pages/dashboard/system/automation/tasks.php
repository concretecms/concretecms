<?php

defined('C5_EXECUTE') or die("Access Denied."); ?>

<div data-view="automated-tasks" v-cloak>

    <form>

    <h3 class="mb-3"><?= t('Choose a Task to Run') ?></h3>

    <table class="table table-striped" id="ccm-jobs-list">
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
        <tbody>
        <tr :key="'task-' + task.id" v-for="task in tasks">
            <td><input type="radio" :id="task.id" v-model="selectedTask" :value="task"></td>
            <td><b><label :for="task.id" class="mb-0">{{task.name}}</label></b></td>
            <td class="small">{{task.description}}</td>
            <td>{{task.dateLastStartedFormatted}}</td>
            <td>{{task.dateLastCompletedFormatted}}</td>
            <td>{{task.lastRunBy ? task.lastRunBy.name : ''}}</td>
        </tr>
        </tbody>
    </table>

    <div v-if="selectedTask !== null && selectedTask.inputDefinition !== null">
        <div class="row">
            <div class="col-md-8">
                <h4 class="mt-2"><?=t('Task Options')?></h4>
                <div class="form-group" v-for="field in selectedTask.inputDefinition.fields">
                    <label class="control-label">{{field.label}}</label>
                    <input :name="field.key" class="form-control"></input>
                    <div class="help-block" v-if="field.description">{{field.description}}</div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="selectedTask !== null && schedulingEnabled">
        <div class="row">
            <div class="col-md-8">
                <h4 class="mt-2"><?=t('Run Task')?></h4>
                <div class="form-check">
                    <input type="radio" v-model="scheduleTask" :value="false" id="runNow" class="form-check-input" checked>
                    <label class="form-check-label" for="runNow"><?=t('Now')?></label>
                </div>
                <div class="form-check">
                    <input type="radio" id="cron" v-model="scheduleTask" :value="true" class="form-check-input">
                    <label class="form-check-label" for="cron"><?=t('Schedule Recurring Task')?></label>
                </div>
            </div>
        </div>
    </div>

    <div v-if="executedProcesses.length > 0">
        <hr class="mt-3" />
        <h4 class="mt-3"><?=t('Executing...')?></h4>
        <task-process-list :processes="executedProcesses"
                       <?php if ($eventSource) { ?> event-source="<?=h($eventSource)?>" <?php } ?>
                       :current-process-id="executedProcess.id"
                       details-action="<?=URL::to('/dashboard/system/automation/activity', 'details', $token->generate('details'))?>">

        </task-process-list>
    </div>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button @click="runTask" type="button" class="btn btn-primary float-right"
                    v-if="selectedTask !== null"><?= t('Run Task') ?></button>
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
                    tasks: <?=json_encode($tasks)?>,
                    scheduleTask: false,
                    executedProcess: null,
                    executedProcesses: []
                },
                watch: {
                    'executedProcess': function(value) {
                        this.executedProcesses.push(value)
                    }
                },
                methods: {
                    runTask() {
                        const my = this;
                        if (this.selectedTask) {
                            var $form = $('div[data-view=automated-tasks] form')
                            var data = $form.serializeArray()
                            data.push({'name': 'id', 'value': my.selectedTask.id})
                            data.push({'name': 'ccm_token', 'value': '<?=$token->generate('execute')?>'})

                            new ConcreteAjaxRequest({
                                url: '<?=URL::to('/ccm/system/tasks/execute')?>',
                                data: data,
                                success: function (r) {
                                    if (r.status === 'completed') {
                                        window.location.reload();
                                    } else if (r.status === 'process_started') {
                                        my.executedProcess = r.process
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