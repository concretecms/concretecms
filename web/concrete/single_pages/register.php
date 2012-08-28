<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="row">
<div class="span10 offset1">
<div class="page-header">
	<h1><?=t('Site Registration')?></h1>
</div>
</div>
</div>

<div class="ccm-form">

<? 
$attribs = UserAttributeKey::getRegistrationList();

if($success) { ?>
<div class="row">
<div class="span10 offset1">
<?	switch($success) { 
		case "registered": 
			?>
			<p><strong><?=$successMsg ?></strong><br/><br/>
			<a href="<?=$this->url('/')?>"><?=t('Return to Home')?></a></p>
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
	} ?>
			</div>
</div>
<? 
} else { ?>

<form method="post" action="<?=$this->url('/register', 'do_register')?>" class="form-horizontal">
<div class="row">
<div class="<? if (count($attribs) > 0) {?>span5<? } else {?>span10<? } ?> offset1">
	<fieldset>
		<legend><?=t('Your Details')?></legend>
		<? if ($displayUserName) { ?>
				<div class="control-group">
				<?= $form->label('uName',t('Username')); ?>
				<div class="controls">
					<?= $form->text('uName'); ?>
				</div>
			</div>
		<? } ?>
	
		<div class="control-group">
			<?php echo $form->label('uEmail',t('Email Address')); ?>
			<div class="controls">
				<?php echo $form->text('uEmail'); ?>
			</div>
		</div>
		<div class="control-group">
			<?php echo $form->label('uPassword',t('Password')); ?>
			<div class="controls">
				<?php echo $form->password('uPassword'); ?>
			</div>
		</div>
		<div class="control-group">
			<?php echo $form->label('uPasswordConfirm',t('Confirm Password')); ?>
			<div class="controls">
				<?php echo $form->password('uPasswordConfirm'); ?>
			</div>
		</div>

	</fieldset>
</div>
<? if (count($attribs) > 0) { ?>
<div class="span5">
	<fieldset>
		<legend><?=t('Options')?></legend>
	<?
	
	$af = Loader::helper('form/attribute');
	
	foreach($attribs as $ak) { ?> 
			<?= $af->display($ak, $ak->isAttributeKeyRequiredOnRegister());	?>
	<? }?>
	</fieldset>
</div>
<? } ?>
<div class="span10 offset1 ">
	<? if (ENABLE_REGISTRATION_CAPTCHA) { ?>
	
		<div class="control-group">
			<?php $captcha = Loader::helper('validation/captcha'); ?>			
			<?=$captcha->label()?>
			<div class="controls">
			<?
		  	  $captcha->showInput(); 
			  $captcha->display();
		  	  ?>
			</div>
		</div>
	
		
	<? } ?>

</div>
<div class="span10 offset1">
	<div class="actions">
	<?=$form->hidden('rcID', $rcID); ?>
	<?=$form->submit('register', t('Register') . ' &gt;', array('class' => 'primary'))?>
	</div>
</div>
	
</div>
</form>
<? } ?>

</div>