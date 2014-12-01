<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

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
            <input type="checkbox" name="registration_enabled" value="1" <?= \Config::get('auth.google.registration_enabled', false) ? 'checked' : '' ?>>
            <span style="font-weight:normal"><?= t('Allow automatic registration') ?></span>
        </label>
        </span>
    </div>
</div>

<div class="form-group">
    <label for="whitelist">
        <?= t('Whitelist regex') ?>
    </label>
    <span class="help-block"><?= t('One per line, whitelist entries are run before blacklist entries.') ?></span>
    <textarea type="text" name="whitelist" class="form-control"><?= implode(PHP_EOL, (array) $whitelist) ?></textarea>
</div>

<div class="form-group">
    <label for="whitelist">
        <?= t('Blacklist regex') ?>
    </label>
    <span class="help-block"><?= t('One per line, Format: %s.', sprintf('<code>[ "~%s~i", "%s" ]</code>', t('Regex'), t('Error Message'))) ?></span>
    <textarea type="text" name="blacklist" class="form-control"><?= implode(PHP_EOL, $blacklist) ?></textarea>
</div>

<div class="alert alert-info">
    <?php echo t('<a href="%s" target="_blank">Click here</a> to obtain your access keys.', 'https://console.developers.google.com/project'); ?>
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
