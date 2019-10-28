<?php

defined('C5_EXECUTE') or die('Access denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\User\Group\Group[] $groups
 * @var string $apikey
 * @var string $apisecret
 * @var string[] $blacklist
 * @var string[] $whitelist
 */
?>

<div class='form-group'>
    <?= $form->label('apikey', t('App ID')) ?>
    <?= $form->text('apikey', $apikey) ?>
</div>
<div class='form-group'>
    <?= $form->label('apisecret', t('App Secret')) ?>
    <div class="input-group">
        <?= $form->password('apisecret', $apisecret, array('autocomplete' => 'off')) ?>
        <span class="input-group-btn">
        <button id="showsecret" class="btn btn-warning" type="button"><?php echo t('Show secret key') ?></button>
      </span>
    </div>
</div>
<div class='form-group'>
    <div class="input-group">
        <label>
            <?= $form->checkbox('registration_enabled', '1', Config::get('auth.google.registration.enabled')) ?>
            <span style="font-weight:normal"><?= t('Allow automatic registration') ?></span>
        </label>
    </div>
</div>
<div class='form-group registration-group'>
    <label for="registration_group" class="control-label"><?= t('Group to enter on registration') ?></label>
    <?php
    $optionValues = [
        '0' => t('None'),
    ];
    foreach ($groups as $group) {
        $optionValues[$group->getGroupID()] = $group->getGroupDisplayName(false);
    }
    echo $form->select('registration_group', $optionValues, (int) Config::get('auth.google.registration.group'));
    ?>
</div>

<h4><?= t('Domain Filtering') ?></h4>
<p><?= t(
        'Google allows accounts be created against custom domains like "example.com". ' .
        'These lists allow you to use standard PHP regular expressions to filter against the domain name or email address. ' .
        'For example user@example.com would filter against "example.com".') ?></p>

<div class="form-group">
    <?= $form->label('whitelist', t('Domain Whitelist regex')) ?>
    <span class="help-block"><?= t(
            'One per line, to whitelist all %s domains: %s',
            '<code>concrete5.org</code>',
            '<code>~^concrete5\\.org$~i</code>') ?></span>
    <?= $form->textarea('whitelist', implode("\n", (array) $whitelist)) ?>
</div>

<div class="form-group">
    <?= $form->label('blacklist', t('Domain Blacklist regex')) ?>
    <span class="help-block"><?= t('One per line') ?></span>
    <span class="help-block"><?= t(
            'Format: %s.',
            sprintf('<code>[ "~%s~i", "%s" ]</code>', t('Regex'), t('Error Message'))) ?></span>
    <span class="help-block"><?= t(
            'To disallow everything other than whitelist: %s.',
            sprintf('<code>[ "~.*~", "%s" ]</code>', t('Invalid domain.'))) ?></span>
    <?= $form->textarea('blacklist', implode("\n", $blacklist)) ?>
</div>

<div class="alert alert-info">
    <?php echo t(
        '<a href="%s" target="_blank">Click here</a> to obtain your access keys.',
        'https://console.developers.google.com/project'); ?>
</div>

<script type="text/javascript">
    (function () {

        (function RegistrationGroup() {

            var input = $('input[name="registration_enabled"]'),
                group_div = $('div.registration-group');

            input.change(function () {
                input.get(0).checked && group_div.show() || group_div.hide();
            }).change();

        }());


        var button = $('#showsecret');
        button.click(function () {
            var apisecret = $('#apisecret');
            if (apisecret.attr('type') == 'password') {
                apisecret.attr('type', 'text');
                button.html('<?php echo addslashes(t('Hide secret key'))?>');
            } else {
                apisecret.attr('type', 'password');
                button.html('<?php echo addslashes(t('Show secret key'))?>');
            }
        });
    }());
</script>
