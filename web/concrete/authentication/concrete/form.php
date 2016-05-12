<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Core::make('helper/form');
/* @var Concrete\Core\Form\Service\Form $form */
?>

<form method="post" action="<?= URL::to('/login', 'authenticate', $this->getAuthenticationTypeHandle()) ?>">

	<div class="">
		<input name="uName" class="form-control col-sm-12" placeholder="<?=Config::get('concrete.user.registration.email_registration') ? t('Email Address') : t('Username')?>" autofocus="autofocus" />
	</div>

	<div class="">
		<label>&nbsp;</label>
		<input name="uPassword" class="form-control" type="password" placeholder="<?=t('Password')?>" />
	</div>

	<div class="checkbox">
		<label style="font-weight:normal">
			<input type="checkbox" name="uMaintainLogin" value="1">
			<?= t('Stay signed in for two weeks') ?>
		</label>
	</div>

	<?php if (isset($locales) && is_array($locales) && count($locales) > 0) {
    ?>
		<div class="form-group">
			<label for="USER_LOCALE" class="control-label"><?= t('Language') ?></label>
			<?= $form->select('USER_LOCALE', $locales) ?>
		</div>
	<?php 
} ?>

	<div class="form-group">
		<button class="btn btn-primary"><?= t('Log in') ?></button>
		<a href="<?= URL::to('/login', 'concrete', 'forgot_password')?>" class="btn pull-right"><?= t('Forgot Password') ?></a>
	</div>

	<?php Core::make('helper/validation/token')->output('login_' . $this->getAuthenticationTypeHandle()); ?>

	<?php if (Config::get('concrete.user.registration.enabled')) {
    ?>
		<br/>
		<hr/>
		<a href="<?=URL::to('/register')?>" class="btn btn-block btn-success"><?=t('Not a member? Register')?></a>
	<?php 
} ?>

</form>
