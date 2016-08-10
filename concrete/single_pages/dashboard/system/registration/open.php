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
        <label class="control-label"><?php echo t('Spam') ?></label>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="enable_registration_captcha" value="1"
                       style="" <?php echo ($enable_registration_captcha) ? 'checked' : '' ?> />
                <span><?php echo t('CAPTCHA required') ?></span>
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label"><?php echo t('Username') ?></label>
        <div class="radio">
            <label>
                <input type="radio" name="email_as_username" value="0"
                       style="" <?php echo (!$email_as_username) ? 'checked' : '' ?> />
                    <span>
                        <?php echo t('Ask for Username & password on login form') ?>
                    </span>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="email_as_username" value="1"
                       style="" <?php echo ($email_as_username) ? 'checked' : '' ?> />
                    <span>
                        <?php echo t('Ask for Email & password on login form') ?>
                    </span>
            </label>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?= $h->submit(t('Save'), 'registration-type-form', 'right', 'btn-primary'); ?>
        </div>
    </div>
</form>


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
</script>
