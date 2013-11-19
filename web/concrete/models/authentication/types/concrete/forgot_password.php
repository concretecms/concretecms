<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>
<div class='forgotPassword'>
	<h2><?=t('Forgot Your Password?')?></h2>
	<div class="ccm-message"><?=$intro_msg?></div>
	<div class='help-block'>
		<?=t('Enter your email address below. We will send you instructions to reset your password.')?>
	</div>
	<form method="post" action="<?=View::url('/login', 'callback', $authType->getAuthenticationTypeHandle(), 'forgot_password')?>" class="form-horizontal">
		<div class='control-group'>
			<label class='control-label' for='uEmail'><?=t('Email Address')?></label>
			<div class='controls'>
				<?=$form->text('uEmail')?>
			</div>
		</div>
		<div class='actions'>
			<?=$form->submit('resetPassword','Reset and Email Password')?>
		</div>
	</form>
</div>