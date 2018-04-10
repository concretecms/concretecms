<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class="forgotPassword">
	<form method="post" action="<?= URL::to('/login', 'callback', $authType->getAuthenticationTypeHandle(), 'forgot_password') ?>">
		<?php $token->output(); ?>
		<h4><?= t('Forgot Your Password?') ?></h4>
		<div class="ccm-message"><?= isset($intro_msg) ? $intro_msg : '' ?></div>
		<div class='help-block'>
			<?= t('Enter your email address below. We will send you instructions to reset your password.') ?>
		</div>
		<div class="form-group">
			<label class="control-label" for="uEmail"><?= t('Email Address') ?></label>
			<input name="uEmail" type="email" id="uEmail" class="form-control" />
		</div>
		<button name="resetPassword" class="btn btn-primary btn-block"><?= t('Reset and Email Password') ?></button>
	</form>
</div>
