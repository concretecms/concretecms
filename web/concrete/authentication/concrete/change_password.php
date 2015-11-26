<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Core::make('helper/form');
?>

<h4><?= t('Reset Password') ?></h4>
<div class="help-block"><?= t('Enter your new password below.') ?></div>
<div class="change-password">
	<form method="post" action="<?= URL::to('/login', 'callback', $authType->getAuthenticationTypeHandle(), 'change_password', $uHash) ?>">
		<div class="form-group">
			<label class="control-label" for="uPassword"><?= t('New Password') ?></label>
			<input type="password" name="uPassword" id="uPassword" class="form-control" autocomplete="off"/>
		</div>
		<div class="form-group">
			<label class="control-label" for="uPassword"><?= t('Confirm New Password') ?></label>
			<input type="password" name="uPasswordConfirm" id="uPasswordConfirm" class="form-control" autocomplete="off"/>
		</div>
		<div class="form-group">
			<button class="btn btn-primary"><?= t('Change password and sign in') ?></button>
		</div>
	</form>
</div>
