<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<h1><?=t('Sign in to %s', SITE)?></h1>

<? if ($validated) { ?>

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

<? } else { ?>

<? if (isset($intro_msg)) { ?>
<h2><?=$intro_msg?></h2>
<? } ?>

<div class="ccm-form">
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

	<hr />
	
	<? if (OpenIDAuth::isEnabled()) { ?>
		<div>
		<label for="uOpenID"><?=t('Or login using an OpenID')?>:</label><br/>
		<input type="text" name="uOpenID" id="uOpenID" <?= (isset($uOpenID)?'value="'.$uOpenID.'"':'');?> class="ccm-input-openid">
		</div>
	<? } ?>
	<?=$form->checkbox('uMaintainLogin', 1)?> <label for="uMaintainLogin"><?=t('Remember Me')?></label>
	
	<div class="ccm-button">
	<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
	</div>
	
	<?=$form->hidden('rcID', $rcID); ?>

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
	<?=$form->hidden('rcID', $rcID); ?>
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

