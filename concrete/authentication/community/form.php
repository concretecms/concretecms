<?php
if (isset($error)) {
    ?>
    <div class="alert alert-danger"><?= $error; ?></div>
    <?php
}
if (isset($message)) {
    ?>
    <div class="alert alert-success"><?= $message; ?></div>
<?php
}
?>

<div class="form-group external-auth-option">
    <div class="d-grid">
        <a href="<?= \URL::to('/ccm/system/authentication/oauth2/community/attempt_auth');
        ?>" class="btn btn-primary btn-community"
           title="<?= t('Join the Concrete community to setup multiple websites, shop for extensions, and get support.'); ?>">
            <img src="<?= Core::getApplicationURL(); ?>/concrete/images/logo.svg" class="concrete-icon"></i>
            <?= t('Sign in with community.concretecms.com'); ?>
        </a>
    </div>
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

    .ccm-concrete-authentication-type-svg > svg {
      width: 16px;
    }

    img.concrete-icon {
        width: 20px;
        margin-right:5px;
    }
</style>
