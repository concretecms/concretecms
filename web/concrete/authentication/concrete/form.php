<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>


<form method='post'
      action='<?= View::url('/login', 'authenticate', $this->getAuthenticationTypeHandle()) ?>'>
    <div class="form-group concrete-login">
        <p>
            <?= t('Sign into your website with an account that is part of this website.') ?>
        </p>
        <hr>
    </div>
    <div class="form-group">
        <input name="uName" class="form-control col-sm-12"
               placeholder="<?=USER_REGISTRATION_WITH_EMAIL_ADDRESS ? t('Email Address') : t('Username')?>" />
    </div>
    <div class="form-group">
        <label><?php // Empty label for spacing ?></label>
        <input name="uPassword" class="form-control" type="password"
               placeholder="Password" />
    </div>
    <div class="form-group">
        <label class="checkbox" style="font-weight:normal">
            <input type="checkbox" name="uMaintainLogin" value="1">
            <?= t('Stay signed in for two weeks') ?>
        </label>
    </div>

    <div class="form-group">
        <button class="btn btn-primary"><?= t('Log in') ?></button>
        <a href="<?=View::url('/login', 'concrete', 'forgot_password')?>" class="btn pull-right"><?= t('Forgot Password') ?></a>
    </div>

    <?php Loader::helper('validation/token')->output('login_' . $this->getAuthenticationTypeHandle()); ?>
</form>




<?php
/*
?>

<div class='row'>
	<div class='span5'>
		<div class='control-group'>
			<label class='control-label' for="uName">
				<?=USER_REGISTRATION_WITH_EMAIL_ADDRESS?t('Email Address'):t('Username')?>
			</label>
			<div class='controls'>
				<?=$form->text('uName')?>
			</div>
		</div>
		<div class='control-group'>
			<label class='control-label' for="uPassword">
				<?=t('Password')?>
			</label>
			<div class='controls'>
				<?=$form->password('uPassword')?>
			</div>
		</div>
	</div>
	<?=$form->hidden('rcID', $rcID); ?>
	<div class='span3 offset1'>
		<label class='checkbox'><?=$form->checkbox('uMaintainLogin',1)?> <?=t('Remain logged in to website.')?></label>
		<div class="help-block"><a href="<?=View::url('/login', 'concrete', 'forgot_password')?>"><?=t('Forgot Password?')?></a></div>
	</div>
</div>
<div class='actions'>
	<button class='btn primary'><?=t('Sign In')?></button>
</div>

*/?>
