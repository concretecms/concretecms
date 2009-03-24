<?

Loader::library('view');
Loader::library('authentication/open_id');
Loader::model('user_attributes');
$form=Loader::helper('form');

if( $_REQUEST['remote'] ){ 
	$loginFormSubmitURL=REL_DIR_FILES_TOOLS_REQUIRED.'/support/remote_auth_proxy/?action=do_login';
	$forgotPasswordFormSubmitURL=REL_DIR_FILES_TOOLS_REQUIRED.'/support/remote_auth_proxy/?action=forgot_password';
	$registerFormSubmitURL=REL_DIR_FILES_TOOLS_REQUIRED.'/support/remote_auth_proxy/?action=do_register';
}else{
	$loginFormSubmitURL=View::url('/login', 'do_login');
	$forgotPasswordFormSubmitURL=View::url('/login', 'forgot_password');
	$registerFormSubmitURL=View::url('/register', 'do_register');
}
?>

<div id="ccm-popupAuth">

	<div id="ccm-popupLoginWrap" class="ccm-form">
	
		<div id="ccm-popupLoginIntroMsg" ></div>
	
		<div id="ccm-popupLoginMsg" class="ccm-popupMsg" style="margin-bottom:16px"></div>
	
		<form id="popupLoginForm" method="post" action="<?=$loginFormSubmitURL ?>" onSubmit="return ccmPopupLogin.login(this);">
		
			<input name="format" type="hidden" value="JSON">
		
			<div class="ccm-fieldPair">
				<label for="uName"><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>
					<?=t('Email Address')?>
				<? } else { ?>
					<?=t('Username')?>
				<? } ?>
				</label>
				<input type="text" name="uName" id="uName" <?= (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
				<div class="ccm-spacer"></div>
			</div> 
				
			<div class="ccm-fieldPair">
				<label for="uPassword"><?=t('Password')?></label>
				<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
				<div class="ccm-spacer"></div>
			</div>
		
			<hr />
			
			<? if (OpenIDAuth::isEnabled()) { ?>
				<div class="ccm-fieldPair">
					<label for="uOpenID"><?=t('Or login using an OpenID')?>:</label><br/>
					<input type="text" name="uOpenID" id="uOpenID" <?= (isset($uOpenID)?'value="'.$uOpenID.'"':'');?> class="ccm-input-openid">
					<div class="ccm-spacer"></div>
				</div>
			<? } ?>
			
			<? if( !$_REQUEST['remote'] ){ ?>
			<div class="ccm-fieldPair">
				<?=$form->checkbox('uMaintainLogin', 1)?>
				<label id="uMaintainLoginLabel" for="uMaintainLogin"><?=t('Remember Me')?></label> 			
			</div>
			<? } ?>
			
			<div class="ccm-spacer"></div>
			
			<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
			
			<?=$form->hidden('rcID', $rcID); ?>	
		</form> 
		
		<div class="ccm-spacer"></div>
		
		<div class="" style="margin-top:16px">
			<a onClick="ccmPopupLogin.toggleForgot()"><?=t('Forgot your password?')?></a>
		
			<? if(ENABLE_REGISTRATION){ ?>
				 &nbsp;|&nbsp; <a onClick="ccmPopupLogin.toggleRegister()"><?=t('Register a new account.')?></a> 	
			<? } ?>
		</div>	
	</div>
	
	
	
	<div id="ccm-popupForgotPasswordWrap" class="ccm-form" style="display:none">
	
		<h2><?=t('Forgot Your Password?')?></h2>
		
		<p><?=t("If you've forgotten your password, enter your email address below. We will reset it to a new password, and send the new one to you.")?></p>
		
		<a name="forgot_password"></a>
		
		<div id="ccm-popupForgotMsg" class="ccm-popupMsg" style="margin-bottom:8px"></div>	
			
		<form id="popupForgotPasswordForm" method="post" action="<?=$forgotPasswordFormSubmitURL?>" onSubmit="return ccmPopupLogin.submitForgotPassword(this);">
			
			<input name="format" type="hidden" value="JSON">
			
			<label for="uEmail"><?=t('Email Address')?></label> 
			<?=$form->hidden('rcID', $rcID); ?>
			<input type="text" name="uEmail" value="" class="ccm-input-text" >
		 
		 	<div class="ccm-spacer"></div>
		 
			<?=$form->submit('submit', t('Reset and Email Password') . ' &gt;')?>
			
		</form>
		
		<div class="ccm-spacer"></div>
		
		<div style="margin-top:16px"><a onClick="ccmPopupLogin.toggleForgot()"><?=t('&laquo; Return to login')?></a></div>
	
	</div>
	
	
	
	<? if(ENABLE_REGISTRATION){ ?>
	<div id="ccm-popupRegisterWrap" class="ccm-form" style="display:none">
	
		<h2><?=t('Register')?></h2>
		
		<div id="ccm-popupRegisterMsg" class="ccm-popupMsg" style="margin-bottom:16px"></div>
		
		<form id="popupRegisterForm" method="post" action="<?=$registerFormSubmitURL ?>" onSubmit="return ccmPopupLogin.submitRegister(this);">
		
			<input name="format" type="hidden" value="JSON">
		
			<? if (!USER_REGISTRATION_WITH_EMAIL_ADDRESS) { ?>
				<div class="ccm-fieldPair">
					<?=$form->label('uName', t('Username') )?>
					<?=$form->text('uName')?>
					<div class="ccm-spacer"></div>
				</div>
			<? } ?>
			
			<div class="ccm-fieldPair">
				<?=$form->label('uEmail', t('Email Address') )?>
				<?=$form->text('uEmail')?>
				<div class="ccm-spacer"></div>
			</div>
			
			<div class="ccm-fieldPair">
			<?=$form->label('uPassword', t('Password') )?>
			<?=$form->password('uPassword')?>
			<div class="ccm-spacer"></div>
			</div>
			
			<div class="ccm-fieldPair">
			<?=$form->label('uPasswordConfirm', t('Confirm Password') )?>
			<?=$form->password('uPasswordConfirm')?>
			<div class="ccm-spacer"></div>
			</div>
			
			<?
			
			$attribs = UserAttributeKey::getRegistrationList();
			foreach($attribs as $ak) { 
				if ($ak->getKeyType() == 'HTML') { ?>
					<div><?=$ak->outputHTML()?></div>
				<? } else { ?>
					<div class="ccm-fieldPair">
					<?=$form->label($ak->getFormElementName(), $ak->getKeyName())?> <? if ($ak->isKeyRequired()) { ?><span class="required">*</span><? } ?>
					<?=$ak->outputHTML()?>
					<div class="ccm-spacer"></div>
					</div>				
				<? } ?>
			<? } ?>
		
			<div class="ccm-spacer"></div>
		
			<?=$form->submit('register', t('Register'))?>
			
			<?=$form->hidden('rcID', $rcID); ?>
		
		</form>	
		
		<div class="ccm-spacer"></div>
		
		<div style="margin-top:16px"><a onClick="ccmPopupLogin.toggleRegister()"><?=t('&laquo; Return to login')?></a></div>
	
	</div>
	<? } ?>

</div>

<div id="ccm-popupLoginThrobber"></div>