<h1>Site Registration</h1>
<div class="ccm-form">

<?php  if ($registered) { ?>
	
	<p><strong>Your account has been created, and you are now logged in.</strong><br/><br/>
	<a href="<?php echo $this->url('/')?>">Return to Home</a>
	

<?php  } else if ($validate) { ?>

	<p>You are registered but you need to validate your email address. Some or all functionality on this site will be limited until you do so.</p>
	<p>An email has been sent to your email address. Click on the URL contained in the email to validate your email address.</p>
	<p><a href="<?php echo $this->url('/')?>">Return to Home</a></p>


<?php  } else { ?>

<form method="post" action="<?php echo $this->url('/register', 'do_register')?>">


	<?php  if ($displayUserName) { ?>
		<div>
		<?php echo $form->label('uName', 'Username')?>
		<?php echo $form->text('uName')?>
		</div>
		<br/>
	<?php  } ?>
	
	<div>
	<?php echo $form->label('uEmail', 'Email Address')?>
	<?php echo $form->text('uEmail')?>
	</div>
	<br/>
	
	<div>
	<?php echo $form->label('uPassword', 'Password')?>
	<?php echo $form->password('uPassword')?>
	</div>
	<br/>
	
	<div>
	<?php echo $form->label('uPasswordConfirm', 'Confirm Password')?>
	<?php echo $form->password('uPasswordConfirm')?>
	</div>
	<br/>
	
	<?php 
	
	$attribs = UserAttributeKey::getRegistrationList();
	foreach($attribs as $ak) { 
		if ($ak->getKeyType() == 'HTML') { ?>
			<div><?php echo $ak->outputHTML()?></div>
		<?php  } else { ?>
			<div>
			<?php echo $form->label($ak->getFormElementName(), $ak->getKeyName())?> <?php  if ($ak->isKeyRequired()) { ?><span class="required">*</span><?php  } ?>
			<?php echo $ak->outputHTML()?>
			</div>
			<br/>
			
		<?php  } ?>
	<?php  } ?>

	<div class="ccm-button">
		<?php echo $form->submit('register', 'Register &gt;')?>
		<?php echo $form->hidden('rcURL')?>
	</div>

</form>

<?php  } ?>

</div>