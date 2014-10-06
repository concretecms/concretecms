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

$user = new User;

if ($user->isLoggedIn()) {
    ?>
    <a href="<?= \URL::to('/system/authentication/twitter/attempt_attach'); ?>">
        <?php echo t('Attach a twitter account')?>
    </a>
    <?php
} else {
    ?>
    <a href="<?= \URL::to('/system/authentication/twitter/attempt_auth'); ?>">
        <?php echo t('Login With Twitter')?>
    </a>
    <?php
}
