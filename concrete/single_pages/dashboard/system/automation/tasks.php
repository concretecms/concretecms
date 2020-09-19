<?php
defined('C5_EXECUTE') or die("Access Denied."); ?>

<div data-view="automated-tasks" v-cloak>

    <h3 class="mb-3"><?=t('Run a Task')?></h3>

    <table class="table table-striped" id="ccm-jobs-list">
        <thead>
        <tr>
            <th></th>
            <th><?= t('Name') ?></th>
            <th><?= t('Description') ?></th>
        </tr>
        </thead>
        <tbody>
        <tr :key="task.id" v-for="task in tasks">
            <td><input type="radio" :id="task.id" v-model="selectedTask" :value="task"></td>
            <td><b><label :for="task.id" class="mb-0">{{task.name}}</label></b></td>
            <td>{{task.description}}</td>
        </tr>
        </tbody>
    </table>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button @click="runTask" type="submit" class="btn btn-primary float-right" :disabled="selectedTask === null"><?=t('Run Task')?></button>
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

                computed: {},

                watch: {},
                methods: {
                    runTask() {
                        const my = this;
                        if (this.selectedTask) {
                            new ConcreteAjaxRequest({
                                url: '<?=URL::to('/ccm/system/tasks/execute')?>',
                                data: {
                                    'id': my.selectedTask.id,
                                    'ccm_token': '<?=$token->generate('execute')?>'
                                },
                                success: function(r) {
                                    if (r.status === 'completed') {
                                        window.location.reload();
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