<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<?php  if ($validated) { ?>

<h1><?php echo t('Email Address Verified')?></h1>

<p>
<?php echo t('The email address <b>%s</b> has been verified and you are now a fully validated member of this website.', $uEmail)?>
</p>
<p><a href="<?php echo $this->url('/')?>"><?php echo t('Return to Home')?> &gt;</a></p>

<?php  } else { ?>

<h1><?php echo t('Sign in to %s', SITE)?></h1>

<?php  if (isset($intro_msg)) { ?>
<h2><?php echo $intro_msg?></h2>
<?php  } ?>

<div class="ccm-form">
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

	<?php echo $form->checkbox('uMaintainLogin', 1)?> <label for="uMaintainLogin"><?php echo t('Remember Me')?></label>
	
	<div class="ccm-button">
	<?php echo $form->submit('submit', t('Sign In') . ' &gt;')?>
	</div>
	
	<?php echo $form->hidden('rcID', $rcID); ?>

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
	<?php echo $form->hidden('rcID', $rcID); ?>
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

