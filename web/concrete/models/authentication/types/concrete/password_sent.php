<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>
<div class='forgotPassword'>
	<h2><?=t('Forgot Your Password?')?></h2>
	<div class="ccm-message"><?=$intro_msg?></div>
	<div class='help-block'>
		<?=t('An email containing instructions on resetting your password has been sent to your account address.')?>
	</div>
</div>