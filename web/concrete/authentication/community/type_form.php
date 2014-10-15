<?php defined('C5_EXECUTE') or die('Access denied.'); ?>
<div class="alert alert-info">
    <h4><?php echo t('Community Authentication Configuration'); ?></h4>
    <p><?php echo t('<a href="%s" target="_blank">Click here</a> to obtain your access keys.', \Config::get('concrete.urls.concrete5_secure') . '/profile/apps/'); ?></p>
    <p><?php echo t('Set the "Callback URL" to: %s.', '<code>' . BASE_URL . DIR_REL . \URL::to('/system/authentication/community/callback') . '</code>'); ?></p>
</div>

<div class='form-group'>
    <?=$form->label('apikey', t('App ID'))?>
    <?=$form->text('apikey', $apikey)?>
</div>
<div class='form-group'>
    <?=$form->label('apisecret', t('App Secret'))?>
    <div class="input-group">
        <?=$form->password('apisecret', $apisecret)?>
        <span class="input-group-btn">
        <button id="showsecret" class="btn btn-warning" type="button"><?php echo t('Show secret key')?></button>
      </span>
    </div>
</div>
<div class='form-group'>
    <div class="input-group">
        <label type="checkbox">
            <input type="checkbox" name="registration_enabled" value="1" <?= \Config::get('auth.community.registration_enabled', false) ? 'checked' : '' ?>>
            <span style="font-weight:normal"><?= t('Allow automatic registration') ?></span>
        </label>
      </span>
    </div>
</div>

<script type="text/javascript">
    var button = $('#showsecret');
    button.click(function() {
        var apisecret = $('#apisecret');
        if(apisecret.attr('type') == 'password') {
            apisecret.attr('type', 'text');
            button.html('<?php echo addslashes(t('Hide secret key'))?>');
        } else {
            apisecret.attr('type', 'password');
            button.html('<?php echo addslashes(t('Show secret key'))?>');
        }
    });
</script>
