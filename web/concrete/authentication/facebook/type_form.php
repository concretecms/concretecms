<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class='form-group'>
    <?=$form->label('apikey', t('Api Key'))?>
    <?=$form->text('apikey', $apikey)?>
</div>
<div class='form-group'>
    <?=$form->label('apisecret', t('Api Secret'))?>
    <?=$form->text('apisecret', $apisecret)?>
</div>

<div class="alert alert-info">
    <?php echo t('<a href="%s" target="_blank">Click here</a> to obtain your access keys.', 'https://developers.facebook.com/apps/'); ?>
</div>