<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class="resetPassword">
    <form method="post"
          action="<?= URL::to('/login', 'callback', $authType->getAuthenticationTypeHandle(), 'reset_password') ?>">
        <h4><?= t('Reset Your Password') ?></h4>
        <div class="ccm-message"></div>
        <div class='help-block'>
            <?= isset($intro_msg) ? $intro_msg : 'Your user account is being upgraded and requires a new password. Please enter your email address below to create this now.' ?>
        </div>
        <div class="form-group">
            <input name="uEmail" type="email" placeholder="<?= t('Email Address') ?>" class="form-control"/>
        </div>
        <button name="resetPassword" class="btn btn-primary btn-block"><?= t('Reset and Email Password') ?></button>
    </form>
</div>
