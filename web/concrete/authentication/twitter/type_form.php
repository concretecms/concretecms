<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class='form-group'>
    <?php echo $form->label('apikey', t('Api Key'))?>
    <?php echo $form->text('apikey', $apikey)?>
</div>
<div class='form-group'>
    <?php echo $form->label('apisecret', t('Api Secret'))?>
    <?php echo $form->text('apisecret', $apisecret)?>
</div>

<div class="alert alert-info">
    <?php echo t('<a href="%s" target="_blank">Click here</a> to obtain your access keys.', 'https://apps.twitter.com/'); ?>
</div>
