
<div class="form-group">
        <span>
            <?= t('Attach a community account') ?>
        </span>
    <hr>
</div>
<div class="form-group">
    <a href="<?= \URL::to('/ccm/system/authentication/oauth2/community/attempt_attach'); ?>" class="btn btn-primary btn-community">
        <img src="<?= \Core::getApplicationURL() ?>/concrete/images/logo.svg" class="concrete5-icon" />
        <?= t('Attach a concrete5.org account') ?>
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

    img.concrete5-icon {
        width: 20px;
        margin-right:5px;
    }
</style>
