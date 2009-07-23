<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();
$uh = Loader::helper('concrete/user');
$txt = Loader::helper('text');
$vals = Loader::helper('validation/strings');
$valt = Loader::helper('validation/token');
$valc = Loader::helper('concrete/validation');
$dtt = Loader::helper('form/date_time');
$form = Loader::helper('form');
$ih = Loader::helper('concrete/interface');
$av = Loader::helper('concrete/avatar');

if ($_POST['create']) {

	if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
		$_POST['uName'] = $_POST['uEmail'];
	}
	

	$username = $_POST['uName'];
	$username = trim($username);
	$username = ereg_replace(" +", " ", $username);
	$password = $_POST['uPassword'];
	
	if (!$vals->email($_POST['uEmail'])) {
		$error[] = t('Invalid email address provided.');
	} else if (!$valc->isUniqueEmail($_POST['uEmail'])) {
		$error[] = t("The email address '%s' is already in use. Please choose another.",$_POST['uEmail']);
	}
	
	if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
		if (strlen($username) < USER_USERNAME_MINIMUM) {
			$error[] = t('A username must be between at least %s characters long.',USER_USERNAME_MINIMUM);
		}

		if (strlen($username) > USER_USERNAME_MAXIMUM) {
			$error[] = t('A username cannot be more than %s characters long.',USER_USERNAME_MAXIMUM);
		}

		if (strlen($username) >= USER_USERNAME_MINIMUM && !$valc->username($username)) {
			if(USER_USERNAME_ALLOW_SPACES) {
				$e->add(t('A username may only contain letters, numbers and spaces.'));
			} else {
				$e->add(t('A username may only contain letters or numbers.'));
			}
			
		}

		if (!$valc->isUniqueUsername($username)) {
			$error[] = t("The username '%s' already exists. Please choose another",$username);
		}		
	}
	
	if ($username == USER_SUPER) {
		$error[] = t('Invalid Username');
	}

	
	if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
		$error[] = t('A password must be between %s and %s characters',USER_PASSWORD_MINIMUM,USER_PASSWORD_MAXIMUM);
	}
		
	if (strlen($password) >= USER_PASSWORD_MINIMUM && !$valc->password($password)) {
		$error[] = t('A password may not contain ", \', >, <, or any spaces.');
	}

	if (!$valt->validate('create_account')) {
		$error[] = $valt->getErrorMessage();
	}
	
	if (!$error) {
		// do the registration
		$data = array('uName' => $username, 'uPassword' => $password, 'uEmail' => $_POST['uEmail']);
		$uo = UserInfo::add($data);
		
		if (is_object($uo)) {

			if (is_uploaded_file($_FILES['uAvatar']['tmp_name'])) {
				$uHasAvatar = $av->updateUserAvatar($_FILES['uAvatar']['tmp_name'], $uo->getUserID());
			}
			
			$uo->updateSelectedUserAttributes($data['editAKID'], $_POST);
			$uo->updateGroups($_POST['gID']);
			$uID = $uo->getUserID();
			$this->controller->redirect('/dashboard/users/search?uID=' . $uID . '&user_created=1');
		} else {
			$error[] = t('An error occurred while trying to create the account.');
		}
		
	}		
}

?>
	<h1><span><?php echo t('Create Account')?></span></h1>
	
	<div class="ccm-dashboard-inner"> 
	
	<div class="actions">
	<span class="required">*</span> - <?php echo t('required field')?>
	</div>
	
	<form method="post" enctype="multipart/form-data" id="ccm-user-form" action="<?php echo $this->url('/dashboard/users/add')?>">
	<?php echo $valt->output('create_account')?>
	
	<input type="hidden" name="_disableLogin" value="1">

	<h2><?php echo t('Required Information')?></h2>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" width="50%"><?php  if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) { ?><?php echo t('Username')?> <span class="required">*</span><?php  } else { ?><?php echo t('Email Address')?> <span class="required">*</span><?php  } ?></td>
		<td class="subheader" width="50%"><?php echo t('Password')?> <span class="required">*</span></td>
	</tr>
	<tr>
		<td><?php  if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) { ?><input type="text" name="uName" autocomplete="off" value="<?php echo $_POST['uName']?>" style="width: 95%"><?php  } else { ?><input type="text" name="uEmail" autocomplete="off" value="<?php echo $_POST['uEmail']?>" style="width: 95%"><?php  } ?></td>
		<td><input type="password" autocomplete="off" name="uPassword" value="" style="width: 95%"></td>
	</tr>
	<tr>
		<td class="subheader"><?php  if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>&nbsp;<?php  } else { ?><?php echo t('Email Address')?> <span class="required">*</span><?php  } ?></td>
		<td class="subheader"><?php echo t('User Avatar')?></td>
	</tr>	
	<tr>
		<td><?php  if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>&nbsp;<?php  } else { ?><input type="text" name="uEmail" autocomplete="off" value="<?php echo $_POST['uEmail']?>" style="width: 95%"><?php  } ?></td>
		<td><input type="file" name="uAvatar" style="width: 95%"/></td>
	</tr>
	</table>
	</div>
	
	<h2><?php echo t('Groups')?></h2>
	
	<p><?php echo t('Once you create the account you may assign it to groups.')?></p>
	

	<div class="ccm-buttons">
		<input type="hidden" name="create" value="1" />
		<a href="javascript:void(0)" onclick="$('#ccm-user-form').get(0).submit()" class="ccm-button-right accept"><span><?php echo t('Create User')?></span></a>
	</div>	

	<div class="ccm-spacer">&nbsp;</div>
	
	</div>
	</form>
