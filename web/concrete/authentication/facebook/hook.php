<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="form-group">
        <span>
            <?= t('Attach a %s account', t('facebook')) ?>
        </span>
    <hr>
</div>
<div class="form-group">
    <?php
    if ($attached) {
        ?>
        <div class="btn-group">
            <a href="<?= \URL::to('/system/authentication/facebook/attempt_attach'); ?>"
               class="btn btn-primary btn-facebook" target="_blank">
                <i class="fa fa-facebook"></i>
                <?= t('Attach a %s account', t('facebook')) ?>
            </a>
            <a href="<?= \URL::to(
                '/login/callback/facebook/handle_detach',
                id(new \Concrete\Core\Validation\CSRF\Token)->generate('oauth_detach')); ?>"
               class="btn btn-danger" target="_blank">
                <?= t('Detach') ?>
            </a>
        </div>
    <?php
    } else {
        ?>
        <a href="<?= \URL::to('/system/authentication/facebook/attempt_attach'); ?>"
           class="btn btn-primary btn-facebook" target="_blank">
            <i class="fa fa-facebook"></i>
            <?= t('Attach a %s account', t('facebook')) ?>
        </a>
    <?php
    }
    ?>
</div>

<style>
    .ccm-ui .btn-facebook {
        border-color: transparent;
        background: #3b5998;
    }

    .btn-facebook .fa-facebook {
        margin: 0 6px 0 3px;
    }
</style>
