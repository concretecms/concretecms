<? defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<h1>Site Registration</h1>
<div class="ccm-form">

<? if ($registered) { ?>
	
	<p><strong>Your account has been created, and you are now logged in.</strong><br/><br/>
	<a href="<?=$this->url('/')?>">Return to Home</a>
	

<? } else if ($validate) { ?>

	<p>You are registered but you need to validate your email address. Some or all functionality on this site will be limited until you do so.</p>
	<p>An email has been sent to your email address. Click on the URL contained in the email to validate your email address.</p>
	<p><a href="<?=$this->url('/')?>">Return to Home</a></p>


<? } else { ?>

<form method="post" action="<?=$this->url('/register', 'do_register')?>">


	<? if ($displayUserName) { ?>
		<div>
		<?=$form->label('uName', 'Username')?>
		<?=$form->text('uName')?>
		</div>
		<br/>
	<? } ?>
	
	<div>
	<?=$form->label('uEmail', 'Email Address')?>
	<?=$form->text('uEmail')?>
	</div>
	<br/>
	
	<div>
	<?=$form->label('uPassword', 'Password')?>
	<?=$form->password('uPassword')?>
	</div>
	<br/>
	
	<div>
	<?=$form->label('uPasswordConfirm', 'Confirm Password')?>
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
		<?=$form->submit('register', 'Register &gt;')?>
		<?=$form->hidden('rcURL')?>
	</div>

</form>

<? } ?>

</div>