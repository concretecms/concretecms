<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>


<form data-form="publish" v-cloak autocomplete="off">
    <div class="row">
        <div class="col-lg-5">
            <h3><?= t('Boards') ?></h3>
            <table class="table w-100">
                <thead>
                <tr>
                    <th><input type="checkbox" v-model="bulkEnabled" @change="toggleBulk"/></th>
                    <th><?= t('Name') ?></th>
                    <th><?= t('Site') ?></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="instance in instances" :key="instance.boardInstanceID">
                    <td><input type="checkbox" v-model="selectedInstances" :value="instance.boardInstanceID"/></td>
                    <td>{{instance.name}}</td>
                    <td>{{instance.site.name}}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="col-lg-5 offset-1 pt-3">
            <div class="mb-3">
                <h3 class="mb-3"><?= t('Stripes Shared by Selected Boards') ?></h3>

                <board-instance-rule-list @delete="deleteBatchRule"
                                          :rules="sharedInstanceSlotRules"></board-instance-rule-list>

                <p v-if="selectedInstances.length === 0"><?= t(
                        'Select some boards to see scheduled instances shared by those boards.'
                    ) ?></p>
            </div>
            <div class="text-center">
                <button :disabled="selectedInstances.length === 0"
                        type="button"
                        class="btn btn-secondary"
                        data-bs-toggle="modal" data-bs-target="#schedule-modal"><?= t(
                        'Add Scheduled Stripe'
                    ) ?></button>
            </div>

        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="schedule-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('Publish') ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="control-label form-label" for="element"><?= t('Element') ?></label>
                        <select :class='{"form-control": true, "is-invalid": invalidSelectedElement}' id="element"
                                v-model="selectedElement">
                            <option value="0">** Choose Element</option>
                            <option v-for="element in elements" :value="element.id">{{element.name}}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?=t('From')?></label>
                        <div class="row mb-3">
                            <div class="col-6">
                                <v-date-picker
                                        :masks="{'input': 'YYYY-MM-DD'}"
                                        v-model='startDate'
                                        :input-props='{name: "startDate", class: ["form-control", {"is-invalid": invalidStartDate}]}'
                                ></v-date-picker>
                            </div>
                            <div class="col-6">
                                <input type="time" class="form-control" v-model="startTime">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?=t('To')?></label>
                        <div class="row">
                            <div class="col-6">
                                <v-date-picker
                                        :masks="{'input': 'YYYY-MM-DD'}"
                                        v-model='endDate'
                                        :input-props='{name: "end", class: ["form-control", {"is-invalid": invalidEndDate}]}'
                                ></v-date-picker>
                            </div>
                            <div class="col-6">
                                <input type="time" class="form-control" v-model="endTime">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <?= $form->label('timezone', t('Time Zone')); ?>
                                <?= $form->select('timezone', $date->getTimezones(), ['v-model' => 'timezone']) ?>
                            </div>
                            <div class="col-6">
                                <label class="control-label form-label" for="chooseSlot"><?= t('Slot') ?></label>
                                <select class="form-select" id="chooseSlot" v-model="slot">
                                    <option v-for="currentSlot in totalSlots" :value="currentSlot">{{currentSlot}}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?=t('Curation')?></label>
                        <div class="form-check">
                            <input class="form-check-input" id="lockType1" type="radio" v-model="lockType" value="L">
                            <label class="form-check-label" for="lockType1">
                                <?= t('Lock stripe – only admins can change.') ?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="lockType2" v-model="lockType" value="U">
                            <label class="form-check-label" for="lockType2">
                                <?= t('Share stripe – editors can remove.') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="save">Save changes</button>
                </div>
            </div>
        </div>
    </div>


</form>

<script type="text/javascript">
    $(function () {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: 'form[data-form=publish]',
                components: config.components,
                data: {
                    instances: <?=json_encode($instances)?>,
                    selectedInstances: [],
                    bulkEnabled: false,
                    invalidStartDate: false,
                    invalidEndDate: false,
                    invalidSelectedElement: false,
                    startDate: '',
                    endDate: '',
                    startTime: '00:00',
                    endTime: '23:59',
                    slot: 1,
                    timezone: '<?=date_default_timezone_get()?>',
                    lockType: 'L',
                    selectedElement: <?=json_encode($selectedElementID)?>,
                    elements: <?=json_encode($elements)?>,
                    sharedInstanceSlotRules: []
                },

                computed: {
                    startDateFormatted() {
                        if (this.startDate) {
                            return moment(this.startDate).format("YYYY-MM-DD")
                        }
                        return null
                    },
                    endDateFormatted() {
                        if (this.endDate) {
                            return moment(this.endDate).format("YYYY-MM-DD")
                        }
                        return null
                    },
                    totalSlots() {
                        let totalSlots = 0
                        this.instances.forEach(function (instance) {
                            if (instance.board.template.slots > totalSlots) {
                                totalSlots = instance.board.template.slots
                            }
                        })
                        return totalSlots
                    }
                },

                watch: {
                    startDate: function () {
                        this.invalidStartDate = false
                        this.invalidEndDate = false
                    },
                    selectedInstances: function () {
                        this.sharedInstanceSlotRules = []
                        var my = this
                        new ConcreteAjaxRequest({
                            url: '<?=$view->action('get_shared_rules')?>',
                            method: 'POST',
                            data: {
                                ccm_token: '<?=$token->generate('get_shared_rules')?>',
                                instances: this.selectedInstances
                            },
                            success: function (r) {
                                my.sharedInstanceSlotRules = r
                            }
                        })
                    }
                },
                methods: {
                    deleteBatchRule(rule, index) {
                        var my = this
                        new ConcreteAjaxRequest({
                            url: CCM_DISPATCHER_FILENAME + '/ccm/system/board/instance/delete_rule_by_batch',
                            data: {
                                instances: this.selectedInstances,
                                batchIdentifier: rule.batchIdentifier,
                                ccm_token: CCM_SECURITY_TOKEN,
                            },
                            success: function (r) {
                                my.sharedInstanceSlotRules.forEach(function(thisRule, index) {
                                    if (thisRule.id === rule.id) {
                                        my.sharedInstanceSlotRules.splice(index, 1);
                                    }
                                })
                            }
                        })
                    },
                    toggleBulk() {
                        if (this.bulkEnabled) {
                            this.instances.forEach(instance => this.selectedInstances.push(instance.boardInstanceID))
                        } else {
                            this.selectedInstances = [];
                        }
                    },
                    save() {
                        let valid = true
                        if (!this.startDateFormatted) {
                            this.invalidStartDate = true
                        } else {
                            this.invalidStartDate = false
                        }
                        if (!this.endDateFormatted) {
                            this.invalidEndDate = true
                        } else {
                            this.invalidEndDate = false
                        }
                        if (this.selectedElement < 1) {
                            this.invalidSelectedElement = true
                        } else {
                            this.invalidSelectedElement = false
                        }
                        if (this.invalidStartDate || this.invalidEndDate || this.invalidSelectedElement) {
                            valid = false
                        }
                        if (valid) {
                            new ConcreteAjaxRequest({
                                url: '<?=$view->action('submit')?>',
                                method: 'POST',
                                data: {
                                    ccm_token: '<?=$token->generate('submit')?>',
                                    elementId: this.selectedElement,
                                    slot: this.slot,
                                    startDate: this.startDateFormatted,
                                    endDate: this.endDateFormatted,
                                    startTime: this.startTime,
                                    endTime: this.endTime,
                                    lockType: this.lockType,
                                    timezone: this.timezone,
                                    instances: this.selectedInstances
                                },
                                success: function (r) {
                                    window.location.href = '<?=URL::to('/dashboard/boards/designer')?>';
                                }
                            })
                        }
                    }
                }
            })
        })
    });
</script>
