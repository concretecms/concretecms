<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<? if ($validated) { ?>

<h1>Email Address Verified</h1>

<p>The email address <b><?=$uEmail?></b> has been verified and you are now a fully validated member of this website.</p>
<p><a href="<?=$this->url('/')?>">Return to Home &gt;</a></p>

<? } else { ?>

<h1><?=t('Sign in to %s', SITE)?></h1>

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
	<input type="text" name="uName" id="uName" class="ccm-input-text">
	</div>
	<br>
	<div>
	<label for="uPassword"><?=t('Password')?></label><br/>
	<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
	</div>

	<?=$form->checkbox('uMaintainLogin', 1)?> <?=t('Remember Me')?>
	
	<div class="ccm-button">
	<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
	</div>

	<input type="hidden" name="rcURL" value="<?=$rcURL?>" />

</form>
</div>


<h2 style="margin-top:32px"><?=t('Forgot Password?')?></h2>

<p><?=t('If you\'ve forgotten your password, enter your email address below. We will reset it to a new password, and send the new one to you.')?></p>

<div class="ccm-form">

<a name="forgot_password"></a>

<form method="post" action="<?=$this->url('/login', 'forgot_password')?>">
	
	<label for="uEmail"><?=t('Email Address')?></label><br/>
	<input type="hidden" name="rcURL" value="<?=$rcURL?>" />
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

