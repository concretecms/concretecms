<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>
<h2><?=t('Reset Password')?></h2>
<div class="help-block"><?=t('Enter your new password below.') ?></div>
<div class="ccm-form">	
	<form method="post" action="<?=View::url('/login', 'callback', $authType->getAuthenticationTypeHandle(), 'change_password', $uHash)?>"> 
		<div class="control-group">
		<label for="uPassword" class="control-label"><?=t('New Password')?></label>
		<div class="controls">
			<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
		</div>
		</div>
		<div class="control-group">
		<label for="uPasswordConfirm"  class="control-label"><?=t('Confirm Password')?></label>
		<div class="controls">
			<input type="password" name="uPasswordConfirm" id="uPasswordConfirm" class="ccm-input-text">
		</div>
		</div>

		<div class="actions">
		<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
		</div>
	</form>			
</div>