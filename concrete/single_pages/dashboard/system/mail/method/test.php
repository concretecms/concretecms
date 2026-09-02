<?php

declare(strict_types=1);

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Mail\Method\Test $controller
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var bool $emailEnabled
 * @var string $senderEmailAddress
 * @var string $senderEmailName
 * @var string $myEmailAddress
 * @var string $settingsPageUrl
 * @var array{name:string,url:string}|null $systemEmailAddressesPage
 */

if (!$emailEnabled) {
    ?>
    <div class="alert alert-info">
        <?= t(/* i18n: %1$s is a configuration name, %2$s is a configuration value */'It\'s not possible to test the settings since the mail system is disabled (the setting %1$s is set to %2$s in the configuration).', '<code>concrete.email.enabled</code>', '<code>false</code>') ?>
        <?php
        if ($settingsPageUrl !== '') {
            ?>
            <div class="mt-3">
                <a href="<?= h($settingsPageUrl) ?>" class="btn btn-secondary"><?= t('Change Settings') ?></a>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    return;
}
?>

<form id="mail-settings-test-form" v-cloak @submit.prevent="submit()">
    <div class="form-group">
        <label class="form-label" for="emailSender"><?= t('Sender') ?></label>
        <input type="text" class="form-control" id="emailSender" readonly="readonly" :value="senderEmailName ? `${senderEmailName} <${senderEmailAddress}>`: senderEmailAddress" />
        <?php
        if ($systemEmailAddressesPage !== null) {
            ?>
            <div class="form-text">
                <?= t(
                    'You can change the sender in the %s page',
                    $systemEmailAddressesPage['url']
                    ? '<a href="' . h($systemEmailAddressesPage['url']) . '">' . h($systemEmailAddressesPage['name']) . '</a>'
                    : h($systemEmailAddressesPage['name'])
                ) ?>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="form-group">
        <label class="form-label" for="emailRecipient"><?= t('Recipient email address') ?></label>
        <input type="email" class="form-control" id="emailRecipient" required="required" v-model.trim="emailRecipient" :readonly="busy" />
        <div class="form-text" v-if="myEmailAddress" :class="emailRecipient === myEmailAddress ? 'invisible' : ''">
            <a href="#" @click.prevent="emailRecipient = myEmailAddress"><?= t('Use your email address') ?></a>
        </div>
    </div>
    <div class="form-group">
        <label class="form-label" for="numEmails"><?= t('Number of messages to send') ?></label>
        <input type="number" class="form-control" id="numEmails" required="required" min="1" v-model.number="numEmails" :readonly="busy" />
    </div>
    <div v-if="outcome" class="alert" :class="outcome.success ? 'alert-success' : 'alert-danger'">
        <div v-if="outcome.isHtml" v-html="outcome.message"></div>
        <div v-else style="white-space: pre-wrap">{{ outcome.message }}</div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php
            if ($settingsPageUrl !== '') {
                ?>
                <a href="<?= h($settingsPageUrl) ?>" class="btn btn-secondary"><?= t('Change Settings') ?></a>
                <?php
            }
            ?>
            <input type="submit" class="btn btn-primary float-end" value="<?= t('Send') ?>" :disabled="busy" />
        </div>
    </div>
</form>

<script>
$(document).ready(() => {
    Concrete.Vue.activateContext('cms', (Vue, config) => {
        new Vue({
            el: '#mail-settings-test-form',
            data() {
                return {
                    senderEmailAddress: <?= json_encode($senderEmailAddress) ?>,
                    senderEmailName: <?= json_encode($senderEmailName) ?>,
                    myEmailAddress: <?= json_encode($myEmailAddress) ?>,
                    emailRecipient: <?= json_encode($myEmailAddress) ?>,
                    numEmails: 1,
                    busy: false,
                    outcome: null,
                };
            },
            mounted() {
                window.addEventListener('beforeunload', (e) => {
                    if (this.busy) {
                        e.preventDefault();
                        return e.returnValue = 'confirm';
                    }
                });
            },
            watch: {
                emailRecipient() {
                    this.outcome = null;
                },
                busy() {
                    if (this.busy) {
                        window.NProgress.start()
                    } else {
                        window.NProgress.done()
                    }
                },
            },
            methods: {
                submit() {
                    if (this.busy) {
                        return;
                    }
                    this.busy = true;
                    this.outcome = null;
                    $.ajax({
                        method: 'POST',
                        dataType: 'json',
                        url: <?= json_encode((string) $view->action('send_test_email')) ?>,
                        data: {
                            <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>: <?= json_encode($token->generate('send_test_email')) ?>,
                            emailRecipient: this.emailRecipient,
                            numEmails: this.numEmails,
                        },
                    }).done(() => {
                        this.outcome = {
                            success: true,
                            isHtml: false,
                            message: <?= json_encode(t('The test email has been successfully sent to %s.')) ?>.replace('%s', this.emailRecipient),
                        };
                    }).fail((xhr, status, error) => {
                        if (xhr?.responseJSON?.errors instanceof Array && typeof xhr.responseJSON.errors[0] === 'string') {
                            this.outcome = {
                                success: false,
                                isHtml: false,
                                message: xhr.responseJSON.errors[0],
                            };
                        } else {
                            this.outcome = {
                                success: false,
                                isHtml: xhr.responseJSON ? true : false,
                                message: xhr.responseJSON ? ConcreteAjaxRequest.renderJsonError(xhr) : xhr.responseText,
                            };
                        }
                    }).always(() => {
                        this.busy = false;
                    });
                },
            },
        });
    });
});
</script>
