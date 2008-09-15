<? if ($validated) { ?>

<h1><?=$_('Email Address Verified')?></h1>

<?=t('The email address <b>%1</b> has been verified and you are now a fully validated member of this website.', $uEmail)?>

<p><a href="<?=$this->url('/')?>">Return to Home &gt;</a></p>

<? } else { ?>

<h1><?=t('Sign In To Edit %s', SITE)?></h1>

<? if (isset($intro_msg)) { ?>
<h2><?=$intro_msg?></h2>
<? } ?>

<div class="ccm-form">

<form method="post" action="<?=$this->url('/login', 'do_login')?>">
	<div>
	<label for="uName"><?=$uNameLabel?></label><br/>
	<input type="text" name="uName" id="uName" class="ccm-input-text">
	</div>
	<br>
	<div>
	<label for="uPassword">Password</label><br/>
	<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
	</div>

	<?=$form->checkbox('uMaintainLogin', 1)?> Remember Me
	
	<div class="ccm-button">
	<?=$form->submit('submit', 'Sign In &gt;')?>
	</div>

	<input type="hidden" name="rcURL" value="<?=$rcURL?>" />

</form>
</div>


<h2 style="margin-top:32px">Forgot Password?</h2>

<p>If you've forgotten your password, enter your email address below. We will reset it to a new password, and send the new one to you.</p>

<div class="ccm-form">

<a name="forgot_password"></a>

<form method="post" action="<?=$this->url('/login', 'forgot_password')?>">
	
	<label for="uEmail">Email Address</label><br/>
	<input type="hidden" name="rcURL" value="<?=$rcURL?>" />
	<input type="text" name="uEmail" value="" class="ccm-input-text" >

	<div class="ccm-button">
	<?=$form->submit('submit', 'Reset and Email Password &gt;')?>
	</div>
	
</form>

</div>


<script type="text/javascript">
	document.getElementById("uName").focus();
</script>

<? } ?>

