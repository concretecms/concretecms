<?php

use Concrete\Core\Logging\Levels;
use Monolog\Logger;

defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div data-view="error-handling" class="mb-5" v-cloak>
    <form method="post" @submit.prevent="submit">

        <section>
            <table class="table">
                <thead>
                <tr>
                    <th><?= t('Error Type') ?></th>
                    <th><?= ('Halt with Message') ?></th>
                    <th><?= t('Log Error @ Severity') ?></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="errorType in errorTypes" :key="errorType.title">
                    <td class="align-middle">{{errorType.title}}</td>
                    <td class="align-middle">
                        <span v-if="errorType.type === 'error'">
                            <?= t("Yes") ?>
                        </span>
                        <span v-else-if="errorType.type === 'deprecated'">
                            <?= t("No") ?>
                        </span>
                        <select v-else class="form-select form-select-sm"
                                v-model="errorConfiguration[errorType.type].halt">
                            <option :value="true"><?= t('Yes') ?></option>
                            <option :value="false"><?= t('No') ?></option>
                        </select>
                    </td>
                    <td>
                        <select class="form-select form-select-sm"
                                v-model="errorConfiguration[errorType.type].logLevel">
                            <optgroup label="<?=t('Will Log')?>">
                                <option v-for="logLevel in logLevels" :value="logLevel.name">{{ logLevel.displayName }}</option>
                            </optgroup>
                            <optgroup label="<?=t('Will NOT Log')?>">
                                <option value=""><?=t('None')?></option>
                            </optgroup>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
        </section>

        <div v-if="minimumSelectedLogLevel < minimumLogLevel" class="alert alert-danger">
            <?=t("You have chosen some error logging levels that are below your selected threshold of <b>{{minimumLogLevelName}}</b>. These errors will not be logged. You can change this logging threshold from the Logging Settings Dashboard page.")?>
            <div class="mt-3">
                <a class="btn-sm btn btn-secondary" href="<?=URL::to('/dashboard/system/environment/logging')?>"><?=t('Logging Settings')?></a>
            </div>
        </div>

        <section class="mt-5">
            <h4><?=t('Error Display')?></h4>
            <p><?=t('When halting execution and displaying an error message to users:')?></p>
            <div class="row">
                <div class="col-6">
                    <div class="mb-3 bg-info bg-opacity-10 p-3 rounded-1">
                        <div class="d-flex align-items-center">
                            <b><?=t('Regular Users and Non-Authenticated Visitors')?></b>
                            <button type="button" class="ms-auto btn btn-sm btn-secondary" @click="preview"><?=t('Preview')?></button>
                        </div>
                    </div>
                    <div>
                        <input type="radio" value="generic" id="halt0" class="form-check-input" v-model="errorDisplay">
                        <label for="halt0" class="form-check-label"><?=t('Show a generic error message.')?></label>
                    </div>
                    <div>
                        <input type="radio" value="message" id="halt1" class="form-check-input" v-model="errorDisplay">
                        <label for="halt1" class="form-check-label"><?=t('Show error exception message and nothing else.')?></label>
                    </div>
                    <div>
                        <input type="radio" value="debug" id="halt2" class="form-check-input" v-model="errorDisplay">
                        <label for="halt2" class="form-check-label"><?=t('Show debug error output.')?>
                            <i title="<?=t('This is could potentially disclose sensitive information. Do not enable this on a production website.')?>" id="debugErrorWarning" class="fa fa-exclamation-triangle"></i>
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mb-3 bg-info bg-opacity-10 p-3 rounded-1">
                        <div class="d-flex align-items-center">
                            <b><?=t('Users with the <a href="%s">View Debug Error Information</a> permission', URL::to('/dashboard/system/permissions/tasks'))?></b>
                        </div>
                    </div>
                    <div>
                        <input type="radio" value="generic" id="haltPrivileged0" class="form-check-input" v-model="errorDisplayPrivileged">
                        <label for="haltPrivileged0" class="form-check-label"><?=t('Show a generic error message.')?></label>
                    </div>
                    <div>
                        <input type="radio" value="message" id="haltPrivileged1" class="form-check-input" v-model="errorDisplayPrivileged">
                        <label for="haltPrivileged1" class="form-check-label"><?=t('Show error exception message and nothing else.')?></label>
                    </div>
                    <div>
                        <input type="radio" value="debug" id="haltPrivileged2" class="form-check-input" v-model="errorDisplayPrivileged">
                        <label for="haltPrivileged2" class="form-check-label"><?=t('Show debug error output.')?></label>
                    </div>
                </div>
            </div>
        </section>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="btn btn-primary float-end"
                        type="submit" name="save" value="save"><?= t("Save") ?></button>
            </div>
        </div>
    </form>

    <div class="modal fade" role="dialog" tabindex="-1" id="preview-modal">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?= t('Preview') ?></h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <iframe id="preview" style="width:100%;height:600px;border:0"></iframe>
                </div>
            </div>
        </div>
    </div>

</div>


<script>
    $(function () {
        Concrete.Vue.activateContext('backend', function (Vue, config) {
            new Vue({
                el: '[data-view=error-handling]',
                data: {
                    errorTypes: <?=json_encode($phpErrors)?>,
                    errorConfiguration: <?=json_encode($errorConfiguration)?>,
                    errorDisplay: <?=json_encode($errorDisplay)?>,
                    errorDisplayPrivileged: <?=json_encode($errorDisplayPrivileged)?>,
                    logLevels: <?=json_encode($logLevels)?>,
                    minimumLogLevel: <?=h($minimumLogLevel)?>,
                    minimumLogLevelName: <?=json_encode($minimumLogLevelName)?>
                },
                mounted() {
                    const tooltip = this.$el.querySelector('#debugErrorWarning')
                    new bootstrap.Tooltip(tooltip, { container: '#ccm-tooltip-holder' })
                },
                computed: {
                    minimumSelectedLogLevel() {
                        let minimumSelectedLogLevel = 9999;
                        this.errorTypes.forEach((errorType) => {
                            let errorTypeLogLevel = this.errorConfiguration[errorType.type].logLevel
                            if (errorTypeLogLevel) {
                                this.logLevels.forEach((logLevel) => {
                                    if (logLevel.name === errorTypeLogLevel) {
                                        if (logLevel.level < minimumSelectedLogLevel) {
                                            minimumSelectedLogLevel = logLevel.level
                                        }
                                    }
                                })
                            }
                        })
                        return minimumSelectedLogLevel
                    }
                },
                methods: {
                    preview() {
                        const url = new URL('<?=$view->action('preview')?>')
                        url.searchParams.append('errorDisplay', this.errorDisplay)
                        this.showPreview(url)
                    },
                    showPreview(url) {
                        const iframe = this.$el.querySelector('#preview')
                        iframe.setAttribute('src', url)
                        this.$nextTick(() => {
                            const previewModal = document.getElementById('preview-modal');
                            const modal = bootstrap.Modal.getOrCreateInstance(previewModal);
                            if (modal) {
                                modal.show();
                            }
                        })
                    },
                    submit() {
                        new ConcreteAjaxRequest({
                            url: '<?=$view->action('submit')?>',
                            data: {
                                errorConfiguration: JSON.stringify(this.errorConfiguration),
                                errorDisplay: this.errorDisplay,
                                errorDisplayPrivileged: this.errorDisplayPrivileged,
                                ccm_token: '<?=$token->generate('submit')?>'
                            },
                            success: function success(r) {
                                window.location.reload()
                            }
                        })
                    }
                }
            });
        });

    });
</script>
