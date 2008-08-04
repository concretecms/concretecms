<?
	Loader::model('user_attributes');
	
	$ui = new UserInfo;
	$error = array();
	
	$rcURL = "";
	if ($_REQUEST['rcURL']) {
		$rcURL = urldecode($_REQUEST['rcURL']);
		$rcURL = urlencode($_REQUEST['rcURL']);
	}
	
	$txt = Loader::helper('text');
	$vals = Loader::helper('validation/strings');
	$valc = Loader::helper('concrete/validation');
	
	if ($_POST['submit']) {
		// someone is registering
		$username = $txt->sanitize($_POST['uName']);
		$password = $txt->sanitize($_POST['uPassword']);
		$passwordConfirm = $txt->sanitize($_POST['uPasswordConfirm']);
		
		if ((strlen($username) < USER_USERNAME_MINIMUM) || (strlen($username) > USER_USERNAME_MAXIMUM)) {
			$error[] = 'Your username must be between ' . USER_USERNAME_MINIMUM . ' and ' . USER_USERNAME_MAXIMUM . ' characters';
		}
		
		if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
			$error[] = 'Your password must be between ' . USER_PASSWORD_MINIMUM . ' and ' . USER_PASSWORD_MAXIMUM . ' characters';
		}
		
		if (!$vals->email($_POST['uEmail'])) {
			$error[] = 'Invalid email address provided.';
		} else if (!$valc->isUniqueEmail($_POST['uEmail'])) {
			$error[] = "The email address '{$_POST['uEmail']}' already exists. Please choose another. If you've forgotten your username or password, but this is your email address, you may have them emailed to this address on the <a href=\"/login\">login page</a>.";
		}
		
		
		if (strlen($username) >= USER_USERNAME_MINIMUM && !$vals->alphanum($username)) {
			$error[] = 'Your username may only contain letters or numbers.';
		}
		
		if (strlen($password) >= USER_PASSWORD_MINIMUM && !$vals->alphanum($password)) {
			$error[] = 'Your password may only contain letters or numbers.';
		}
				
		if ($password != $passwordConfirm) {
			$error[] = 'The two passwords provided do not match.';
		}
				
		if (!$valc->isUniqueUsername($username)) {
			$error[] = "The username '{$username}' already exists. Please choose another";
		}
		
		// now, we run the UserAttributeKey::validSubmittedFields() method
		// this method checks all active attribute keys that are required, and returns an array
		// of fields that FAIL this requirement
		// TODO: add in more aggressive validation (regular expressions, force numeric, etc...)
		$invalidFields = UserAttributeKey::validateSubmittedRequest();
		foreach($invalidFields as $field) {
			$error[] = "The field '{$field}' is required.";
		}

		if (!$error) {

			// do the registration
			$process = $ui->register($_POST);
			$db = Loader::db();
			if ($process) {
				// now we log the user in
				$u = new User($_POST['uName'], $_POST['uPassword']);
				// if this is successful, uID is loaded into session for this user
								
				if (!$u->isError()) {
					
					header("Location: " . DIR_REL . "/register_success?rcURL=" . $rcURL);
					exit;										
				} else if ($u->isError()) {
					if ($u->getError() == USER_INACTIVE) {
						if (defined("VALIDATE_USER_EMAIL")) {
							if (VALIDATE_USER_EMAIL > 0) {
								$vue = true;
								
								$ui = UserInfo::getByID($u->getUserID());
								
								$mh = Loader::helper('mail');
								$mh->addParameter('uEmail', $_POST['uEmail']);
								$mh->addParameter('uHash', UserInfo::getUserActivationHash($_POST['uEmail']));
								$mh->to($_POST['uEmail']);
								$mh->load('validate_user_email');
								$mh->sendMail();							
							}
						}	
					}
				}
			}
		}
	}


	$pageTitle = 'Site Registration';

	?>
	
	<div id="inner-page">
	<h1>Register</h1>
	
	<? if ($registered) { ?>
	
	<p><strong>Your account has been created, and you are now logged in.</strong><br/><br/>
	
	
	<? } else if ($registeredValidate) { ?>
	
	<p>Your account has been created, but your email address needs to be verified. An email has been sent to <strong><?=$_POST['uEmail']?></strong>. It contains a URL that, once clicked, will activate your account. Thanks again for registering.</p>
	
	
	<? } else { ?>

<form method="post" action="<?=DIR_REL?>/register/">

<table border="0" cellspacing="0" cellpadding="0" id="register-builtin" align="center">
	<tr>
		<td valign="top" class="field">
			Desired Username: <span class="required">*</span></td>
		<td>
			<input type="text" name="uName" value="<?=$_POST['uName']?>" class="text"><br>
		</td>
	</tr>
	<tr>
		<td valign="top" class="field">
			Email Address: <span class="required">*</span></td>
		<td>
			<input type="text" name="uEmail" value="<?=$_REQUEST['uEmail']?>" class="text"><br>
		</td>
	</tr>
	<tr>
		<td valign="top" class="field">
			Password: <span class="required">*</span></td>
		<td>
			<input type="password" name="uPassword" value="" class="text"><br>
		</td>
	</tr>
	<tr>
		<td valign="top" class="field">
			Confirm Password: <span class="required">*</span></td>
		<td>
			<input type="password" name="uPasswordConfirm" value="" class="text"><br>
		</td>
	</tr>
	<?
	
	$attribs = UserAttributeKey::getRegistrationList();
	foreach($attribs as $ak) { 
		if ($ak->getKeyType() == 'HTML') { ?>
		<tr>
			<td colspan="2"><?=$ak->outputHTML()?></td>
		</tr>		
		<? } else { ?>
		<tr>
			<td valign="top" class="field">
				<?=$ak->getKeyName()?>: <? if ($ak->isKeyRequired()) { ?><span class="required">*</span><? } ?></td>
			<td><?=$ak->outputHTML()?></td>
		</tr>	
		
		<? } ?>
	<? } ?>
	
</table>
	
	
<div class="buttons">
<input type="hidden" name="_disableLogin" value="1" />
<input type="submit" name="submit" value="Register &gt;" />
</div>
<input type="hidden" name="rcURL" value="<?=$rcURL?>" />

</form>
	<? } ?>
	

	</div>