<?php
defined('C5_EXECUTE') or die("Access Denied.");

$subject = SITE.' '.t('Registration Approved');

/**
 * HTML BODY START
 */
ob_start();

?>
<h2><?= t('Welcome to') ?> <?= SITE ?></h2>
<?= t("Your registration has been approved. You can log into your new account here") ?>:<br />
<br />
<a href="<?= BASE_URL.View::url('/login') ?>"><?= BASE_URL.View::url('/login') ?></a>
<?

$bodyHTML = ob_get_clean();
/**
 * HTML BODY END
 *
 * =====================
 *
 * PLAIN TEXT BODY START
 */
ob_start();

?>
<?= t('Welcome to') ?> <?= SITE ?>

<?= t("Your registration has been approved. You can log into your new account here") ?>:

<?= BASE_URL.View::url('/login') ?>
<?

$body = ob_get_clean();
/**
 * PLAIN TEXT BODY END
 */
