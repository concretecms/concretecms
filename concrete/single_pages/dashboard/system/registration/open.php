<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?= Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    t('Public Registration'),
    t(
        'Control the options available for Public Registration.'),
    'span6 offset3',
    false); ?>
<?php
$h = Loader::helper('concrete/ui');
?>
<form method="post" id="registration-type-form"
      action="<?php echo $view->action('update_registration_type') ?>">
        <?=$token->output('update_registration_type')?>

    <div class="form-group">
        <label class="control-label"><?php echo t('Allow visitors to signup as site members?') ?></label>
        <div class="radio">
            <label>
                <input type="radio" name="registration_type" value="disabled"
                       style="" <?php echo ($registration_type == "disabled" || !strlen(
                        $registration_type)) ? 'checked' : '' ?> />
                    <span>
                        <?php echo t('Off - only admins can create accounts from Dashboard') ?>
                    </span>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="registration_type" value="enabled"
                       style="" <?php echo ($registration_type == "enabled") ? 'checked' : '' ?> />
                <span><?php echo t('On - anyone can create an account from Login page') ?></span>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="registration_type" value="validate_email"
                       style="" <?php echo ($registration_type == "validate_email") ? 'checked' : '' ?> />
                <span><?php echo t('Validate - anyone can create an account from Login page, once validated by email') ?></span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?php echo t('Notification') ?></label>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="register_notification"
                       value="1"<?php echo ($register_notification) ? ' checked="checked"' : '' ?>/>
                <span><?php echo t('Send admin an email when new user registers.'); ?></span>
            </label>
        </div>
    </div>
    <div class="form-group notify_email">
        <label class="control-label"><?php echo t('Email address'); ?></label>
        <input class="form-control" name="register_notification_email" type="text"
            value="<?php echo h($register_notification_email); ?>"/>
    </div>
    <div class="form-group">
        <label class="control-label"><?php echo t('Login form') ?></label>
        <div class="radio">
            <label>
                <input type="radio" name="email_as_username" value="0" id="display_username_on_login"
                       style="" <?php echo (!$email_as_username) ? 'checked' : '' ?> />
                    <span>
                        <?php echo t('Ask for username & password') ?>
                    </span>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="email_as_username" value="1"
                       style="" <?php echo ($email_as_username) ? 'checked' : '' ?> />
                    <span>
                        <?php echo t('Ask for email & password') ?>
                    </span>
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label"><?php echo t('Registration form') ?></label>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="display_username_field" value="1"
                       style="" <?php echo ($display_username_field) ? 'checked' : '' ?> />
                <span><?php echo t('Username required') ?></span>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="display_confirm_password_field" value="1"
                       style="" <?php echo ($display_confirm_password_field) ? 'checked' : '' ?> />
                <span><?php echo t('Confirm Password required') ?></span>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="enable_registration_captcha" value="1"
                       style="" <?php echo ($enable_registration_captcha) ? 'checked' : '' ?> />
                <span><?php echo t('CAPTCHA required') ?></span>
            </label>
        </div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?= $h->submit(t('Save'), 'registration-type-form', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>

<div id="dialog-confirm" style="display: none" title="<?= t('Do you want to apply?') ?>">
    <p><?=t('You have to disable ask for Username on login form, if you want to disable it.') ?></p>
    <div class="dialog-buttons">
        <button class="btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
        <button class="btn btn-success pull-right" onclick="enableEmailAsUsername()"><?php echo  t('Apply') ?></button>
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
