<?php
defined('C5_EXECUTE') or die('Access Denied');
?>
<div class="form-group">
            <span>
                <?= t('Attach a %s account', t('twitter')) ?>
            </span>
    <hr>
</div>
<div class="form-group">
</div>


<div class="form-group">
    <?php
    if ($attached) {
        ?>
        <div class="btn-group">
            <a href="<?= \URL::to('/system/authentication/twitter/attempt_attach'); ?>"
               class="btn btn-primary btn-twitter target="_blank"">
                <i class="fa fa-twitter"></i>
                <?= t('Attach a %s account', t('twitter')) ?>
            </a>
            <a href="<?= \URL::to(
                '/login/callback/twitter/handle_detach',
                id(new \Concrete\Core\Validation\CSRF\Token)->generate('oauth_detach')); ?>"
               class="btn btn-danger" target="_blank">
                <?= t('Detach') ?>
            </a>
        </div>
    <?php
    } else {
        ?>
        <a href="<?= \URL::to('/system/authentication/twitter/attempt_attach'); ?>"
           class="btn btn-primary btn-twitter" target="_blank">
            <i class="fa fa-twitter"></i>
            <?= t('Attach a %s account', t('twitter')) ?>
        </a>
    <?php
    }
    ?>
</div>

<style>
    .ccm-ui .btn-twitter {
        border-color: transparent;
        background: #00aced;
    }

    .btn-twitter .fa-twitter {
        margin: 0 6px 0 3px;
    }
</style>
