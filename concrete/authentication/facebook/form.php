<?php
/* @var $user \Concrete\Core\User\User;
 * @var $authenticationType \Concrete\Core\Authentication\AuthenticationType
 */


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
    <form action="<?= \URL::to('/login/callback/facebook/handle_register') ?>">
        <?php // It's best to show full name here for regional variations
        if (isset($fullName) && !empty($fullName)) {?>
        <span><?= t('Register an account for "%s"', $fullName) ?></span>
        <?php  } else {
            ?>
        <span><?= t('Register an account for "%s"', $username) ?></span>
            <?php
        }?>
        <hr />
        <div class="input-group">
            <input type="email" name="uEmail" placeholder="email" class="form-control" />
            <span class="input-group-btn">
                <button class="btn btn-primary"><?= t('Register') ?></button>
            </span>
        </div>
        <?=$token->output('facebook_register')?>
    </form>
    <?php

} else {

    if ($user->isLoggedIn()) {
        ?>

        <?php if ($authenticationType->isHooked($user)):
            ?>
            <div class="form-group">
        <span>
            <?= t('Detach your %s account', t('facebook')) ?>
        </span>
                <hr>
            </div>
            <div class="form-group">
                <a href="<?= \URL::to('/ccm/system/authentication/oauth2/facebook/attempt_detach');
                ?>" class="btn btn-primary btn-facebook btn-block">
                    <i class="fa fa-facebook"></i>
                    <?= t('Detach your %s account', t('facebook')) ?>
                </a>
            </div>

        <?php else: ?>

            <div class="form-group">
            <span>
                <?= t('Attach a %s account', t('facebook')) ?>
            </span>
                <hr>
            </div>
            <div class="form-group">
                <a href="<?= \URL::to('/ccm/system/authentication/oauth2/facebook/attempt_attach');
                ?>" class="btn btn-primary btn-facebook btn-block">
                    <i class="fa fa-facebook"></i>
                    <?= t('Attach a %s account', t('facebook')) ?>
                </a>
            </div>

        <?php endif; ?>

        <?php

    } else {
        ?>
        <div class="form-group">
        <span>
            <?= t('Sign in with %s', t('facebook')) ?>
        </span>
            <hr>
        </div>
        <div class="form-group">
            <a href="<?= \URL::to('/ccm/system/authentication/oauth2/facebook/attempt_auth');
            ?>" class="btn btn-primary btn-facebook btn-block">
                <i class="fa fa-facebook"></i>
                <?= t('Log in with %s', 'facebook') ?>
            </a>
        </div>
        <?php
    }
    ?>
    <div class="form-group">
        <a href="<?= \URL::to('/') ?>" class="btn btn-success btn-block">
            <?= t('Return to Home Page')?>
        </a>
    </div>
    <?php
}
?>
<style>
    .ccm-ui .btn-facebook {
        border-width: 0px;
        background: #3b5998;
    }
    .btn-facebook .fa-facebook {
        margin: 0 6px 0 3px;
    }
</style>
