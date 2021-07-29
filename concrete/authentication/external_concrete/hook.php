<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class="form-group">
        <span>
            <?= t('Attach your %s account', h($name)) ?>
        </span>
    <hr>
</div>
<div class="form-group">
    <a href="<?= $attachUrl ?>" class="btn btn-primary btn-success btn-external-concrete">
        <img src="<?= $assetUrl ?>/concrete/images/logo.svg" class="concrete-icon" />
        <?= t('Attach your %s account', h($name)) ?>
    </a>
</div>

<style>
    .ccm-ui .btn-community {
        border-width: 0px;
        background: rgb(31,186,232);
        background: -moz-linear-gradient(top, rgba(31,186,232,1) 0%, rgba(18,155,211,1) 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(31,186,232,1)), color-stop(100%,rgba(18,155,211,1)));
        background: -webkit-linear-gradient(top, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        background: -o-linear-gradient(top, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        background: -ms-linear-gradient(top, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        background: linear-gradient(to bottom, rgba(31,186,232,1) 0%,rgba(18,155,211,1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1fbae8', endColorstr='#129bd3',GradientType=0 );
    }

    img.concrete-icon {
        width: 20px;
        margin-right:5px;
    }
</style>
