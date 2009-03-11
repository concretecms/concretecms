<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h1><?=t('Site Registration')?></h1>
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

	<? if ($displayUserName) { ?>
		<div>
		<?=$form->label('uName', t('Username') )?>
		<?=$form->text('uName')?>
		</div>
		<br/>
	<? } ?>
	
	<div>
	<?=$form->label('uEmail', t('Email Address') )?>
	<?=$form->text('uEmail')?>
	</div>
	<br/>
	
	<div>
	<?=$form->label('uPassword', t('Password') )?>
	<?=$form->password('uPassword')?>
	</div>
	<br/>
	
	<div>
	<?=$form->label('uPasswordConfirm', t('Confirm Password') )?>
	<?=$form->password('uPasswordConfirm')?>
	</div>
	<br/>
	
	<?
	
	$attribs = UserAttributeKey::getRegistrationList();
	foreach($attribs as $ak) { 
		if ($ak->getKeyType() == 'HTML') { ?>
			<div><?=$ak->outputHTML()?></div>
		<? } else { ?>
			<div>
			<?=$form->label($ak->getFormElementName(), $ak->getKeyName())?> <? if ($ak->isKeyRequired()) { ?><span class="required">*</span><? } ?>
			<?=$ak->outputHTML()?>
			</div>
			<br/>
			
		<? } ?>
	<? } ?>

	<div class="ccm-button">
		<?=$form->submit('register', t('Register'))?>
		<?=$form->hidden('rcID', $rcID); ?>
	</div>

</form>
<? } ?>

</div>