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

<h4><?= t('Domain Filtering') ?></h4>
<p><?= t('Google allows accounts be created against custom domains like "example.com". ' .
         'These lists allow you to use standard PHP regular expressions to filter against the domain name or the google app domain account. ' .
         'For example user@example.com would filter against "example.com".') ?></p>

<div class="form-group">
    <label for="whitelist">
        <?= t('Domain Whitelist regex') ?>
    </label>
    <span class="help-block"><?= t('One per line, to whitelist all %s domains: %s', '<code>concrete5.org</code>', '<code>~^concrete5\\.org$i~</code>') ?></span>
    <textarea type="text" name="whitelist" class="form-control"><?= implode(PHP_EOL, (array) $whitelist) ?></textarea>
</div>

<div class="form-group">
    <label for="whitelist">
        <?= t('Domain Blacklist regex') ?>
    </label>
    <span class="help-block"><?= t('One per line') ?></span>
    <span class="help-block"><?= t('Format: %s.', sprintf('<code>[ "~%s~i", "%s" ]</code>', t('Regex'), t('Error Message'))) ?></span>
    <span class="help-block"><?= t('To disallow everything other than whitelist: %s.', sprintf('<code>[ "~.*~", "%s" ]</code>', t('Invalid domain.'))) ?></span>
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
