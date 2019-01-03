<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<form method="post" id="registration-type-form" action="<?= $view->action('update_registration_type'); ?>">
    <?= $token->output('update_registration_type'); ?>

    <div class="form-group">
        <?= $form->label('registration_type', t('Allow visitors to signup as site members?')); ?>
        <div class="radio">
            <label>
                <input type="radio" name="registration_type" value="disabled" <?= ($registration_type == 'disabled' || !strlen($registration_type)) ? 'checked' : ''; ?> />
                <span><?= t('Off - only admins can create accounts from Dashboard'); ?></span>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="registration_type" value="enabled" <?= ($registration_type == 'enabled') ? 'checked' : ''; ?> />
                <span><?= t('On - anyone can create an account from Login page'); ?></span>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="registration_type" value="validate_email" <?= ($registration_type == 'validate_email') ? 'checked' : ''; ?> />
                <span><?= t('Validate - anyone can create an account from Login page, once validated by email'); ?></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('register_notification', t('Notification')); ?>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="register_notification" value="1"<?= ($register_notification) ? ' checked="checked"' : ''; ?>/>
                <span><?= t('Send admin an email when new user registers.'); ?></span>
            </label>
        </div>
    </div>
    <div class="form-group notify_email">
        <?= $form->label('register_notification_email', t('Email addresses')); ?>
        <?= $form->text('register_notification_email', h($register_notification_email)); ?>
        <p class="help-block"><?= t('(Separate multiple emails with a comma)'); ?></p>
    </div>
    <div class="form-group">
        <?= $form->label('email_as_username', t('Login form')); ?>
        <div class="radio">
            <label>
                <input type="radio" name="email_as_username" value="0" id="display_username_on_login" <?= (!$email_as_username) ? 'checked' : ''; ?> />
                <span><?= t('Ask for username & password'); ?></span>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="email_as_username" value="1" <?= ($email_as_username) ? 'checked' : ''; ?> />
                <span><?= t('Ask for email & password'); ?></span>
            </label>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('display_username_field', t('Registration form')); ?>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="display_username_field" value="1" <?= ($display_username_field) ? 'checked' : ''; ?> />
                <span><?= t('Username required'); ?></span>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="display_confirm_password_field" value="1" <?= ($display_confirm_password_field) ? 'checked' : ''; ?> />
                <span><?= t('Confirm Password required'); ?></span>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="enable_registration_captcha" value="1" <?= ($enable_registration_captcha) ? 'checked' : ''; ?> />
                <span><?= t('CAPTCHA required'); ?></span>
            </label>
        </div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?= $concrete_ui->submit(t('Save'), 'registration-type-form', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>

<div id="dialog-confirm" style="display: none" title="<?= t('Do you want to apply?'); ?>">
    <p><?= t('You have to disable ask for Username on login form, if you want to disable it.'); ?></p>
    <div class="dialog-buttons">
        <button class="btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel'); ?></button>
        <button class="btn btn-success pull-right" onclick="enableEmailAsUsername()"><?=  t('Apply'); ?></button>
    </div>
</div>

<script type="text/javascript">

    var val = $("input[name=registration_type]:checked").val();
    if (val == 'disabled') {
        $("input[name=enable_registration_captcha]").prop('disabled', true).prop('checked', false);
        $("input[name=register_notification]").prop('checked', false);
        $('.notify_email').hide();
        $("input[name=register_notification]").prop('disabled', true);
    }
    if ($('input[name=register_notification]').prop('checked')) {
        $('.notify_email').show();
    } else {
        $('.notify_email').hide();
    }
    $("input[name=registration_type]").click(function () {
        if ($(this).val() === 'disabled') {
            $("input[name=enable_registration_captcha]").prop('disabled', true).prop('checked', false);
            $("input[name=register_notification]").prop('checked', false).prop('disabled', true);
            $('.notify_email').hide();
        } else {
            $("input[name=enable_registration_captcha]").prop('disabled', false);
            $("input[name=register_notification]").prop('disabled', false);
        }
    });

    $("input[name=register_notification]").click(function () {
        if ($(this).is(':checked')) {
            $('.notify_email').show();
        } else {
            $('.notify_email').hide();
        }
    });

    $("input[name=display_username_field]").click(function (e) {
        if (!$(this).is(':checked') && $("#display_username_on_login").is(":checked")) {
            $.fn.dialog.open({
                width: 500,
                height: 100,
                element: $("#dialog-confirm"),
            });
            return false;
        }
    });

    function enableEmailAsUsername() {
        $('input[name=display_username_field]').prop('checked', false);
        $('input[name=email_as_username]').prop('checked', true);
        $.fn.dialog.closeTop();
    }
</script>
