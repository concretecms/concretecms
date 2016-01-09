<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?= Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    t('Public Registration'),
    t(
        'Control the options available for Public Registration.'),
    'span6 offset3',
    false); ?>
<?php
$h = Loader::helper('concrete/ui');
?>
<form class="form-stacked" method="post" id="registration-type-form"
      action="<?php echo $view->action('update_registration_type') ?>">
        <?=$token->output('update_registration_type')?>
    <div class="row">

        <div class="col-sm-6">
            <label id="optionsCheckboxes"><strong><?php echo t(
                        'Allow visitors to signup as site members?') ?></strong></label>

            <div class="form-group">
                <div class="radio">
                    <label>
                        <input type="radio" name="registration_type" value="disabled"
                               style="" <?php echo ($registration_type == "disabled" || !strlen(
                                $registration_type)) ? 'checked' : '' ?> />
                            <span>
                                <?php echo t('Off') ?>
                            </span>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="registration_type" value="validate_email"
                               style="" <?php echo ($registration_type == "validate_email") ? 'checked' : '' ?> />
                        <span><?php echo t(' On - email validation') ?></span>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="registration_type" value="manual_approve"
                               style="" <?php echo ($registration_type == "manual_approve") ? 'checked' : '' ?> />
                        <span><?php echo t('On - approve manually') ?></span>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="registration_type" value="enabled"
                               style="" <?php echo ($registration_type == "enabled") ? 'checked' : '' ?> />
                        <span><?php echo t('On - signup and go') ?></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <label id="optionsCheckboxes"><strong><?php echo t('Options') ?></strong></label>


            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="register_notification"
                               value="1"<?php echo ($register_notification) ? ' checked="checked"' : '' ?>/>
                        <span><?php echo t('Send email when a user registers'); ?></span>
                    </label>
                    <label class="notify_email">
                        <span><?php echo t('Email address'); ?> </span>
                        <input
                            class="span3" name="register_notification_email" type="text"
                            value="<?php echo h($register_notification_email); ?>"/>
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="enable_registration_captcha" value="1"
                               style="" <?php echo ($enable_registration_captcha) ? 'checked' : '' ?> />
                        <span><?php echo t('CAPTCHA required') ?></span>
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="email_as_username" value="1"
                               style="" <?php echo ($email_as_username) ? 'checked' : '' ?> />
                        <span><?php echo t('Use emails for login') ?></span>
                    </label>
                </div>
            </div>
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
        $("input[name=enable_registration_captcha]").attr('disabled', true);
        $("input[name=register_notification]").attr('checked', false);
        $('.notify_email').hide();
        $("input[name=register_notification]").attr('disabled', true);
    }
    if ($('input[name=register_notification]').attr('checked')) {
        $('.notify_email').show();
    } else {
        $('.notify_email').hide();
    }
    $("input[name=registration_type]").click(function () {
        if ($(this).val() === 'disabled') {
            $("input[name=enable_registration_captcha]").attr('disabled', true).attr('checked', false);
            $("input[name=register_notification]").attr('checked', false).attr('disabled', true);
            $('.notify_email').hide();
        } else {
            $("input[name=enable_registration_captcha]").attr('disabled', false);
            $("input[name=register_notification]").attr('disabled', false);
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
