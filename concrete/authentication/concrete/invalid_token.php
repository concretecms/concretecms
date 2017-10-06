<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class="forgotPassword">
	<h2><?= t('Unable to validate token') ?></h2>
	<div class="help-block">
		<?= t('The token you provided doesn\'t appear to be valid. Please paste the url exactly as it appears in the email or visit the forgot password page again to have a new token generated.') ?>
	</div>
	<a href="<?= URL::to('/login/callback/concrete') ?>" class="btn btn-block btn-primary">
		<?= t('Continue') ?>
	</a>
</div>
