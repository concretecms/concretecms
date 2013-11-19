<?php defined('C5_EXECUTE') or die('Access denied.');

$activeAuths = AuthenticationType::getActiveListSorted();
$form = Loader::helper('form');
?>
<style>
.authForm .row {
	margin-left:0;
}
.authForm .actions {
	margin-left:20px;
}
</style>
<div class='row'>
	<div class='span10 offset1'>
		
		<? if( $passwordChanged ){ ?>
			<div class="block-message info alert-message"><p><?=t('Password changed.  Please login to continue. ') ?></p></div>
		<? } ?> 
		<? if($changePasswordForm){ ?>
			<h2><?=t('Reset Password')?></h2>
			<div class="help-block"><?=t('Enter your new password below.') ?></div>
			<div class="ccm-form">	
				<form method="post" action="<?=$this->url( '/login', 'change_password', $uHash )?>"> 
			
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
		<? }elseif($validated) { ?>
			<h3><?=t('Email Address Verified')?></h3>
			
			<div class="success alert-message block-message">
				<p>
				<?=t('The email address <b>%s</b> has been verified and you are now a fully validated member of this website.', $uEmail)?>
				</p>
				<div class="alert-actions"><a class="btn small" href="<?=$this->url('/')?>"><?=t('Continue to Site')?></a></div>
			</div>
		<? } else { ?>
			<div class="page-header">
				<h1><?=t('Sign in to %s', SITE)?></h1>
			</div>
			<?php
			if($authType instanceof AuthenticationType && strlen($authTypeElement)) {
				$authType->renderForm($authTypeElement);
			} else {
				if (count($activeAuths) > 1) {
					?>
					<ul class="nav nav-tabs">
						<?php
						$first = true;
						foreach ($activeAuths as $auth) {
							?>
							<li<?=$first?" class='active'":''?>>
								<a data-authType='<?=$auth->getAuthenticationTypeHandle()?>' href='#<?=$auth->getAuthenticationTypeHandle()?>'><?=$auth->getAuthenticationTypeName()?></a>
							</li>
							<?php
							$first = false;
						}
						?>
					</ul>
					<?php
				}
				?>
				<div class='authTypes row'>
					<?php
					$first = true;
					foreach ($activeAuths as $auth) {
						?>
						<div data-authType='<?=$auth->getAuthenticationTypeHandle()?>' style='<?=$first?"display:block":"display:none"?>'>
							<fieldset>
								<form method='post' class='form-horizontal' action='<?=$this->action('authenticate', $auth->getAuthenticationTypeHandle())?>'>
									<div class='authForm'>
										<?$auth->renderForm()?>
									</div>
								</form>
							</fieldset>
						</div>
						<?php
						$first = false;
					}
					?>
				</div>
			<? } ?>
		<? } ?>
	</div>
</div>
<script type="text/javascript">
(function($){
	"use strict";
	$('ul.nav.nav-tabs > li > a').on('click',function(){
		var me = $(this);
		if (me.parent().hasClass('active')) return false;
		$('ul.nav.nav-tabs > li.active').removeClass('active');
		var at = me.attr('data-authType');
		me.parent().addClass('active');
		$('div.authTypes > div').hide().filter('[data-authType="'+at+'"]').show();
		return false;
	});
})(jQuery);
</script>
