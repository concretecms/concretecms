<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="form-group">
        <span>
            <?= t('Attach a %s account', t('google')) ?>
        </span>
    <hr>
</div>
<div class="form-group">
    <a href="<?= \URL::to('/system/authentication/google/attempt_attach'); ?>" class="btn btn-primary btn-google">
        <i class="fa fa-google"></i>
        <?= t('Attach a %s account', t('google')) ?>
    </a>
</div>

<style>
    .ccm-ui .btn-facebook {
        border-width: 0px;
        background: #3b5998;
    }
    .btn-facebook .fa-facebook {
        margin: 0 6px 0 3px;
    }
</style>
