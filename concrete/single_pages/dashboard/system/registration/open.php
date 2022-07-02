<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\Open $controller
 * @var string $registrationType
 * @var bool $registerNotification
 * @var string $registerNotificationEmail
 * @var bool $emailAsUsername
 * @var bool $displayUsernameField
 * @var bool $displayConfirmPasswordField
 * @var bool $enableRegistrationCaptcha
 * @var bool $displayUsernameFieldEdit
 */
?>

<form method="POST" action="<?= $controller->action('update_registration_type') ?>">
    <?php $token->output('update_registration_type') ?>

    <div class="form-group">
        <?= $form->label('registration_type', t('Allow visitors to signup as site members?')) ?>
        <div class="form-check">
            <?= $form->radio('registration_type', 'disabled', $registrationType, ['id' => 'registration_type_disabled']) ?>
            <label class="form-check-label" for="registration_type_disabled"><?= t('Off - only admins can create accounts from Dashboard') ?></label>
        </div>
        <div class="form-check">
            <?= $form->radio('registration_type', 'enabled', $registrationType, ['id' => 'registration_type_enabled']) ?>
            <label class="form-check-label" for="registration_type_enabled"><?= t('On - anyone can create an account from Login page') ?></label>
        </div>
        <div class="form-check">
            <?= $form->radio('registration_type', 'validate_email', $registrationType, ['id' => 'registration_type_validate_email']) ?>
            <label class="form-check-label" for="registration_type_validate_email"><?= t('Validate - anyone can create an account from Login page, once validated by email') ?></label>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('', t('Notification')) ?>
        <div class="form-check">
            <?= $form->checkbox('register_notification', '1', $registerNotification, $registrationType === 'disabled' ? ['disabled' => 'disabled'] : []) ?>
            <label class="form-check-label" for="register_notification"><?= t('Send admin an email when new user registers.') ?></label>
            <div class="form-group notify_email<?= $registerNotification && $registrationType !== 'disabled' ? '' : ' d-none' ?>">
                <?= $form->label('register_notification_email', t('Recipient email addresses')) ?>
                <?= $form->text('register_notification_email', $registerNotificationEmail) ?>
                <small class="form-text text-muted"><?= t('(Separate multiple emails with a comma)') ?></small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('', t('Login form')) ?>
        <div class="form-check">
            <?= $form->radio('email_as_username', '0', $emailAsUsername ? '1' : '0', ['id' => 'email_as_username_0']) ?>
            <label class="form-check-label" for="email_as_username_0"><?= t('Ask for username & password') ?></label>
        </div>
        <div class="form-check">
            <?= $form->radio('email_as_username', '1', $emailAsUsername ? '1' : '0', ['id' => 'email_as_username_1']) ?>
            <label class="form-check-label" for="email_as_username_1"><?= t('Ask for email & password') ?></label>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('display_username_field', t('Registration form')) ?>
        <div class="form-check">
            <?= $form->checkbox('display_username_field', '1', $displayUsernameField) ?>
            <label class="form-check-label" for="display_username_field"><?= t('Username required') ?></label>
        </div>
        <div class="form-check">
            <?= $form->checkbox('display_confirm_password_field', '1', $displayConfirmPasswordField) ?>
            <label class="form-check-label" for="display_confirm_password_field"><?= t('Confirm Password required') ?></label>
        </div>
        <div class="form-check">
            <?= $form->checkbox('enable_registration_captcha', '1', $enableRegistrationCaptcha, $registrationType === 'disabled' ? ['disabled' => 'disabled'] : []) ?>
            <label class="form-check-label" for="enable_registration_captcha"><?= t('CAPTCHA required') ?></label>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('display_username_field_on_edit', t('Edit Profile form')) ?>
        <div class="form-check">
            <?= $form->checkbox('display_username_field_on_edit', '1', $displayUsernameFieldEdit) ?>
            <label class="form-check-label" for="display_username_field_on_edit"><?= t('Username required') ?></label>
        </div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?= $interface->submit(t('Save'), '', 'right', 'btn-primary') ?>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {

    function registrationTypeUpdated() {
        var registrationType = $('input[name=registration_type]:checked').val();
        $('input[name=register_notification]').attr('disabled', registrationType === 'disabled' ? 'disabled' : null);
        $('input[name=enable_registration_captcha]').attr('disabled', registrationType === 'disabled' ? 'disabled' : null);
        registerNotificationUpdated();
    }

    function registerNotificationUpdated() {
        var notify = $('input[name=register_notification]').is(':checked:enabled');
        $('.notify_email').toggleClass('d-none', !notify);
        $('#register_notification_email').attr('required', notify ? 'required' : null);
    }

    // If users choose to "login by username", "Username required" must be checked
    $('input[name=email_as_username][value=0]').on('change', function (e) {
        var $this = $(this),
            $displayUsername = $('input[name=display_username_field]');
        if (!$this.is(':checked') || $displayUsername.is(':checked')) {
            return;
        }
        $('input[name=email_as_username][value=1]').prop('checked', true);
        e.preventDefault();
        ConcreteAlert.confirm(
            <?= json_encode(t('You have to require the username if you want to login by username.')) ?>,
            function() {
                $this.prop('checked', true);
                $displayUsername.prop('checked', true);
                $.fn.dialog.closeTop();
            },
            '',
            <?= json_encode(t('Apply')) ?>
        );
        return false;
    });

    // If users uncheck the "Username required", we must switch to "login by email"
    $('input[name=display_username_field]').on('change', function (e) {
        if ($(this).is(':checked') || !$('input[name=email_as_username][value=0]').is(':checked')) {
            return;
        }
        e.preventDefault();
        ConcreteAlert.confirm(
            <?= json_encode(t('You have to disable ask for Username on login form, if you want to disable it.')) ?>,
            function() {
                $('input[name=display_username_field]').prop('checked', false);
                $('input[name=email_as_username]').prop('checked', true);
                $.fn.dialog.closeTop();
            },
            '',
            <?= json_encode(t('Apply')) ?>
        );
        return false;
    });

    $('input[name=registration_type]').on('change', function () {
        registrationTypeUpdated();
    });
    $('input[name=register_notification]').on('change', function () {
        registerNotificationUpdated();
    });

    registrationTypeUpdated();
});
</script>
