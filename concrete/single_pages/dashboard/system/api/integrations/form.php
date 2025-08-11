<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
/**
 * @var \Concrete\Core\Entity\OAuth\Client $client
 */

use Concrete\Core\Entity\OAuth\Client;

$dataScopes = [];
foreach ($scopes as $scope) {
    $dataScopes[] = ['identifier' => $scope->getIdentifier(), 'description' => $scope->getDescription()];
}

$customScopes = [];
if (isset($client) && $client->hasCustomScopes()) {
    $customScopes = $client->getScopes()->toArray();
}

?>

<div data-form="integration">
    <form method="post" v-cloak @submit="save">
        <div class="alert alert-danger" v-if="errorList.length > 0">
            <span v-html="errorList.join('<br>')"></span>
        </div>
        <fieldset>
            <div class="mb-3">
                <label class="form-label" for="name" ><?php echo t('Name'); ?></label>
                <div class="input-group">
                    <input type="text" class="form-control" required="required" autocomplete="off" autofocus="autofocus" v-model="name">
                    <span class="input-group-text"><i class="fas fa-asterisk"></i></span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="redirect"><?php echo t('Redirect'); ?></label>
                <div class="input-group">
                    <input type="text" pattern="https?:\/\/.+" class="form-control" autocomplete="off" v-model="redirect" required="required">
                    <span class="input-group-text"><i class="fas fa-asterisk"></i></span>
                </div>
                <div class="small text-muted"><?= t('You can specify multiple URLs separating them with %s', '<code>|</code>') ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label"><?php echo t('Documentation'); ?></label>
                <div class="form-check">
                    <input type="checkbox" id="enableDocumentation" class="form-check-input"  v-model="enableDocumentation">
                    <label class="form-check-label" for="enableDocumentation"><?=t('Enable interactive documentation.')?></label>
                </div>
                <div class="help-block"><?=t('When enabled, those with the `access_api` custom permission will be able to view interactive REST API documentation for the integration.')?></div>
            </div>
            
            <div class="mb-3">
                <label class="control-label form-label"><?=t('User Consent Level')?></label>
                <div class="form-check">
                    <input id="consent-type-standard" class="form-check-input" type="radio" name="consentType" value="<?= Client::CONSENT_SIMPLE ?>" v-model="consentType" />
                    <label for="consent-type-standard" class="form-check-label"><?= t('Standard') ?></label>
                </div>
                <div class="form-check">
                    <input id="consent-type-none" class="form-check-input" type="radio" name="consentType" value="<?= Client::CONSENT_NONE ?>" v-model="consentType" />
                    <label for="consent-type-none" class="form-check-label"><?= t('None') ?></label>
                </div>

                <div :class="{'consent-warning': true, 'alert': true, 'alert-danger': true, 'mt-3': true, 'd-none': consentType == 1}" >
                    <?= t("Only disable user consent if you trust this integration fully. By disabling user consent, you remove the user's ability to deny access.") ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="control-label form-label"><?=t('API Scopes')?></label>
                <div class="form-check">
                    <input id="custom-scopes-all" class="form-check-input" type="radio" name="hasCustomScopes" :value="false" v-model="hasCustomScopes" />
                    <label for="custom-scopes-all" class="form-check-label"><?= t('This client has full access to all API capabilities.') ?></label>
                </div>
                <div class="form-check">
                    <input id="custom-scopes-custom" class="form-check-input" type="radio" name="hasCustomScopes" :value="true" v-model="hasCustomScopes" />
                    <label for="custom-scopes-custom" class="form-check-label"><?= t('This client may only access certain parts of the API.') ?></label>
                </div>
            </div>

            <div v-if="hasCustomScopes" class="mb-3">
                <label class="form-label"><?=t('Enabled Scopes')?></label>

                <div class="form-check" v-for="(scope, index) in scopes">
                    <input type="checkbox" :id="'scope' + index" v-model="customScopes" :value="scope.identifier" class="form-check-input">
                    <label class="form-check-label" :for="'scope' + index">{{scope.identifier}}</label>
                </div>

            </div>


            <?php if (!isset($client)) { ?>
                <div class="mb-3">
                    <label class="form-label"><?=t('Client Key &amp; Secret')?></label>
                    <div class="alert alert-info"><?=t('When you add an Oauth2 integration, the client key and secret will automatically be generated.')?></div>
                </div>
            <?php } ?>

        </fieldset>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=URL::to('/dashboard/system/api/integrations')?>" class="float-start btn btn-secondary"><?=t('Cancel')?></a>
                <button class="float-end btn btn-primary" type="submit" ><?=t('Save')?></button>
            </div>
        </div>

    </form>
</div>

<script type="text/javascript">
    $(function() {
        Concrete.Vue.activateContext('cms', function (Vue, config) {
            new Vue({
                el: 'div[data-form=integration]',
                components: config.components,
                mounted() {

                },
                data: {
                    scopes: <?=json_encode($dataScopes)?>,
                    name: <?=json_encode(isset($client) ? $client->getName() : null)?>,
                    redirect: <?=json_encode(isset($client) ? $client->getSpecifiedRedirectUri() : null)?>,
                    <?php if (isset($client)) {
                        if ($client->isDocumentationEnabled()) { ?>
                            enableDocumentation: true,
                        <?php } else { ?>
                            enableDocumentation: false,
                        <?php }
                    } else { ?>
                        enableDocumentation: false,
                    <?php } ?>
                    errorList: [],
                    consentType: <?=json_encode(isset($client) ? $client->getConsentType() : Client::CONSENT_SIMPLE)?>,
                    <?php if (isset($client)) {
                        if ($client->hasCustomScopes()) { ?>
                            hasCustomScopes: true,
                        <?php } else { ?>
                            hasCustomScopes: false,
                        <?php }
                    } else { ?>
                        hasCustomScopes: false,
                    <?php } ?>
                    customScopes: <?=json_encode($customScopes)?>
                },
                watch: {},
                methods: {
                    save(e) {
                        e.preventDefault()
                        new ConcreteAjaxRequest({
                            url: '<?=$submitAction?>',
                            skipResponseValidation: true,
                            data: {
                                'ccm_token': <?=json_encode($submitToken)?>,
                                'name': this.name,
                                'redirect': this.redirect,
                                'enableDocumentation': this.enableDocumentation ? 1 : 0,
                                'consentType': this.consentType,
                                'hasCustomScopes': this.hasCustomScopes ? 1 : 0,
                                'customScopes': this.customScopes,
                            },
                            method: 'POST',
                            success: (r) => {
                                if (typeof(r.error) !== 'undefined') {
                                    this.errorList = r.errors
                                } else {
                                    this.errorList = []
                                    window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/system/api/integrations/view_client/' + r.identifier
                                }
                            }
                        })
                    }
                }
            })
        })
    });
</script>
