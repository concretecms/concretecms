<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div v-cloak id="ccm-process-list" class="h-100">
    <?php if ($showManageActivityButton) { ?>
        <a class="float-end btn btn-secondary btn-sm" id="button-manage-activity" title="<?=t('View historical processes, process logs (if enabled) and remove stuck processes.')?>"
           href="<?=URL::to('/dashboard/system/automation/activity')?>"><?=t('Manage Processes')?> <i class="fa fa-share"></i>
        </a>
    <?php } ?>
    <div v-if="processListProcesses.length">
        <p class="lead"><?=t('Active Processes')?></p>
        <div v-if="processListProcesses.length">
            <running-process-list :processes="processListProcesses" @complete-process="completeProcess"></running-process-list>
        </div>
    </div>
    <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center">
        <div class="alert alert-info text-center"><span class="lead"><?=t('There are no currently running processes.')?></span></div>
    </div>

</div>


<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: '#ccm-process-list',
                components: config.components,
                methods: {
                    completeProcess(process) {
                        var my = this
                        this.processListProcesses.forEach(function(runningProcess, i) {
                            if (runningProcess.id == process.id) {
                                my.processListProcesses.splice(i, 1)
                            }
                        })
                    }
                },
                data: {
                    'processListProcesses': <?=json_encode($processes)?>,
                },
                mounted() {
                    const tooltip = this.$el.querySelector('#button-manage-activity')
                    new bootstrap.Tooltip(tooltip, { container: '#ccm-tooltip-holder' })
                }
            })
        })
    });
</script>
