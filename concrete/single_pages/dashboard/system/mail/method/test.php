<?php
defined('C5_EXECUTE') or die('Access Denied.');

// Arguments
/* @var bool $emailEnabled */
/* @var string $mailRecipient */

?>

<form method="post" action="<?= $view->action('do_test') ?>" id="mail-settings-test-form">
    <?php
    if (!$emailEnabled) {
        ?>
        <div class="alert alert-info">
            <?= t(/*i18n: %1$s is a configuration name, %2$s is a configuration value*/'It\'s not possible to test the settings since the mail system is disabled (the setting %1$s is set to %2$s in the configuration).', '<b>concrete.email.enabled</b>', '<b>false</b>') ?>
        </div>
        <?php
    } else {
        ?>
        <?php $token->output('test') ?>
        <div class="form-group">
            <?= $form->label('mailRecipient', t('Recipient email address')) ?>
            <?= $form->email('mailRecipient', $mailRecipient, ['required' => 'required']) ?>
        </div>
        <?php
    }
    ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= URL::to('/dashboard/system/mail/method') ?>" class="btn btn-default pull-left"><?= t('Change Settings') ?></a>
            <?php
            if ($emailEnabled) {
                echo $interface->submit(t('Send'), 'mail-settings-test-form', 'right', 'btn-primary');
            }
            ?>
        </div>
    </div>
</form>
