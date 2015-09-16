<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>
<div class='forgotPassword'>
    <?php if($validated || $workflowPending) : ?>
        <h4><?= t('Email Address Verified') ?></h4>

        <div class='help-block'>
            <?= $workflowPending ? t('The email address <b>%s</b> has been verified. Your account is currently pending for activation. After your account has been activated, you become a member of this website and are able to login.', $uEmail) : t('The email address <b>%s</b> has been verified and you are now a fully validated member of this website.', $uEmail)?>
        </div>
        <a href="<?= \URL::to('/') ?>" class="btn btn-block btn-primary">
            <?= t('Continue') ?>
        </a>
    <?php endif; ?>
</div>
