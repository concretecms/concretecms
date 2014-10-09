<?php
use Concrete\Core\Validation\CSRF\Token;

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
        <span>Register an account for "@<?= $username ?>"</span>
        <hr />
        <div class="input-group">
            <input type="email" name="uEmail" placeholder="email" class="form-control" />
            <span class="input-group-btn">
                <button class="btn btn-primary"><?= t('Register') ?></button>
            </span>
        </div>
        <?= id(new Token)->output('twitter_register'); ?>
    </form>
    <?php
} else {

    $user = new User;

    if ($user->isLoggedIn()) {
        ?>
        <a href="<?= \URL::to('/system/authentication/twitter/attempt_attach'); ?>">
            <?php echo t('Attach a twitter account') ?>
        </a>
    <?php
    } else {
        ?>
        <a href="<?= \URL::to('/system/authentication/twitter/attempt_auth'); ?>">
            <?php echo t('Log in With Twitter') ?>
        </a>
    <?php
    }
}
