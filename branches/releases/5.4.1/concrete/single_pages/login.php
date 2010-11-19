<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php  Loader::library('authentication/open_id');?>
<?php  $form = Loader::helper('form'); ?>

<div style="position: relative">

<h1><?php echo t('Sign in to %s', SITE)?></h1>

<?php  if( $passwordChanged ){ ?>

	<div style="margin-bottom:16px; font-weight:bold"><?php echo t('Password changed.  Please login to continue. ') ?></div>

<?php  } ?> 

<?php  if($changePasswordForm){ ?>

	<div style="margin-bottom:16px; font-weight:bold"><?php echo t('Enter your new password below.') ?></div>

	<?php  if (isset($errorMsg)) { ?>
		<div class="ccm-error" style="margin-bottom:16px;"><?php echo $errorMsg?></div>
	<?php  } ?>

	<div class="ccm-form">	

	<form method="post" action="<?php echo $this->url( '/login', 'change_password', $uHash )?>"> 

		<div>
		<label for="uPassword"><?php echo t('New Password')?></label><br/>
		<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
		</div>
		&nbsp;<br>
		<div>
		<label for="uPasswordConfirm"><?php echo t('Confirm Password')?></label><br/>
		<input type="password" name="uPasswordConfirm" id="uPasswordConfirm" class="ccm-input-text">
		</div>

		<div class="ccm-button">
		<?php echo $form->submit('submit', t('Sign In') . ' &gt;')?>
		</div>
	</form>
	
	</div>

<?php  }elseif($validated) { ?>

<h2><?php echo t('Email Address Verified')?></h2>

<p>
<?php echo t('The email address <b>%s</b> has been verified and you are now a fully validated member of this website.', $uEmail)?>
</p>
<p><a href="<?php echo $this->url('/')?>"><?php echo t('Return to Home')?> &gt;</a></p>

<?php  } else if (isset($_SESSION['uOpenIDError']) && isset($_SESSION['uOpenIDRequested'])) { ?>

<div class="ccm-form">

<?php  switch($_SESSION['uOpenIDError']) {
	case OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE: ?>

		<form method="post" action="<?php echo $this->url('/login', 'complete_openid_email')?>">
			<p><?php echo t('To complete the signup process, you must provide a valid email address.')?></p>
			<label for="uEmail"><?php echo t('Email Address')?></label><br/>
			<?php echo $form->text('uEmail')?>
				
			<div class="ccm-button">
			<?php echo $form->submit('submit', t('Sign In') . ' &gt;')?>
			</div>
		</form>

	<?php  break;
	case OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS:
	
	$ui = UserInfo::getByID($_SESSION['uOpenIDExistingUser']);
	
	?>

		<form method="post" action="<?php echo $this->url('/login', 'do_login')?>">
			<p><?php echo t('The OpenID account returned an email address already registered on this site. To join this OpenID to the existing user account, login below:')?></p>
			<label for="uEmail"><?php echo t('Email Address')?></label><br/>
			<div><strong><?php echo $ui->getUserEmail()?></strong></div>
			<br/>
			
			<div>
			<label for="uName"><?php  if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>
				<?php echo t('Email Address')?>
			<?php  } else { ?>
				<?php echo t('Username')?>
			<?php  } ?></label><br/>
			<input type="text" name="uName" id="uName" <?php echo  (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
			</div>			<div>

			<label for="uPassword"><?php echo t('Password')?></label><br/>
			<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
			</div>

			<div class="ccm-button">
			<?php echo $form->submit('submit', t('Sign In') . ' &gt;')?>
			</div>
		</form>

	<?php  break;

	}
?>

</div>

<?php  } else if ($invalidRegistrationFields == true) { ?>

<div class="ccm-form">

	<p><?php echo t('You must provide the following information before you may login.')?></p>
	
<form method="post" action="<?php echo $this->url('/login', 'do_login')?>">
	<?php  
	$attribs = UserAttributeKey::getRegistrationList();
	$af = Loader::helper('form/attribute');
	
	$i = 0;
	foreach($unfilledAttributes as $ak) { 
		if ($i > 0) { 
			print '<br/><br/>';
		}
		print $af->display($ak, $ak->isAttributeKeyRequiredOnRegister());	
		$i++;
	}
	?>
	
	<?php echo $form->hidden('uName', $_POST['uName'])?>
	<?php echo $form->hidden('uPassword', $_POST['uPassword'])?>
	<?php echo $form->hidden('uOpenID', $uOpenID)?>
	<?php echo $form->hidden('completePartialProfile', true)?>

	<div class="ccm-button">
		<?php echo $form->submit('submit', t('Sign In'))?>
		<?php echo $form->hidden('rcID', $rcID); ?>
	</div>
	
</form>
</div>	

<?php  } else { ?>

<?php  if (isset($intro_msg)) { ?>
<h2><?php echo $intro_msg?></h2>
<?php  } ?>

<div class="ccm-form">
<?php  if (ENABLE_REGISTRATION == 1) { ?><div style="position: absolute; top: 36px; right: 0px; font-size: 11px"><?php echo t('Not a member?')?> <a href="<?php echo $this->url('/register')?>"><?php echo t('Register here!')?></a></div><?php  } ?>

<form method="post" action="<?php echo $this->url('/login', 'do_login')?>">
	<div>
	<label for="uName"><?php  if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>
		<?php echo t('Email Address')?>
	<?php  } else { ?>
		<?php echo t('Username')?>
	<?php  } ?></label><br/>
	<input type="text" name="uName" id="uName" <?php echo  (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
	</div>
	<br>
	<div>
	<label for="uPassword"><?php echo t('Password')?></label><br/>
	<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
	</div>

	<hr />
	
	<?php  if (OpenIDAuth::isEnabled()) { ?>
		<div>
		<label for="uOpenID"><?php echo t('Or login using an OpenID')?>:</label><br/>
		<input type="text" name="uOpenID" id="uOpenID" <?php echo  (isset($uOpenID)?'value="'.$uOpenID.'"':'');?> class="ccm-input-openid">
		</div>
	<?php  } ?>
	<?php echo $form->checkbox('uMaintainLogin', 1)?> <label for="uMaintainLogin"><?php echo t('Remember Me')?></label>
	
	<div class="ccm-button">
	<?php echo $form->submit('submit', t('Sign In') . ' &gt;')?>
	</div>
	<?php  $rcID = isset($_REQUEST['rcID']) ? preg_replace('/<|>/', '', $_REQUEST['rcID']) : $rcID; ?>
	<input type="hidden" name="rcID" value="<?php echo $rcID?>" />
</form>
</div>

<div class="ccm-form">

<h2 style="margin-top:32px"><?php echo t('Forgot Your Password?')?></h2>

<p><?php echo t("If you've forgotten your password, enter your email address below. We will reset it to a new password, and send the new one to you.")?></p>

</div>

<div class="ccm-form">

<a name="forgot_password"></a>

<form method="post" action="<?php echo $this->url('/login', 'forgot_password')?>">
	
	<label for="uEmail"><?php echo t('Email Address')?></label><br/>
	<input type="hidden" name="rcID" value="<?php echo $rcID?>" />
	<input type="text" name="uEmail" value="" class="ccm-input-text" >

	<div class="ccm-button">
	<?php echo $form->submit('submit', t('Reset and Email Password') . ' &gt;')?>
	</div>
	
</form>

</div>


<script type="text/javascript">
	document.getElementById("uName").focus();
</script>

<?php  } ?>

</div>

