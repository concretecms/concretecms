<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class="alert alert-info">
    <h4><?php echo t('Twitter Login Configuration'); ?></h4>
    <p><?php echo t('<a href="%s" target="_blank">Click here</a> to obtain your access keys.', 'https://apps.twitter.com/'); ?></p>
    <p><?php echo t('Check the box labeled "Allow this application to be used to Sign in with Twitter".'); ?></p>
    <p><?php echo t('Set the "Callback URL" to:%s.', ' <code>'.\URL::to('/ccm/system/authentication/oauth2/twitter/callback').'</code>'); ?></p>
</div>

<div class='form-group'>
    <?=$form->label('apikey', t('Consumer Key (API Key)'))?>
    <?=$form->text('apikey', $apikey, array('autocomplete' => 'off'))?>
</div>
<div class='form-group'>
    <?=$form->label('apisecret', t('Consumer Secret (API Secret)'))?>
    <div class="input-group">
        <?=$form->password('apisecret', $apisecret, array('autocomplete' => 'off'))?>
        <span class="input-group-btn">
        <button id="showsecret" class="btn btn-warning" type="button"><?php echo t('Show API secret')?></button>
      </span>
    </div>
</div>
<div class='form-group'>
    <div class="input-group">
        <label type="checkbox">
            <input type="checkbox" name="registration_enabled" value="1" <?= \Config::get('auth.twitter.registration.enabled', false) ? 'checked' : '' ?>>
            <span style="font-weight:normal"><?= t('Allow automatic registration') ?></span>
        </label>
        </span>
    </div>
</div>
<div class='form-group registration-group'>
    <label for="registration_group" class="control-label"><?= t('Group to enter on registration') ?></label>
    <select name="registration_group" class="form-control">
        <option value="0"><?= t("None") ?></option>
        <?php
        /** @var \Group $group */
        foreach ($groups as $group) {
            ?>
            <option value="<?= $group->getGroupID() ?>" <?= intval($group->getGroupID(), 10) === intval(
                \Config::get('auth.twitter.registration.group', false),
                10) ? 'selected' : '' ?>>
                <?= $group->getGroupDisplayName(false) ?>
            </option>
        <?php

        }
        ?>
    </select>
</div>

<script type="text/javascript">

    (function RegistrationGroup() {

        var input = $('input[name="registration_enabled"]'),
            group_div = $('div.registration-group');

        input.change(function () {
            input.get(0).checked && group_div.show() || group_div.hide();
        }).change();

    }());

    var button = $('#showsecret');
    button.click(function() {
        var apisecret = $('#apisecret');
        if(apisecret.attr('type') == 'password') {
            apisecret.attr('type', 'text');
            button.html('<?php echo addslashes(t('Hide API secret'))?>');
        } else {
            apisecret.attr('type', 'password');
            button.html('<?php echo addslashes(t('Show API secret'))?>');
        }
    });
</script>
