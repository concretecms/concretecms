<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
Loader::library('search');
Loader::model('search/user');
Loader::model('user_attributes');

$attribs = UserAttributeKey::getList(true);
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

if ($_REQUEST['user_created'] == 1) {
	$message = t('User created successfully. ');
}

if ($_GET['uID']) {
	$uo = UserInfo::getByID($_GET['uID']);
	if (is_object($uo)) {
		$uID = $_REQUEST['uID'];
		if ($_GET['task'] == 'activate') {
			$uo->activate();
			$uo = UserInfo::getByID($_GET['uID']);
			$message = t("User activated.");
		}

		if ($_GET['task'] == 'validate_email') {
			$uo->markValidated();
			$uo = UserInfo::getByID($_GET['uID']);
			$message = t("Email marked as valid.");
		}
		
		
		if ($_GET['task'] == 'remove-avatar') {
			$av->removeAvatar($uo->getUserID());
			$this->controller->redirect('/dashboard/users/search?uID=' . $_GET['uID'] . '&task=edit');

		}
		
		if ($_GET['task'] == 'deactivate') {
			$uo->deactivate();
			$uo = UserInfo::getByID($_GET['uID']);
			$message = t("User deactivated.");
		}
		
		
		if ($_POST['edit']) {
			
			if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
				$_POST['uName'] = $_POST['uEmail'];
			}
			
			$username = $_POST['uName'];
			$password = $_POST['uPassword'];
			$passwordConfirm = $_POST['uPasswordConfirm'];
			
			if ($password) {
				if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
					$error[] = t('A password must be between %s and %s characters',USER_PASSWORD_MINIMUM,USER_PASSWORD_MAXIMUM);
				}
			}
			
			if (!$vals->email($_POST['uEmail'])) {
				$error[] = t('Invalid email address provided.');
			} else if (!$valc->isUniqueEmail($_POST['uEmail']) && $uo->getUserEmail() != $_POST['uEmail']) {
				$error[] = t("The email address '%s' is already in use. Please choose another.",$_POST['uEmail']);
			}
			
			if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
				if (strlen($username) < USER_USERNAME_MINIMUM) {
					$error[] = t('A username must be at least %s characters long.',USER_USERNAME_MINIMUM);
				}
	
				if (strlen($username) > USER_USERNAME_MAXIMUM) {
					$error[] = t('A username cannot be more than %s characters long.',USER_USERNAME_MAXIMUM);
				}

				if (strlen($username) >= USER_USERNAME_MINIMUM && !$valc->username($username)) {
					$error[] = t('A username may not contain ", \', >, <, or any spaces.');
				}
				if (!$valc->isUniqueUsername($username) && $uo->getUserName() != $username) {
					$error[] = t("The username '%s' already exists. Please choose another",$username);
				}		
			}
			
			if (strlen($password) >= USER_PASSWORD_MINIMUM && !$valc->password($password)) {
				$error[] = t('A password may not contain ", \', >, <, or any spaces.');
			}
			
			if ($password) {
				if ($password != $passwordConfirm) {
					$error[] = t('The two passwords provided do not match.');
				}
			}
			
			if (!$valt->validate('update_account_' . $_GET['uID'])) {
				$error[] = $valt->getErrorMessage();
			}
		
			if (!$error) {
				// do the registration
				$process = $uo->update($_POST);
				$pr2 = $uo->updateUserAttributes($_POST);
				
				//$db = Loader::db();
				if ($process) {
					if ( is_uploaded_file($_FILES['uAvatar']['tmp_name']) ) {
						$uHasAvatar = $av->updateUserAvatar($_FILES['uAvatar']['tmp_name'], $uo->getUserID());
					}
					
					$uo->updateSelectedUserAttributes($data['editAKID'], $_POST);
					$uo->updateGroups($_POST['gID']);

					$message = t("User updated successfully. ");
					if ($password) {
						$message .= t("Password changed.");
					}
					$editComplete = true;
					// reload user object
					$uo = UserInfo::getByID($_GET['uID']);
				} else {
					$db = Loader::db();
					$error[] = $db->ErrorMsg();
				}
			}		
		}	
	}
}

if ((!is_object($uo))) {
	if ($_REQUEST['task'] == 'search') { 
		$sa = $_GET;
		$sa['uDateAddedStart'] = $dtt->translate('uDateAddedStart', $sa);
		$sa['uDateAddedEnd'] = $dtt->translate('uDateAddedEnd', $sa);
		$sa['uLoggedInDateStart'] = $dtt->translate('uLoggedInDateStart', $sa);
		$sa['uLoggedInDateEnd'] = $dtt->translate('uLoggedInDateEnd', $sa);
	}

	$sa['uVal'] = $_GET['uVal'];
	$s = new UserSearch($sa);
		
	if ($s->getTotal() > 0) {
		if ($_GET['output'] == 'excel') {
			$res = $s->getResult($_GET['sort'], $_GET['start'], $_GET['order'], -1);
		} else {
			$res = $s->getResult($_GET['sort'], $_GET['start'], $_GET['order']);
		}
		$pOptions = $s->paging($_GET['start'], $_GET['order']);
		
		if ($_GET['output'] == 'excel') {
			header("Content-Type: application/vnd.ms-excel");
			header("Cache-control: private");
			header("Pragma: public");
			$date = date('Ymd');
			header("Content-Disposition: inline; filename=user_report_{$date}.xls"); 
			header("Content-Title: User Report - Run on {$date}");
			
			echo("<table><tr>");
			echo("<td><b>".t('Username')."</b></td>");
			echo("<td><b>".t('Email Address')."</b></td>");
			echo("<td><b>".t('Registered')."</b></td>");
			echo("<td><b>".t('# Logins')."</b></td>");
			$attribs = UserAttributeKey::getList();
			foreach($attribs as $ak) {
				echo("<td><b>" . $ak->getKeyName() . "</b></td>");
			}
			echo("</tr>");
			while ($row = $res->fetchRow()) {
				echo("<tr>");
				echo("<td>{$row['uName']}</td>");
				echo("<td>{$row['uEmail']}</td>");
				echo("<td>" . date('Y-m-d H:i:s', strtotime($row['uDateAdded'])) . "</td>");
				echo("<td>{$row['uNumLogins']}</td>");
				foreach($attribs as $ak) {
					echo("<td>" . $ak->getUserValue($row['uID']) . "</td>");
				}
				echo("</tr>");
			}
			echo("</table>");
			exit;
		}
			
	}
}

$section = 'users';
if (is_object($uo)) { 
	$gl = new GroupList($uo, true);
	if ($_GET['task'] == 'edit' || $_POST['edit'] && !$editComplete) { ?>

		<div class="wrapper">
		<div class="actions">
		<span class="required">*</span> - <?php echo t('required field')?>
		</div>
		
		<?php 
		$uName = ($_POST) ? $_POST['uName'] : $uo->getUserName();
		$uEmail = ($_POST) ? $_POST['uEmail'] : $uo->getUserEmail();
		?>
		
	<script>	
	function editAttrVal(attId,cancel){
		if(!cancel){
			$('#attUnknownWrap'+attId).css('display','none');
			$('#attEditWrap'+attId).css('display','block');
			$('#attValChanged'+attId).val(attId);	
		}else{
			$('#attUnknownWrap'+attId).css('display','block');
			$('#attEditWrap'+attId).css('display','none');
			$('#attValChanged'+attId).val(0);	
		}
	}
	</script>
		
		
	<h1><span><?php echo t('Edit Account')?></span></h1>
	
	<div class="ccm-dashboard-inner">

		<form method="post" enctype="multipart/form-data" id="ccm-user-form" action="<?php echo $this->url('/dashboard/users/search?uID=' . $_GET['uID'])?>">
		<?php echo $valt->output('update_account_' . $_GET['uID'])?>
		<input type="hidden" name="_disableLogin" value="1">
	
		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td colspan="3" class="header"><?php echo t('Core Information')?></td>
		</tr>
		<tr>
			<td class="subheader"><?php echo t('Username')?> <span class="required">*</span></td>
			<td class="subheader"><?php echo t('Email Address')?> <span class="required">*</span></td>
			<td class="subheader"><?php echo t('User Avatar')?></td>
		</tr>	
		<tr>
			<td><?php  if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?><?php echo $uo->getUserName()?><?php  } else { ?><input type="text" name="uName" autocomplete="off" value="<?php echo $uName?>" style="width: 94%"><?php  } ?></td>
			<td><input type="text" name="uEmail" autocomplete="off" value="<?php echo $uEmail?>" style="width: 94%"></td>
			<td><input type="file" name="uAvatar" style="width: 94%" /> <input type="hidden" name="uHasAvatar" value="<?php echo $uo->hasAvatar()?>" />
			
			<?php  if ($uo->hasAvatar()) { ?>
			<input type="button" onclick="location.href='<?php echo $this->url('/dashboard/users/search?uID=' . $uID . '&task=remove-avatar')?>'" value="<?php echo t('Remove Avatar')?>" />
			<?php  } ?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="header"><?php echo t('Change Password')?></td>
		</tr>
		<tr>
			<td class="subheader"><?php echo t('Password')?></td>
			<td class="subheader" colspan="2"><?php echo t('Password (Confirm)')?></td>
		</tr>	
		<tr>
			<td><input type="password" name="uPassword" autocomplete="off" value="" style="width: 94%"></td>
			<td><input type="password" name="uPasswordConfirm" autocomplete="off" value="" style="width: 94%"></td>
			<td><?php echo t('(Leave these fields blank to keep the same password)')?></td>
		</tr>
		<tr>
			<td colspan="3" class="header"><?php echo t('Other Information (Click the checkbox to modify existing values)')?></td>
		</tr>
		<?php 
	
		$attribs = UserAttributeKey::getList();
		foreach($attribs as $ak) { 
			$attrVal=$ak->getUserValue($_REQUEST['uID']);
			?>
			<tr>
				<td valign="top" class="field" style="text-align: right">
					<?php  $editAKID = array();
					if (is_array($_REQUEST['editAKID'])) {
						$editAKID = $_REQUEST['editAKID'];
					} ?>
					<?php echo wordwrap($ak->getKeyName(),20,'<br/>')?>: 
					<input id="attValChanged<?php echo $ak->getKeyID()?>" type="hidden" value="<?php echo ( strlen($attrVal) )?$ak->getKeyID():0 ?>" name="editAKID[]" />
				</td>
				<td colspan="2"> 
					<?php  if( strlen($attrVal) ){ ?>
						<div id="attEditWrap<?php echo $ak->getKeyID()?>"><?php echo $ak->outputHTML($uo->getUserID())?>&nbsp;</div>
					<?php  }else{ ?>
						<div id="attEditWrap<?php echo $ak->getKeyID()?>" style="display:none"><?php echo $ak->outputHTML($uo->getUserID())?> <a onclick="editAttrVal(<?php echo $ak->getKeyID()?>,1)"><?php echo t('Cancel')?></a></div>
						<div id="attUnknownWrap<?php echo $ak->getKeyID()?>"><?php echo t('Unknown')?> <a onclick="editAttrVal(<?php echo $ak->getKeyID()?>)"><?php echo t('Edit')?></a></div>
					<?php  } ?>
				</td>
			</tr>	
		<?php  } ?>
		
		<tr>
			<td colspan="3" class="header">
				<a id="groupSelector" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector.php?mode=groups" dialog-title="<?php echo t('Add Groups')?>" dialog-modal="false" style="float: right"><?php echo t('Add Group')?></a>
				<?php echo t('Groups')?>
			</td>
		</tr>
		<?php  $gArray = $gl->getGroupList(); ?>
		<tr>
			<td colspan="3">
			<?php  foreach ($gArray as $g) { ?>
				<input type="checkbox" name="gID[]" value="<?php echo $g->getGroupID()?>" style="vertical-align: middle" <?php  
					if (is_array($_POST['gID'])) {
						if (in_array($g->getGroupID(), $_POST['gID'])) {
							echo(' checked ');
						}
					} else {
						if ($g->inGroup()) {
							echo(' checked ');
						}
					}
				?> /> <?php echo $g->getGroupName()?><br>
			<?php  } ?>
			
			<div id="ccm-additional-groups"></div>
			
			</td>
		</tr>
		</table>
		</div>
		
		<div class="ccm-buttons">
		<input type="hidden" name="edit" value="1" />
		<a href="<?php echo $this->url('/dashboard/users/search?uID=' . $_GET['uID'])?>" class="ccm-button-left cancel"><span><?php echo t('Cancel')?></span></a>
		<a href="javascript:void(0)" onclick="$('#ccm-user-form').get(0).submit()" class="ccm-button-right accept"><span><?php echo t('Update User')?></span></a>
		</div>	
		
		<div class="ccm-spacer">&nbsp;</div>
		</form>
	</div>
	
	<?php  } else { ?>
	<h1><span><?php echo t('View User')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		<div class="actions" >			<?php  define('USER_VALIDATE_EMAIL', 1);?>

			<?php  print $ih->button(t('Edit User'), $this->url('/dashboard/users/search?uID=' . $uID) . '&task=edit', 'left');?>

			<?php  if (USER_VALIDATE_EMAIL == true) { ?>
				<?php  if ($uo->isValidated() < 1) { ?>
				<?php  print $ih->button(t('Mark Email as Valid'), $this->url('/dashboard/users/search?uID=' . $uID . '&task=validate_email'), 'left');?>
				<?php  } ?>
			<?php  } ?>
			
			<?php  if ($uo->isActive()) { ?>
				<?php  print $ih->button(t('Deactivate User'), $this->url('/dashboard/users/search?uID=' . $uID . '&task=deactivate'), 'left');?>
			<?php  } else { ?>
				<?php  print $ih->button(t('Activate User'), $this->url('/dashboard/users/search?uID=' . $uID . '&task=activate'), 'left');?>
			<?php  } ?>
			
			<?php  if ($u->isSuperUser()) { ?>
				<?php  
				
				$loginAsUserConfirm = t('This will end your current session and sign you in as %s', $uo->getUserName());
				
				print $ih->button_js(t('Sign In as User'), 'loginAsUser()', 'left');?>

				<script type="text/javascript">
				loginAsUser = function() {
					if (confirm('<?php echo $loginAsUserConfirm?>')) { 
						location.href = "<?php echo $this->url('/dashboard/users/search', 'sign_in_as_user', $uo->getUserID(), $valt->generate('sudo'))?>";				
					}
				}
				</script>

			<?php  } else { ?>
				<?php  print $ih->button_js(t('Sign In as User'), 'alert(\'' . t('Only the super user may sign in as another account.') . '\')', 'left', 'ccm-button-inactive');?>
			<?php  } ?>

		</div>
		
		<h2><?php echo t('Required Information')?></h2>
		
		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td><?php echo $av->outputUserAvatar($uo)?></td>
			<td><?php echo $uo->getUserName()?><br/>
			<a href="mailto:<?php echo $uo->getUserEmail()?>"><?php echo $uo->getUserEmail()?></a><br/>
			<?php echo $uo->getUserDateAdded()?>
			<?php  if (USER_VALIDATE_EMAIL) { ?><br/>
				<?php echo t('Full Record')?>: <strong><?php echo  ($uo->isFullRecord()) ? "Yes" : "No" ?></strong>
				&nbsp;&nbsp;
				<?php echo t('Email Validated')?>: <strong><?php 
					switch($uo->isValidated()) {
						case '-1':
							print t('Unknown');
							break;
						case '0':
							print t('No');
							break;
						case '1':
							print t('Yes');
							break;
					}?>
					</strong>
			<?php  } ?></td>
		</tr>
		</table>
		</div>

		
		<?php 
		$attribs = UserAttributeKey::getList(true);
		if (count($attribs) > 0) { ?>
		<h2><?php echo t('Other Information')?></h2>

		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">


		<?php  
		for ($i = 0; $i < count($attribs); $i = $i + 3) { 			
			$uk = $attribs[$i]; 
			$uk2 = $attribs[$i+1]; 
			$uk3 = $attribs[$i+2]; 		
			
			?>
			
		<tr>
			<td class="subheader" style="width: 33%"><?php echo $uk->getKeyName()?></td>
			<?php  if (is_object($uk2)) { ?><td  style="width: 33%" class="subheader"><?php echo $uk2->getKeyName()?></td><?php  } else { ?><td  style="width: 33%" class="subheader">&nbsp;</td><?php  } ?>
			<?php  if (is_object($uk3)) { ?><td  style="width: 33%"class="subheader"><?php echo $uk3->getKeyName()?></td><?php  } else { ?><td style="width: 33%" class="subheader">&nbsp;</td><?php  } ?>
		</tr>
		<tr>
			<td><?php echo $uk->getUserValue($uo->getUserID())?></td>
			<?php  if (is_object($uk2)) { ?><td><?php echo $uk2->getUserValue($uo->getUserID())?></td><?php  } else { ?><td style="width: 33%">&nbsp;</td><?php  } ?>
			<?php  if (is_object($uk3)) { ?><td><?php echo $uk3->getUserValue($uo->getUserID())?></td><?php  } else { ?><td>&nbsp;</td><?php  } ?>
		</tr>
		<?php  } ?>
		
		</table>
		</div>
		
		<?php  }  ?>
		
		<h2><?php echo t('Groups')?></h2>

		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td colspan="2" class="header"><?php echo t('Group')?></td>
			<td class="header"><?php echo t('Date Entered')?></td>
		</tr>
		<?php  $gArray = $gl->getGroupList(); ?>
		<tr>
			<td colspan="2">
				<?php  $enteredArray = array(); ?>
				<?php  foreach ($gArray as $g) { ?>
					<?php  if ($g->inGroup()) {
						echo($g->getGroupName() . '<br>');
						$enteredArray[] = $g->getGroupDateTimeEntered();
					} ?>
				<?php  } ?>
			</td>
			<td>
			<?php  foreach ($enteredArray as $dateTime) {
				if ($dateTime != '0000-00-00 00:00:00') {
					echo($dateTime . '<br>');
				} else {
					echo('<br>');
				}
			} ?>
			</td>
		</tr>
		</table>
		</div>
	</div>
		


	<h1><span><?php echo t('Delete User')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		<?php 
		$delConfirmJS = t('Are you sure you want to permanently remove this user?');
		if ($uo->getUserID() == USER_SUPER_ID) { ?>
			<?php echo t('You may not remove the super user account.')?>
		<?php  } else if($u->isSuperUser() == false){ ?>
			<?php echo t('You must be logged in as %s to remove user accounts.', USER_SUPER)?>
			
		<?php  }else{ ?>   
			
			<script type="text/javascript">
			deleteUser = function() {
				if (confirm('<?php echo $delConfirmJS?>')) { 
					location.href = "<?php echo $this->url('/dashboard/users/search', 'delete', $uo->getUserID(), $valt->generate('delete_account'))?>";				
				}
			}
			</script>

			<?php  print $ih->button_js(t('Delete User Account'), "deleteUser()", 'left');?>

		<?php  } ?>
		<div class="ccm-spacer"></div>
	</div>
	<?php  } ?>

<?php 

} else { ?>

	<h1><span><?php echo t('Search User Accounts')?></span></h1>
	
	<div class="ccm-dashboard-inner">

	<div id="ccm-user-search">
	
	<a href="javascript:void(0)" id="ccm-user-search-advanced-control" <?php  if ($_REQUEST['task'] == 'search') { ?> style="display: none" <?php  } ?>><?php echo t('Advanced Search')?> &gt;</a>
	
	<div id="ccm-user-search-simple" <?php  if ($_REQUEST['task'] == 'search') { ?> style="display: none" <?php  } ?>>
	<br/>
	
	<h3><?php echo t('Username or Email Address Contains:')?></h3>
	<form method="get" id="ccm-user-search-simple-form" action="<?php echo $this->url('/dashboard/users/search')?>">
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td>
	<input type="hidden" name="task" value="simple_search" />
	<input type="text" name="uVal" value="<?php echo $_REQUEST['uVal']?>" style="width: 200px" />
	</td>
	<td style="padding-left: 10px">
	<a href="javascript:void(0)" onclick="$('#ccm-user-search-simple-form').get(0).submit()" class="ccm-button"><span><?php echo t('Search Users')?></span></a>
	</td>
	</tr>
	</table>
	</div>
	</form>
	
	</div>
	
	<a href="javascript:void(0)" id="ccm-user-search-simple-control" <?php  if ($_REQUEST['task'] != 'search') { ?> style="display: none" <?php  } ?>>&lt; <?php echo t('Back to Simple Search')?></a>

	<div id="ccm-user-search-advanced" <?php  if ($_REQUEST['task'] == 'search') { ?> style="display: block" <?php  } ?>>
	
	<form method="get" action="<?php echo $this->url('/dashboard/users/search')?>" id="ccm-user-search-advanced-form">
	
	<input type="hidden" name="task" value="search" />
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader"><?php echo t('Username')?></td>
		<td><input type="text" name="uName" autocomplete="off" value="<?php echo $_GET['uName']?>" style="width: 100%"></td>
		<td class="subheader"><?php echo t('Email Address')?></td>
		<td><input type="text" name="uEmail" autocomplete="off" value="<?php echo $_GET['uEmail']?>" style="width: 100%"></td>
	</tr>
	<tr>
		<td class="subheader"><?php echo t('Registered between:')?></td>
		<td><?php  print $dtt->datetime('uDateAddedStart', $dtt->translate('uDateAddedStart', $_GET), true)?></td>
		<td class="subheader"><?php echo t('and:')?> </td>
		<td><?php  print $dtt->datetime('uDateAddedEnd', $dtt->translate('uDateAddedEnd', $_GET), true)?></td>
	</tr>
	<?php  if (USER_VALIDATE_EMAIL) { ?>
	<tr>
		<td class="subheader"><?php echo t('Email Validation')?></td>
		<td>
			<?php echo $form->checkbox('uIsValidated[]', 0, true)?> <?php echo t('Non-Validated')?>
			<?php echo $form->checkbox('uIsValidated[]', 1, true)?> <?php echo t('Validated')?>	
		</td>	
		<td class="subheader"><?php echo t('Record Types')?></td>
		<td>
			<?php echo $form->checkbox('uIsFullRecord[]', 1, true)?> <?php echo t('Full')?>	
			<?php echo $form->checkbox('uIsFullRecord[]', 0, true)?> <?php echo t('Email Only ')?>
		</td>	
	</tr>
	<?php  } ?>
	<?php  /*
	<tr>
		<td class="subheader">Logged in between:</td>
		<td><?php  print $dtt->datetime('uLoggedInDateStart', $_GET['uLoggedInDateStart'], true)?></td>
		<td class="subheader">and: </td>
		<td><?php  print $dtt->datetime('uLoggedInDateEnd', $_GET['uLoggedInDateEnd'], true)?></td>
	</tr>
	*/ ?>
	<?php 
	
	$attribs = UserAttributeKey::getList();
	$mod = false;
	for ($i = 0; $i < count($attribs); $i = $i + 2) {
		$ak = $attribs[$i]; ?>
		<tr>
			<td valign="top" class="subheader">
				<?php echo wordwrap($ak->getKeyName(),20,'<br/>')?>:</td>
			<td valign="top"><?php echo $ak->outputSearchHTML()?></td>
			<?php  if (is_object($attribs[$i+1])){
				$ak = $attribs[$i+1];
			?>
			<td valign="top" class="subheader">
				<?php echo wordwrap($ak->getKeyName(),20,'<br/>')?>:</td>
			<td valign="top"><?php echo $ak->outputSearchHTML()?></td>
			
			<?php  } else { ?>
			<td colspan="2">&nbsp;</td>
			<?php  } ?>
		</tr>
	<?php  } ?>
	<tr>
		<td colspan="4" class="header" style="text-align: right">
			<a href="javascript:void(0)" onclick="$('#ccm-user-search-advanced-form').get(0).submit()" class="ccm-button-right"><span><?php echo t('Search Users')?></span></a>
		</td>
	</tr>
	</table>
	</div>

	</form>	
	
	</div>
	</div>
	
	<h2><?php echo t('Results')?></h2>
	
		<?php  if ($s->getTotal() > 0) { ?>
	

	<?php  
		$variables['output'] = 'excel';
		$url = Search::qsReplace($variables);
	?>
	<a href="<?php echo $url?>" style="float: right; line-height: 18px; padding-left: 20px; background: transparent url(<?php echo ASSETS_URL_IMAGES?>/icons/excel.png) no-repeat"><?php echo t('Export to Excel')?></a>

	<?php  include(DIR_FILES_ELEMENTS_CORE . '/search_results_top.php'); ?>
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<?php echo $s->printHeader(t('User Name'),'uName',1)?>
		<?php echo $s->printHeader(t('Email Address'),'uEmail',1)?>
		<?php echo $s->printHeader(t('Date Added'),'uDateAdded',1)?>
		<?php echo $s->printHeader(t('# Logins'), 'uNumLogins',1)?>
	</tr>
	<?php  if ($s->getTotal() > 0) { 
		while ($row = $res->fetchRow()) { ?>
		<tr>
			<?php echo $s->printRow($row['uName'], 'uName', $this->url('/dashboard/users/search?uID=' . $row['uID']))?>
			<?php echo $s->printRow($row['uEmail'], 'uEmail', 'mailto:' . $row['uEmail'])?>
			<?php echo $s->printRow($row['uDateAdded'], 'uDateAdded')?>
			<?php echo $s->printRow($row['uNumLogins'], 'uNumLogins')?>
		</tr>
		<?php  } 
	} ?>
	</table>
	</div>
	
	<?php  if ($pOptions['needPaging']) { ?>
		<br><br>
		<?php  include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php'); ?>			
	<?php  } ?>
	
	<?php  } else { ?>
		
		<strong><?php echo t('No users found.')?></strong>
		
	<?php  } ?>
	
	</div>
	
	
	
<?php 
	
	}

?>

<script type="text/javascript">
$(function() {

	$("#groupSelector").dialog();
	ccm_triggerSelectGroup = function(gID, gName) {
		var html = '<input type="checkbox" name="gID[]" value="' + gID + '" style="vertical-align: middle" checked /> ' + gName + '<br/>';
		$("#ccm-additional-groups").append(html);
	}
	$("#ccm-user-search-advanced-control").click(function() {
		$("#ccm-user-search-simple").hide();
		$("#ccm-user-search-simple-control").show();
		$(this).hide();
		$("#ccm-user-search-advanced").show();
	});

	$("#ccm-user-search-simple-control").click(function() {
		$("#ccm-user-search-advanced").hide();
		$("#ccm-user-search-advanced-control").show();
		$(this).hide();
		$("#ccm-user-search-simple").show();
	});
	
});
</script>