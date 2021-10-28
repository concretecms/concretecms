<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Core::make('helper/form');
$date = Core::make('date')
?>

<div class="ccm-ui">
    <div data-view="populate-custom-slot" class="ccm-board-slot-designer">
        <div v-show="currentStep == 'items'">
            <ul class="nav nav-tabs nav-fill mb-3">
                <li class="nav-item" v-for="dataSource in dataSources" :key="dataSource.id">
                    <a :class="{'nav-link': true, 'active': activeDataSource === dataSource.id}"
                       @click="activeDataSource = dataSource.id"
                       href="javascript:void(0)">
                        {{dataSource.name}}
                    </a>
                </li>
            </ul>

            <form @submit="performSearch">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input-group mb-3">
                                <input type="text" v-model="searchKeywords" class="form-control" placeholder="<?=t('Search items')?>">
                                <button class="btn btn-secondary" type="submit"><?=t('Search')?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="container" v-show="activeDataSource === dataSource.id" v-for="dataSource in dataSources"
                 :key="dataSource.id">
                <div class="row row-cols-1 row-cols-md-3">
                    <div class="col mb-4"
                         v-for="item in dataSource.items" @click="toggleChecked(item)" style="cursor: pointer">
                        <div class="card mb-4">
                            <img :src="item.thumbnail" class="ccm-board-slot-designer-thumbnail" alt="">
                            <div class="card-body">
                                <h5 class="card-title">{{item.name}}</h5>
                                <div class="text-muted">{{item.relevantDateString}}</div>
                            </div>
                            <input type="checkbox" :value="item.id" v-model="selectedItemIds">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-show="currentStep == 'templates'">
            <div v-for="(templateOption, index) in templateOptions">

                <div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" :value="index" name="selectedTemplateOption"
                                   v-model="selectedTemplateOption">
                            <span class="text-muted"><?= t('Template Name:') ?> {{templateOption.template.name}}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 ps-0">

                        <span v-html="templateOption.content"></span>
                        <hr>

                    </div>
                </div>

            </div>
            <div v-if="templateOptions.length === 0">
                <?=t('No available templates found for selected items.')?>
            </div>
        </div>

        <div v-show="currentStep == 'schedule'">
            <form autocomplete="off">
                <div class="mb-3">
                    <label class="form-label"><?=t('Name')?></label>
                    <input type="text" class="form-control" v-model="customSlotName">
                </div>
                <div class="mb-3">
                    <label class="form-label"><?=t('From')?></label>
                    <div class="row">
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
                                <option v-for="currentSlot in instance.board.template.slots" :value="currentSlot">{{currentSlot}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary" @click="handleCancelButton">{{cancelButtonText}}</button>
            <button type="button" @click="handleSaveButton"
                    :disabled="selectedItemIds.length === 0 || (currentStep == 'templates' && selectedTemplateOption < 0)"
                    class="btn btn-primary">{{saveButtonText}}
            </button>
        </div>


    </div>
</div>

<script type="text/javascript">

    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: 'div[data-view=populate-custom-slot]',
            components: config.components,
            mounted() {
                this.activeDataSource = this.dataSources[0].id
            },
            methods: {
                createRule() {
                    var my = this
                    new ConcreteAjaxRequest({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/boards/custom_slot/save_template?boardInstanceID=' +
                            my.boardInstanceID,
                        method: 'POST',
                        data: {
                            // Comment this out because with it we're not turning this into a draft post.
                            // Uncomment this when we figure out a way to determine whether we're using the schedule functionality
                            // or the create in place functionality
                            //slot: my.slot,
                            slot: 0,
                            selectedTemplateOption: my.templateOptions[my.selectedTemplateOption]
                        },
                        success: function (r) {
                            my.currentStep = 'schedule'
                            my.currentRule = r
                        }
                    })
                },
                scheduleRule() {
                    var my = this
                    if (my.currentRule) {
                        new ConcreteAjaxRequest({
                            url: CCM_DISPATCHER_FILENAME + '/ccm/system/board/instance/save_rule/' +
                                my.currentRule.id,
                            method: 'POST',
                            data: {
                                ccm_token: '<?=Core::make('token')->generate('save_rule')?>',
                                slot: this.slot,
                                startDate: this.startDateFormatted,
                                endDate: this.endDateFormatted,
                                name: this.customSlotName,
                                startTime: this.startTime,
                                endTime: this.endTime,
                                timezone: this.timezone,
                            },
                            success: function (r) {
                                window.location.reload()
                            }
                        })
                    }
                },
                getTemplates() {
                    var my = this
                    new ConcreteAjaxRequest({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/boards/custom_slot/get_templates?boardInstanceID=' +
                            my.boardInstanceID,
                        method: 'POST',
                        data: {
                            slot: my.slot,
                            selectedItemIds: my.selectedItemIds
                        },
                        success: function (r) {
                            my.currentStep = 'templates'
                            my.templateOptions = r;
                        }
                    })
                },
                handleCancelButton() {
                    if (this.currentStep === 'items') {
                        jQuery.fn.dialog.closeTop();
                    }
                    if (this.currentStep === 'templates') {
                        this.currentStep = 'items'
                    }
                    if (this.currentStep === 'schedule') {
                        this.currentStep = 'templates'
                    }
                },
                toggleChecked(item) {
                    if (this.selectedItemIds.includes(item.id)) {
                        this.selectedItemIds.splice(this.selectedItemIds.indexOf(item.id), 1)
                    } else {
                        this.selectedItemIds.push(item.id)
                    }
                },
                performSearch(event) {
                    event.preventDefault()
                    var my = this
                    new ConcreteAjaxRequest({
                        url: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/boards/custom_slot/search_items?boardInstanceID=' +
                            my.boardInstanceID,
                        method: 'POST',
                        data: {
                            keywords: my.searchKeywords
                        },
                        success: function (r) {
                            my.dataSources = r;
                        }
                    })
                },
                handleSaveButton() {
                    if (this.currentStep === 'items') {
                        this.getTemplates();
                    }
                    if (this.currentStep === 'templates') {
                        this.createRule()
                    }
                    if (this.currentStep === 'schedule') {
                        this.scheduleRule()
                    }
                }
            },
            computed: {
                cancelButtonText: function () {
                    if (this.currentStep == 'items') {
                        return '<?=t('Close')?>'
                    } else if (this.currentStep == 'templates' || this.currentStep == 'schedule') {
                        return '<?=t('Back')?>'
                    }
                },
                saveButtonText: function () {
                    if (this.currentStep == 'items' || this.currentStep == 'templates') {
                        return '<?=t('Next')?>'
                    } else if (this.currentStep === 'schedule') {
                        return '<?=t('Save')?>'
                    }
                },
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
            },
            watch: {},
            data: {
                invalidStartDate: false,
                invalidEndDate: false,
                invalidSelectedElement: false,
                startDate: '',
                endDate: '',
                customSlotName: '',
                startTime: '00:00',
                endTime: '23:59',
                slot: <?=(int)$slot?>,
                timezone: '<?=date_default_timezone_get()?>',
                currentStep: 'items',
                searchKeywords: '',
                selectedItemIds: [],
                boardInstanceID: <?=$instance->getBoardInstanceID()?>,
                instance: <?=json_encode($instance)?>,
                dataSources: <?=$dataSourcesJson?>,
                activeDataSource: 0,
                currentRule: null,
                templateOptions: [],
                selectedTemplateOption: -1,
            }
        })
    })


</script>
