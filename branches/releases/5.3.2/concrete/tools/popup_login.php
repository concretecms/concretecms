<?php 

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
	
	
	
		<form id="popupLoginForm" method="post" action="<?php echo $loginFormSubmitURL ?>" onSubmit="return ccmPopupLogin.login(this);">
		
			<input name="format" type="hidden" value="JSON">
			
			<div id="loginFormColums" style="position:relative; left:0px; top:0px; width:100%; height:auto;"> 
			
				<?php  if(ENABLE_REGISTRATION || $_REQUEST['remote']){ ?>
				<div id="loginFormRight" style="margin-top:16px; width:33%; text-align:right; float:right; margin-right:20px; height:auto; margin-top:80px; "> 
					 <div style="font-size:inherit; line-height:inherit; text-align:left; margin-bottom:8px;">
					 <?php echo  t("Don't have an account on <Br />the concrete5 marketplace?"); ?>
					 </div>
					 <a onClick="ccmPopupLogin.toggleRegister()"><?php echo t('Register a new account.')?></a> 	
				</div>	
				<?php  } ?>
				
				<div id="loginFormLeft" style="width:60%;">
					
					<h2><?php echo t('Login')?></h2>
					
					<div class="ccm-fieldPair">
						<label for="uName">
						<?php  if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true && !$_REQUEST['remote']) { ?>
							<?php echo t('Email Address')?>
						<?php  } else { ?>
							<?php echo t('Username')?>
						<?php  } ?>
						</label>
						<input type="text" name="uName" id="uName" <?php echo  (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
						<div class="ccm-spacer"></div>
					</div> 
						
					<div class="ccm-fieldPair">
						<label for="uPassword"><?php echo t('Password')?></label>
						<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
						<div class="ccm-spacer"></div>
					</div>
				 
					
					<?php  if (OpenIDAuth::isEnabled() && !$_REQUEST['remote']) { ?>
						<div class="ccm-fieldPair">
							<label for="uOpenID"><?php echo t('Or login using an OpenID')?>:</label><br/>
							<input type="text" name="uOpenID" id="uOpenID" <?php echo  (isset($uOpenID)?'value="'.$uOpenID.'"':'');?> class="ccm-input-openid">
							<div class="ccm-spacer"></div>
						</div>
					<?php  } ?>
					
					<div class="ccm-fieldPair" style="padding-top:0px;">
						<label for="placeholder">&nbsp;</label>
						<a onClick="ccmPopupLogin.toggleForgot()"><?php echo t('Forgot your password?')?></a>
						<div class="ccm-spacer"></div>
					</div>
					
					
					
					<?php  if( !$_REQUEST['remote'] ){ ?>
					<div class="ccm-fieldPair">
						<?php echo $form->checkbox('uMaintainLogin', 1)?>
						<label id="uMaintainLoginLabel" for="uMaintainLogin"><?php echo t('Remember Me')?></label> 			
					</div>
					<?php  } ?>
					
					<div class="ccm-spacer"></div>
					
				</div>
				
				<div class="ccm-spacer"></div>
			
			</div>
			
			<hr />
			
			<div class="ccm-buttons"> 
			<a onclick="$('#ccm-popup-login-submit').click()" class="ccm-button-right"><span><em class=""><?php echo t('Sign In')?> &gt;</em></span></a>	
			<input type="submit" name="submit" value="submit" style="display: none" id="ccm-popup-login-submit" />
			</div>
			
			<?php echo $form->hidden('rcID', $rcID); ?>	
		</form> 
		
		<div class="ccm-spacer"></div>	
	</div>
	
	
	
	<div id="ccm-popupForgotPasswordWrap" class="ccm-form" style="display:none">
	
		<h2><?php echo t('Forgot Your Password?')?></h2>
		
		<p><?php echo t("If you've forgotten your password, enter your email address below. We will reset it to a new password, and send the new one to you.")?></p>
		
		<a name="forgot_password"></a>
		
		<div id="ccm-popupForgotMsg" class="ccm-popupMsg" style="margin-bottom:8px"></div>	
			
		<form id="popupForgotPasswordForm" method="post" action="<?php echo $forgotPasswordFormSubmitURL?>" onSubmit="return ccmPopupLogin.submitForgotPassword(this);">
			
			<input name="format" type="hidden" value="JSON">
			
			<table>
				<tr>
					<td>
						<label for="uEmail" style="white-space:nowrap; width: auto; float:none; padding-top:12px; "><?php echo t('Email Address')?></label> 
					</td>
					<td style="padding-left:8px; padding-top:8px">
						<?php echo $form->hidden('rcID', $rcID); ?>
						<input type="text" name="uEmail" value="" class="ccm-input-text" >
						<div class="ccm-spacer"></div>
					</td>
					<td style="padding-left:12px">  
						<div class="ccm-buttons"> 
							<a onclick="$('#ccm-popup-forgot-pass-submit').click()" class="ccm-button-right"><span><em><?php echo t('Reset and Email Password')  ?> &gt;</em></span></a>	
							<input type="submit" name="submit" value="submit" style="display: none" id="ccm-popup-forgot-pass-submit" />
						</div>	
					</td>
				</tr>
			</table>		
			
		</form>
		
		<div class="ccm-spacer"></div>
		
		<div style="margin-top:16px"><a onClick="ccmPopupLogin.toggleForgot()"><?php echo t('&laquo; Return to login')?></a></div>
	
	</div>
	
	
	
	<?php  if(ENABLE_REGISTRATION || $_REQUEST['remote']){ ?>
	<div id="ccm-popupRegisterWrap" class="ccm-form" style="display:none">
	
		<h2><?php echo t('Register')?></h2>
		
		<div id="ccm-popupRegisterMsg" class="ccm-popupMsg" style="margin-bottom:16px"></div>
		
		<form id="popupRegisterForm" method="post" action="<?php echo $registerFormSubmitURL ?>" onSubmit="return ccmPopupLogin.submitRegister(this);">
		
			<input name="format" type="hidden" value="JSON">
		
			<div id="popupRegisterColRight" style="float:right; width:48%;">
				<?php echo $form->checkbox('uTermsConditions', 'terms')?>	<?php echo t('I agree to the <a href="%s" target="_blank">terms and conditions</a>.','http://www.concrete5.org/help/legal') ?>
				
			</div>
		
			<div id="popupRegisterColLeft" style="width:48%;">
				<?php  if (!USER_REGISTRATION_WITH_EMAIL_ADDRESS || $_REQUEST['remote']) { ?>
					<div class="ccm-fieldPair">
						<?php echo $form->label('uName', t('Username') )?>
						<?php echo $form->text('uName')?>
						<div class="ccm-spacer"></div>
					</div>
				<?php  } ?>
				
				<div class="ccm-fieldPair">
					<?php echo $form->label('uEmail', t('Email Address') )?>
					<?php echo $form->text('uEmail')?>
					<div class="ccm-spacer"></div>
				</div>
				
				<div class="ccm-fieldPair">
				<?php echo $form->label('uPassword', t('Password') )?>
				<?php echo $form->password('uPassword')?>
				<div class="ccm-spacer"></div>
				</div>
				
				<div class="ccm-fieldPair">
				<?php echo $form->label('uPasswordConfirm', t('Confirm Password') )?>
				<?php echo $form->password('uPasswordConfirm')?>
				<div class="ccm-spacer"></div>
				</div>
				
				<?php 
				if(!$_REQUEST['remote']){
				$attribs = UserAttributeKey::getRegistrationList();
				foreach($attribs as $ak) { 
				
					if ($ak->getKeyType() == 'HTML') { ?>
						<div><?php echo $ak->outputHTML()?></div>
					<?php  } else { ?>
						<div class="ccm-fieldPair">
						<?php echo $form->label($ak->getFormElementName(), $ak->getKeyName())?> <?php  if ($ak->isKeyRequired()) { ?><span class="required">*</span><?php  } ?>
						<?php echo $ak->outputHTML()?>
						<div class="ccm-spacer"></div>
						</div>				
					<?php  } ?>
					
				<?php  }
				} ?>

				<div class="ccm-spacer"></div>
			</div>	
		
			<div class="ccm-spacer"></div>
			
			<hr />
			
			<div class="ccm-buttons" style="width:40%; float:right"> 
				<a onclick="$('#ccm-popup-register-submit').click()" class="ccm-button-right"><span><em><?php echo t('Register') ?>  &gt;</em></span></a>	
				<input type="submit" name="submit" value="submit" style="display: none" id="ccm-popup-register-submit" />
			</div>	
			
			<div style="margin-top:16px; width:40%; padding-top:16px; "><a onClick="ccmPopupLogin.toggleRegister()"><?php echo t('&laquo; Return to login')?></a></div>
	
			
			<div class="ccm-spacer"></div>		
			
			<?php echo $form->hidden('rcID', $rcID); ?>
		
		</form>	
	
	</div>
	<?php  } ?>

</div>

<div id="ccm-popupLoginThrobber"></div>