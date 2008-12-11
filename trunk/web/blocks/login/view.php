<?
defined('C5_EXECUTE') or die(_("Access Denied.")); 
global $c;
  
$loginURL= $this->url('/login', 'do_login' );
?>


<style>
.login_block_form .loginTxt{ font-weight:bold }
.login_block_form .uNameWrap{ margin:8px 0px; }
.login_block_form .passwordWrap{ margin-bottom:8px;}
.login_block_form .login_block_register_link{margin-top:8px; font-size:11px}
</style>

<form class="login_block_form" method="post" action="<?=$loginURL?>">
	<? if($returnToSamePage ){ ?>
		<input type="hidden" name="rcID" id="rcID" value="<?=$c->getCollectionID(); ?>" />
	<? } ?>
	
	<div class="loginTxt"><?=t('Login')?></div>

	<div class="uNameWrap">
		<label for="uName"><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>
			<?=t('Email Address')?>
		<? } else { ?>
			<?=t('Username')?>
		<? } ?></label><br/>
		<input type="text" name="uName" id="uName" <?= (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
	</div>
	<div class="passwordWrap">
		<label for="uPassword"><?=t('Password')?></label><br/>
		<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
	</div>
	
	<div class="loginButton">
	<?=$form->submit('submit', t('Sign In') . ' &gt;')?>
	</div>	

	<? if($showRegisterLink){ ?>
		<div class="login_block_register_link"><a href="<?=View::url('/register')?>"><?=$registerText?></a></div>
	<? } ?>

</form>