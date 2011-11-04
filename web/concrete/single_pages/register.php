<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="page-header">
	<h1><?=t('Site Registration')?></h1>
</div>
<div class="ccm-form">

<? 
if($success) { 
	switch($success) { 
		case "registered": 
			?>
			<p><strong><?=$successMsg ?></strong><br/><br/>
			<a href="<?=$this->url('/')?>"><?=t('Return to Home')?></a>
			<? 
		break;
		case "validate": 
			?>
			<p><?=$successMsg[0] ?></p>
			<p><?=$successMsg[1] ?></p>
			<p><a href="<?=$this->url('/')?>"><?=t('Return to Home')?></a></p>
			<?
		break;
		case "pending":
			?>
			<p><?=$successMsg ?></p>
			<p><a href="<?=$this->url('/')?>"><?=t('Return to Home')?></a></p>
            <?
		break;
	}
		
} else { ?>

<form method="post" action="<?=$this->url('/register', 'do_register')?>">
<div class="row">
<div class="span8 columns">
	<fieldset>
		<legend>Your Details</legend>
		<? if ($displayUserName) { ?>
			<div class="clearfix">
				<?= $form->label('uName',t('Username')); ?>
				<div class="input">
					<?= $form->text('uName'); ?>
				</div>
			</div>
		<? } ?>
	
		<div class="clearfix">
			<?php echo $form->label('uEmail',t('Email Address')); ?>
			<div class="input">
				<?php echo $form->text('uEmail'); ?>
			</div>
		</div>
		<div class="clearfix">
			<?php echo $form->label('uPassword',t('Password')); ?>
			<div class="input">
				<?php echo $form->password('uPassword'); ?>
			</div>
		</div>
		<div class="clearfix">
			<?php echo $form->label('uPasswordConfirm',t('Confirm Password')); ?>
			<div class="input">
				<?php echo $form->text('uPasswordConfirm'); ?>
			</div>
		</div>

	</fieldset>
</div>
<div class="span8 columns">
	<fieldset>
		<legend><?=t('Options')?></legend>
	<?
	
	$attribs = UserAttributeKey::getRegistrationList();
	$af = Loader::helper('form/attribute');
	
	foreach($attribs as $ak) { ?> 
			<?= $af->display($ak, $ak->isAttributeKeyRequiredOnRegister());	?>
	<? }?>
	</fieldset>
</div>
<div class="span16 columns ">
	<? if (ENABLE_REGISTRATION_CAPTCHA) { ?>
	
		<div class="clearfix">
			<?=$form->label('captcha', t('Please type the letters and numbers shown in the image.')); ?>
			<div class="input">
				<?php $captcha = Loader::helper('validation/captcha');				
					  $captcha->display();?> </br>
				     <?php $captcha->showInput();?>  
			</div>
		</div>
	
		
	<? } ?>

</div>
<div class="span16 columns">
	<div class="actions">
	<?=$form->hidden('rcID', $rcID); ?>
	<?=$form->submit('register', t('Register') . ' &gt;', array('class' => 'primary'))?>
	</div>
</div>
	
</div>
</form>
<? } ?>

</div>