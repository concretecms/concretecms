<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>

<form method='post'
      action='<?= View::url('/login', 'authenticate', $this->getAuthenticationTypeHandle()) ?>'>
    <div class="form-group">
        <input name="uName" class="form-control col-sm-12"
               placeholder="<?=Config::get('concrete.user.registration.email_registration') ? t('Email Address') : t('Username')?>" />
    </div>

    <div class="form-group">
        <label>&nbsp;</label>
        <input name="uPassword" class="form-control" type="password"
               placeholder="<?=t('Password')?>" />
    </div>

    <div class="checkbox">
        <label style="font-weight:normal">
            <input type="checkbox" name="uMaintainLogin" value="1">
            <?= t('Stay signed in for two weeks') ?>
        </label>
    </div>

    <?php
    if (isset($locales) && is_array($locales) && count($locales) > 0) {
        ?>
        <div class="form-group">
            <label for="USER_LOCALE" class="control-label"><?= t('Language') ?></label>
            <?= $form->select('USER_LOCALE', $locales) ?>
        </div>
    <?
    }
    ?>

    <div class="form-group">
        <button class="btn btn-primary"><?= t('Log in') ?></button>
        <a href="<?=View::url('/login', 'concrete', 'forgot_password')?>" class="btn pull-right"><?= t('Forgot Password') ?></a>
    </div>

    <script type="text/javascript">
        document.querySelector('input[name=uName]').focus();
    </script>
    <?php Loader::helper('validation/token')->output('login_' . $this->getAuthenticationTypeHandle()); ?>

    <?php if (Config::get('concrete.user.registration.enabled')) { ?>
        <br/>
        <hr/>
        <a href="<?=URL::to('/register')?>" class="btn btn-block btn-success"><?=t('Not a member? Register')?></a>
    <?php } ?>
</form>
