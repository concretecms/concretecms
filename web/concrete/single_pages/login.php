<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? Loader::library('authentication/open_id');?>
<? $form = Loader::helper('form'); ?>

<div style="position: relative">

<h1><?=t('Sign in to %s', SITE)?></h1>

<? if( $passwordChanged ){ ?>

	<div style="margin-bottom:16px; font-weight:bold"><?=t('Password changed.  Please login to continue. ') ?></div>

<? } ?> 

<? if($changePasswordForm){ ?>

	<div style="margin-bottom:16px; font-weight:bold"><?=t('Enter your new password below.') ?></div>

	<? if (isset($errorMsg)) { ?>
		<div class="ccm-error" style="margin-bottom:16px;"><?=$errorMsg?></div>
	<? } ?>

	<div class="ccm-form">	

	<form method="post" action="<?=$this->url( '/login', 'change_password', $uHash )?>"> 

		<div>
		<label for="uPassword"><?=t('New Password')?></label><br/>
		<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
		</div>
		&nbsp;<br>
		<div>
		<label for="uPasswordConfirm"><?=t('Confirm Password')?></label><br/>
		<input type="password" name="uPasswordConfirm" id="uPasswordConfirm" class="ccm-input-text">
		</div>

		<div class="ccm-button">
		<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
		</div>
	</form>
	
	</div>

<? }elseif($validated) { ?>

<h2><?=t('Email Address Verified')?></h2>

<p>
<?=t('The email address <b>%s</b> has been verified and you are now a fully validated member of this website.', $uEmail)?>
</p>
<p><a href="<?=$this->url('/')?>"><?=t('Return to Home')?> &gt;</a></p>

<? } else if (isset($_SESSION['uOpenIDError']) && isset($_SESSION['uOpenIDRequested'])) { ?>

<div class="ccm-form">

<? switch($_SESSION['uOpenIDError']) {
	case OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE: ?>

		<form method="post" action="<?=$this->url('/login', 'complete_openid_email')?>">
			<p><?=t('To complete the signup process, you must provide a valid email address.')?></p>
			<label for="uEmail"><?=t('Email Address')?></label><br/>
			<?=$form->text('uEmail')?>
				
			<div class="ccm-button">
			<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
			</div>
		</form>

	<? break;
	case OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS:
	
	$ui = UserInfo::getByID($_SESSION['uOpenIDExistingUser']);
	
	?>

		<form method="post" action="<?=$this->url('/login', 'do_login')?>">
			<p><?=t('The OpenID account returned an email address already registered on this site. To join this OpenID to the existing user account, login below:')?></p>
			<label for="uEmail"><?=t('Email Address')?></label><br/>
			<div><strong><?=$ui->getUserEmail()?></strong></div>
			<br/>
			
			<div>
			<label for="uName"><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>
				<?=t('Email Address')?>
			<? } else { ?>
				<?=t('Username')?>
			<? } ?></label><br/>
			<input type="text" name="uName" id="uName" <?= (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
			</div>			<div>

			<label for="uPassword"><?=t('Password')?></label><br/>
			<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
			</div>

			<div class="ccm-button">
			<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
			</div>
		</form>

	<? break;

	}
?>

</div>

<? } else if ($invalidRegistrationFields == true) { ?>

<div class="ccm-form">

	<p><?=t('You must provide the following information before you may login.')?></p>
	
<form method="post" action="<?=$this->url('/login', 'do_login')?>">
	<? 
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
	
	<?=$form->hidden('uName', $_POST['uName'])?>
	<?=$form->hidden('uPassword', $_POST['uPassword'])?>
	<?=$form->hidden('uOpenID', $uOpenID)?>
	<?=$form->hidden('completePartialProfile', true)?>

	<div class="ccm-button">
		<?=$form->submit('submit', t('Sign In'))?>
		<?=$form->hidden('rcID', $rcID); ?>
	</div>
	
</form>
</div>	

<? } else { ?>

<? if (isset($intro_msg)) { ?>
<h2><?=$intro_msg?></h2>
<? } ?>

<div class="ccm-form">
<? if (ENABLE_REGISTRATION == 1) { ?><div style="position: absolute; top: 36px; right: 0px; font-size: 11px"><?=t('Not a member?')?> <a href="<?=$this->url('/register')?>"><?=t('Register here!')?></a></div><? } ?>

<form method="post" action="<?=$this->url('/login', 'do_login')?>">
	<div>
	<label for="uName"><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>
		<?=t('Email Address')?>
	<? } else { ?>
		<?=t('Username')?>
	<? } ?></label><br/>
	<input type="text" name="uName" id="uName" <?= (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
	</div>
	<br>
	<div>
	<label for="uPassword"><?=t('Password')?></label><br/>
	<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
	</div>

	
	<? if (OpenIDAuth::isEnabled()) { ?>
		<div>
		<label for="uOpenID"><?=t('Or login using an OpenID')?>:</label><br/>
		<input type="text" name="uOpenID" id="uOpenID" <?= (isset($uOpenID)?'value="'.$uOpenID.'"':'');?> class="ccm-input-openid">
		</div>

	<? } ?>

	<? if (isset($locales) && is_array($locales) && count($locales) > 0) { ?>
		<div>
		<br/>
		<label for="USER_LOCALE"><?=t('Language')?></label><br/>
		<?=$form->select('USER_LOCALE', $locales)?>
		</div>
		<br/>
	<? } ?>

	<div style="float: left; width: 120px; padding-top: 12px"><?=$form->checkbox('uMaintainLogin', 1)?> <label for="uMaintainLogin"><?=t('Remember Me')?></label></div>
	
	<div class="ccm-button">
	<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
	</div>
	<div class="ccm-spacer">&nbsp;</div>
	<? $rcID = isset($_REQUEST['rcID']) ? Loader::helper('text')->entities($_REQUEST['rcID']) : $rcID; ?>
	<input type="hidden" name="rcID" value="<?=$rcID?>" />
</form>
</div>

<div class="ccm-form">

<h2 style="margin-top:32px"><?=t('Forgot Your Password?')?></h2>

<p><?=t("If you've forgotten your password, enter your email address below. We will reset it to a new password, and send the new one to you.")?></p>

</div>

<div class="ccm-form">

<a name="forgot_password"></a>

<form method="post" action="<?=$this->url('/login', 'forgot_password')?>">
	
	<label for="uEmail"><?=t('Email Address')?></label><br/>
	<input type="hidden" name="rcID" value="<?=$rcID?>" />
	<input type="text" name="uEmail" value="" class="ccm-input-text" >

	<div class="ccm-button">
	<?=$form->submit('submit', t('Reset and Email Password') . ' &gt;')?>
	</div>
	
</form>

</div>


<script type="text/javascript">
	document.getElementById("uName").focus();
</script>

<? } ?>

</div>

