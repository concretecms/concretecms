<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? Loader::library('authentication/open_id');?>
<? $form = Loader::helper('form'); ?>

<script type="text/javascript">
$(function() {
	$("input[name=uName]").focus();
});
</script>

<? if (isset($intro_msg)) { ?>
<div class="alert-message block-message success"><p><?=$intro_msg?></p></div>
<? } ?>

<div class="row">
<div class="span10 offset1">
<div class="page-header">
	<h1><?=t('Sign in to %s', SITE)?></h1>
</div>
</div>
</div>

<? if( $passwordChanged ){ ?>

	<div class="block-message info alert-message"><p><?=t('Password changed.  Please login to continue. ') ?></p></div>

<? } ?> 

<? if($changePasswordForm){ ?>

	<p><?=t('Enter your new password below.') ?></p>

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
	
	<?=$form->hidden('uName', Loader::helper('text')->entities($_POST['uName']))?>
	<?=$form->hidden('uPassword', Loader::helper('text')->entities($_POST['uPassword']))?>
	<?=$form->hidden('uOpenID', $uOpenID)?>
	<?=$form->hidden('completePartialProfile', true)?>

	<div class="ccm-button">
		<?=$form->submit('submit', t('Sign In'))?>
		<?=$form->hidden('rcID', $rcID); ?>
	</div>
	
</form>
</div>	

<? } else { ?>

<form method="post" action="<?=$this->url('/login', 'do_login')?>" class="form-horizontal">

<div class="row">
<div class="span10 offset1">
<div class="row">
<div class="span5">

<fieldset>
	
	<legend><?=t('User Account')?></legend>

	<div class="control-group">
	
	<label for="uName" class="control-label"><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>
		<?=t('Email Address')?>
	<? } else { ?>
		<?=t('Username')?>
	<? } ?></label>
	<div class="controls">
		<input type="text" name="uName" id="uName" <?= (isset($uName)?'value="'.$uName.'"':'');?> class="ccm-input-text">
	</div>
	
	</div>
	<div class="control-group">

	<label for="uPassword" class="control-label"><?=t('Password')?></label>
	
	<div class="controls">
		<input type="password" name="uPassword" id="uPassword" class="ccm-input-text" />
	</div>
	
	</div>
</fieldset>

<? if (OpenIDAuth::isEnabled()) { ?>
	<fieldset>

	<legend><?=t('OpenID')?></legend>

	<div class="control-group">
		<label for="uOpenID" class="control-label"><?=t('Login with OpenID')?>:</label>
		<div class="controls">
			<input type="text" name="uOpenID" id="uOpenID" <?= (isset($uOpenID)?'value="'.$uOpenID.'"':'');?> class="ccm-input-openid">
		</div>
	</div>
	</fieldset>
<? } ?>

</div>
<div class="span4 offset1">

	<fieldset>

	<legend><?=t('Options')?></legend>

	<? if (isset($locales) && is_array($locales) && count($locales) > 0) { ?>
		<div class="control-group">
			<label for="USER_LOCALE" class="control-label"><?=t('Language')?></label>
			<div class="controls"><?=$form->select('USER_LOCALE', $locales)?></div>
		</div>
	<? } ?>
	
	<div class="control-group">
		<label class="checkbox"><?=$form->checkbox('uMaintainLogin', 1)?> <span><?=t('Remain logged in to website.')?></span></label>
	</div>
	<? $rcID = isset($_REQUEST['rcID']) ? Loader::helper('text')->entities($_REQUEST['rcID']) : $rcID; ?>
	<input type="hidden" name="rcID" value="<?=$rcID?>" />
	
	</fieldset>
</div>
<div class="span10">
	<div class="actions">
	<?=$form->submit('submit', t('Sign In') . ' &gt;', array('class' => 'primary'))?>
	</div>
</div>
</div>
</div>
</div>
</form>

<a name="forgot_password"></a>

<form method="post" action="<?=$this->url('/login', 'forgot_password')?>" class="form-horizontal">
<div class="row">
<div class="span10 offset1">

<h3><?=t('Forgot Your Password?')?></h3>

<p><?=t("Enter your email address below. We will send you instructions to reset your password.")?></p>

<input type="hidden" name="rcID" value="<?=$rcID?>" />
	
	<div class="control-group">
		<label for="uEmail" class="control-label"><?=t('Email Address')?></label>
		<div class="controls">
			<input type="text" name="uEmail" value="" class="ccm-input-text" >
		</div>
	</div>
	
	<div class="actions">
		<?=$form->submit('submit', t('Reset and Email Password') . ' &gt;')?>
	</div>

</div>
</div>	
</form>


<? if (ENABLE_REGISTRATION == 1) { ?>
<div class="row">
<div class="span10 offset1">
<div class="control-group">
<h3><?=t('Not a Member')?></h3>
<p><?=t('Create a user account for use on this website.')?></p>
<div class="actions">
<a class="btn" href="<?=$this->url('/register')?>"><?=t('Register here!')?></a>
</div>
</div>
</div>
</div>
<? } ?>

<? } ?>
