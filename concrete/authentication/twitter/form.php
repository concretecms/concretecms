<?php
defined('C5_EXECUTE') or die('Access Denied');
if (isset($error)) {
    ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php

}
if (isset($message)) {
    ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php

}

if (isset($show_email) && $show_email) {
    ?>
    <form action="<?= \URL::to('/login/callback/twitter/handle_register') ?>">
        <span><?= t('Register an account for "%s"', "@{$username}") ?></span>
        <hr />
        <div class="input-group">
            <input type="email" name="uEmail" placeholder="email" class="form-control" />
            <span class="input-group-btn">
                <button class="btn btn-primary"><?= t('Register') ?></button>
            </span>
        </div>
        <?=$token->output('twitter_register')?>
    </form>
    <?php

} else {
    if ($user->isLoggedIn()) {
        ?>

        <?php if ($authenticationType->isHooked($user)):
            ?>
            <div class="form-group">
        <span>
            <?= t('Detach your %s account', t('twitter')) ?>
        </span>
                <hr>
            </div>
            <div class="form-group">
                <a href="<?= \URL::to('/ccm/system/authentication/oauth2/twitter/attempt_detach');
                ?>" class="btn btn-primary btn-twitter btn-block">
                    <i class="fa fa-twitter"></i>
                    <?= t('Detach your %s account', t('twitter')) ?>
                </a>
            </div>

        <?php else: ?>
            <div class="form-group">
            <span>
                <?= t('Attach a %s account', t('twitter')) ?>
            </span>
                <hr>
            </div>
            <div class="form-group">
                <a href="<?= \URL::to('/ccm/system/authentication/oauth2/twitter/attempt_attach');
                ?>"
                   class="btn btn-primary btn-twitter btn-block">
                    <i class="fa fa-twitter"></i>
                    <?= t('Attach a %s account', t('twitter')) ?>
                </a>
            </div>
        <?php endif;

    } else {
        ?>
        <div class="form-group">
            <span>
                <?= t('Sign in with %s', t('twitter')) ?>
            </span>
            <hr>
        </div>
        <div class="form-group">
            <a href="<?= \URL::to('/ccm/system/authentication/oauth2/twitter/attempt_auth');
            ?>"
               class="btn btn-primary btn-twitter btn-block">
                <i class="fa fa-twitter"></i>
                <?= t('Log in with %s', 'twitter') ?>
            </a>
        </div>
        <?php

    }

}
?>
    <div class="form-group">
        <a href="<?= \URL::to('/') ?>" class="btn btn-success btn-block">
            <?= t('Return to Home Page')?>
        </a>
    </div>
<?php
    ?>
    <style>
        .ccm-ui .btn-twitter {
            border-width: 0px;
            background: #00aced;
        }

        .btn-twitter .fa-twitter {
            margin: 0 6px 0 3px;
        }
    </style>
