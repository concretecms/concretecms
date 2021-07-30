<template>
    <div>
        <div :class="{'card': true, 'card-body': true, 'process-card': true}"
        v-for="process in processes" :key="process.id">
            <div class="row">
                <div class="col-md-9 running-process-name">
                    <b>{{process.name}}</b>
                    <span v-if="!configurationLoaded">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </span>
                </div>
                <div class="col-md-3 text-black-50 text-right" v-if="process.batch !== null">
                    {{process.batch.totalJobs - process.batch.pendingJobs}} / {{process.batch.totalJobs}}
                </div>
            </div>
            <div v-if="configurationLoaded" class="row process-progress-wrapper" v-show="process.batch !== null">
                <div class="col-md-12">
                    <div class="mt-2 progress process-progress" >
                        <div class="progress-bar" role="progressbar" :style="'width: ' + process.progress + '%'"></div>
                    </div>
                </div>
                <div v-if="process.details" class="mt-2 col-md-12">
                    {{process.details[process.details.length - 1]}}
                </div>
            </div>

        </div>

    </div>

</template>

<script>
/* eslint-disable no-new */
/* eslint eqeqeq: 0 */
import Icon from './Icon'
export default {
    components: {
        Icon
    },
    props: {
        processes: {
            type: Array,
            required: true
        }
    },
    data: () => ({
        pollToken: null,
        eventSource: null,
        configurationLoaded: false,
        requiresPolling: true,
        subscribedProcesses: [],
        completedProcesses: []
    }),
    methods: {
        completeProcess(process) {
            this.$emit('complete-process', process)
        },
        runPoll() {
            var my = this
            // we must poll for the activity.
            var watchedProcessIds = []
            my.processes.forEach(function(process) {
                watchedProcessIds.push(process.id)
            })
            new ConcreteAjaxRequest({
                loader: false,
                url: CCM_DISPATCHER_FILENAME + '/ccm/system/processes/poll/',
                data: {
                    token: my.pollToken,
                    watchedProcessIds: watchedProcessIds
                },
                success: r => {
                    var pollAgain = false
                    if (r.processes.length) {
                        r.processes.forEach(function (responseProcess) {
                            my.processes.forEach(function (process) {
                                if (process.id == responseProcess.id) {
                                    process.progress = responseProcess.progress
                                    process.dateCompleted = responseProcess.dateCompleted
                                    process.dateCompletedString = responseProcess.dateCompletedString
                                    process.batch = responseProcess.batch

                                    if (process.progress < 100) {
                                        pollAgain = true
                                    } else {
                                        my.completeProcess(process)
                                    }
                                }
                            })
                        })
                    }
                    if (pollAgain) {
                        setTimeout(function() {
                            my.runPoll()
                        }, 5000)
                    }
                }
            })
        }
    },
    mounted() {
        var my = this
        new ConcreteAjaxRequest({
            method: 'POST',
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/processes/get_configuration',
            data: {
                ccm_token: CCM_SECURITY_TOKEN
            },
            success: function (r) {
                my.configurationLoaded = true
                my.requiresPolling = r.requiresPolling
                my.pollToken = r.pollToken
                if (my.requiresPolling) {
                    my.runPoll()
                }
            }
        })

        ConcreteEvent.subscribe('ConcreteServerEventProcessOutput', function(e, data) {
            if (data.processId) {
                my.processes.forEach(function (thisProcess) {
                    if (thisProcess.id === data.processId) {
                        thisProcess.details.push(data.message)
                    }
                })
            }
        })
        ConcreteEvent.subscribe('ConcreteServerEventBatchUpdated', function(e, data) {
            var total = data.batch.totalJobs
            var progress = total - data.batch.pendingJobs
            var percent = Math.round(progress / total * 100)

            my.processes.forEach(function (thisProcess) {
                if (thisProcess.batch && thisProcess.batch.id == data.batch.id) {
                    thisProcess.progress = percent
                    thisProcess.batch = data.batch
                }
            })
        })
        ConcreteEvent.subscribe('ConcreteServerEventCloseProcess', function(e, data) {
            my.processes.forEach(function (thisProcess) {
                if (thisProcess.id == data.process.id) {
                    thisProcess.dateCompleted = data.process.dateCompleted
                    thisProcess.dateCompletedString = data.process.dateCompletedString
                    my.completeProcess(thisProcess)
                }
            })
        })
    },
    watch: {
        processes: {
            /*
            immediate: true,
            deep: true,
            */
            handler: function(processes) {
                const my = this
                if (my.configurationLoaded && my.requiresPolling) {
                    my.runPoll()
                }
            }
        }
    }
}
</script>
