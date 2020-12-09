<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div v-cloak id="process-list">

    <div v-if="runningProcesses.length">
        <h3><?=t('Currently Running')?></h3>
    </div>

    <div class="mt-4">
        <div v-if="completedProcesses.length">
            <h3><?=t('History')?></h3>
            <div class="p-2">
                <div class="row">
                    <div class="col-md-4">
                        <h5>asdf</h5>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">alsdfkj</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">alsdfkj</div>
                    </div>
                    <div class="col-md-2 d-flex">

                    </div>
                </div>
            </div>
            <div class="card card-body p-2 mb-2" v-for="process in completedProcesses" :key="process.id">
                <a data-toggle="collapse" :data-target="'#process-' + process.id"
                   class="text-decoration-none text-dark" role="button"
                   aria-expanded="false"
                   @click="toggleProcess(process)">
                    <div class="row">
                        <div class="col-md-4">
                            <div>{{process.name}}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">alsdfkj</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">alsdfkj</div>
                        </div>
                        <div class="col-md-2 d-flex">
                            <div class="ml-auto"><Icon :icon="openProcesses.includes(process.id) ? 'chevron-down' : 'chevron-right'" type="fas" /></div>
                        </div>
                    </div>
                </a>
                <div :id="'process-' + process.id" class="collapse">
                    wat
                </div>
            </div>
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
                    'openProcesses': []
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