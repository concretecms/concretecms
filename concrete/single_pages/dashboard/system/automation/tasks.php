<?php

defined('C5_EXECUTE') or die("Access Denied."); ?>

<div data-view="automated-tasks" v-cloak>

    <h3 class="mb-3"><?= t('Run a Task') ?></h3>

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

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="btn btn-primary float-right"
                    v-if="selectedTask !== null && selectedTask.inputDefinition !== null"
                    @click="configureTask"><?= t('Configure Task') ?></button>
            <button @click="runTask" type="submit" class="btn btn-primary float-right"
                    v-if="selectedTask !== null && selectedTask.inputDefinition === null"><?= t('Run Task') ?></button>
        </div>
    </div>


    <div :key="'modal-' + task.id" class="modal fade" tabindex="-1" role="dialog" v-for="task in tasks" :id="'configure-task-' + task.id" v-if="task.inputDefinition !== null">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{task.name}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <svg><use xlink:href="#icon-dialog-close" /></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group" :key="'task-' + task.id" v-for="field in task.inputDefinition.fields">
                            <label class="control-label">{{field.label}}</label>
                            <input :name="field.key" class="form-control"></input>
                            <div class="help-block" v-if="field.description">{{field.description}}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=t('Cancel')?></button>
                        <button type="button" class="btn btn-primary" @click="runTask"><?=t('Execute')?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(function () {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: 'div[data-view=automated-tasks]',
                components: config.components,
                data: {
                    selectedTask: null,
                    tasks: <?=json_encode($tasks)?>
                },
                methods: {
                    configureTask() {
                        var $modal = $('#configure-task-' + this.selectedTask.id)
                        $modal.modal('show')
                    },
                    runTask() {
                        const my = this;
                        if (this.selectedTask) {
                            var $form = $('#configure-task-' + this.selectedTask.id + ' form')
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
                                        window.location.href = '<?=URL::to(
                                            '/dashboard/system/automation/processes'
                                        )?>/' + r.processId
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