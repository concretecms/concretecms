<?
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

if (intval($_GET['uID'])) {
	$uo = UserInfo::getByID(intval($_GET['uID']));
	if (is_object($uo)) {
		$uID = intval($_REQUEST['uID']);
		
		if ($_GET['task'] == 'activate') {
			if( !$valt->validate("user_activate") ){
				throw new Exception('Invalid token.  Unable to activate user.');
			}else{		
				$uo->activate();
				$uo = UserInfo::getByID(intval($_GET['uID']));
				$message = t("User activated.");
			}
		}

		if ($_GET['task'] == 'validate_email') {
			$uo->markValidated();
			$uo = UserInfo::getByID(intval($_GET['uID']));
			$message = t("Email marked as valid.");
		}
		
		
		if ($_GET['task'] == 'remove-avatar') {
			$av->removeAvatar($uo->getUserID());
			$this->controller->redirect('/dashboard/users/search?uID=' . intval($_GET['uID']) . '&task=edit');

		}
		
		if ($_GET['task'] == 'deactivate') {
			if( !$valt->validate("user_deactivate") ){
				throw new Exception('Invalid token.  Unable to deactivate user.');
			}else{
				$uo->deactivate();
				$uo = UserInfo::getByID(intval($_GET['uID']));
				$message = t("User deactivated.");
			}
		}
		
		
		if ($_POST['edit']) {
			
			$username = $_POST['uName'];
			$username = trim($username);
			$username = ereg_replace(" +", " ", $username);
			
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

				/*
				if (strlen($username) >= USER_USERNAME_MINIMUM && !$vals->alphanum($username,USER_USERNAME_ALLOW_SPACES)) {
					if(USER_USERNAME_ALLOW_SPACES) {
						$e->add(t('A username may only contain letters, numbers and spaces.'));
					} else {
						$e->add(t('A username may only contain letters or numbers.'));
					}
					
				}
				*/
				
				if (strlen($username) >= USER_USERNAME_MINIMUM && !$valc->username($username)) {
					if(USER_USERNAME_ALLOW_SPACES) {
						$error[] = t('A username may only contain letters, numbers and spaces.');
					} else {
						$error[] = t('A username may only contain letters or numbers.');
					}
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
			
			if (!$valt->validate('update_account_' . intval($_GET['uID']) )) {
				$error[] = $valt->getErrorMessage();
			}
		
			if (!$error) {
				// do the registration
				$pr2 = $uo->updateUserAttributes($_POST);
				$process = $uo->update($_POST);
				
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
					$uo = UserInfo::getByID(intval($_GET['uID']));
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
					echo("<td>" . $ak->getUserValue(intval($row['uID'])) . "</td>");
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
		<span class="required">*</span> - <?=t('required field')?>
		</div>
		
		<?
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
		
		
	<h1><span><?=t('Edit Account')?></span></h1>
	
	<div class="ccm-dashboard-inner">

		<form method="post" enctype="multipart/form-data" id="ccm-user-form" action="<?=$this->url('/dashboard/users/search?uID=' . intval($_GET['uID']) )?>">
		<?=$valt->output('update_account_' . intval($_GET['uID']) )?>
		<input type="hidden" name="_disableLogin" value="1">
	
		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td colspan="3" class="header"><?=t('Core Information')?></td>
		</tr>
		<tr>
			<td class="subheader"><?=t('Username')?> <span class="required">*</span></td>
			<td class="subheader"><?=t('Email Address')?> <span class="required">*</span></td>
			<td class="subheader"><?=t('User Avatar')?></td>
		</tr>	
		<tr>
			<td><input type="text" name="uName" autocomplete="off" value="<?=$uName?>" style="width: 94%"></td>
			<td><input type="text" name="uEmail" autocomplete="off" value="<?=$uEmail?>" style="width: 94%"></td>
			<td><input type="file" name="uAvatar" style="width: 94%" /> <input type="hidden" name="uHasAvatar" value="<?=$uo->hasAvatar()?>" />
			
			<? if ($uo->hasAvatar()) { ?>
			<input type="button" onclick="location.href='<?=$this->url('/dashboard/users/search?uID=' . intval($uID) . '&task=remove-avatar')?>'" value="<?=t('Remove Avatar')?>" />
			<? } ?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="header"><?=t('Change Password')?></td>
		</tr>
		<tr>
			<td class="subheader"><?=t('Password')?></td>
			<td class="subheader" colspan="2"><?=t('Password (Confirm)')?></td>
		</tr>	
		<tr>
			<td><input type="password" name="uPassword" autocomplete="off" value="" style="width: 94%"></td>
			<td><input type="password" name="uPasswordConfirm" autocomplete="off" value="" style="width: 94%"></td>
			<td><?=t('(Leave these fields blank to keep the same password)')?></td>
		</tr>
		<tr>
			<td colspan="3" class="header"><?=t('Other Information (Click the checkbox to modify existing values)')?></td>
		</tr>
		<?
	
		$attribs = UserAttributeKey::getList();
		foreach($attribs as $ak) { 
			$attrVal=$ak->getUserValue(intval($_REQUEST['uID']));
			?>
			<tr>
				<td valign="top" class="field" style="text-align: right">
					<? $editAKID = array();
					if (is_array($_REQUEST['editAKID'])) {
						$editAKID = $_REQUEST['editAKID'];
					} ?>
					<?=wordwrap($ak->getKeyName(),20,'<br/>')?>: 
					<input id="attValChanged<?=$ak->getKeyID()?>" type="hidden" value="<?=( strlen($attrVal) )?$ak->getKeyID():0 ?>" name="editAKID[]" />
				</td>
				<td colspan="2"> 
					<? if( strlen($attrVal) ){ ?>
						<div id="attEditWrap<?=$ak->getKeyID()?>"><?=$ak->outputHTML($uo->getUserID())?>&nbsp;</div>
					<? }else{ ?>
						<div id="attEditWrap<?=$ak->getKeyID()?>" style="display:none"><?=$ak->outputHTML($uo->getUserID())?> <a onclick="editAttrVal(<?=$ak->getKeyID()?>,1)"><?=t('Cancel')?></a></div>
						<div id="attUnknownWrap<?=$ak->getKeyID()?>"><?=t('Unknown')?> <a onclick="editAttrVal(<?=$ak->getKeyID()?>)"><?=t('Edit')?></a></div>
					<? } ?>
				</td>
			</tr>	
		<? } ?>
		
		<tr>
			<td colspan="3" class="header">
				<a id="groupSelector" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector.php?mode=groups" dialog-title="<?=t('Add Groups')?>" dialog-modal="false" style="float: right"><?=t('Add Group')?></a>
				<?=t('Groups')?>
			</td>
		</tr>
		<? $gArray = $gl->getGroupList(); ?>
		<tr>
			<td colspan="3">
			<? foreach ($gArray as $g) { ?>
				<input type="checkbox" name="gID[]" value="<?=$g->getGroupID()?>" style="vertical-align: middle" <? 
					if (is_array($_POST['gID'])) {
						if (in_array($g->getGroupID(), $_POST['gID'])) {
							echo(' checked ');
						}
					} else {
						if ($g->inGroup()) {
							echo(' checked ');
						}
					}
				?> /> <?=$g->getGroupName()?><br>
			<? } ?>
			
			<div id="ccm-additional-groups"></div>
			
			</td>
		</tr>
		</table>
		</div>
		
		<div class="ccm-buttons">
		<input type="hidden" name="edit" value="1" />
		<a href="<?=$this->url('/dashboard/users/search?uID=' . intval($_GET['uID']))?>" class="ccm-button-left cancel"><span><?=t('Cancel')?></span></a>
		<a href="javascript:void(0)" onclick="$('#ccm-user-form').get(0).submit()" class="ccm-button-right accept"><span><?=t('Update User')?></span></a>
		</div>	
		
		<div class="ccm-spacer">&nbsp;</div>
		</form>
	</div>
	
	<? } else { ?>
	<h1><span><?=t('View User')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		<div class="actions" >			<? define('USER_VALIDATE_EMAIL', 1);?>

			<? print $ih->button(t('Edit User'), $this->url('/dashboard/users/search?uID=' . intval($uID) ) . '&task=edit', 'left');?>

			<? if (USER_VALIDATE_EMAIL == true) { ?>
				<? if ($uo->isValidated() < 1) { ?>
				<? print $ih->button(t('Mark Email as Valid'), $this->url('/dashboard/users/search?uID=' . intval($uID) . '&task=validate_email'), 'left');?>
				<? } ?>
			<? } ?>
			
			<? if ($uo->isActive()) { ?>
				<? print $ih->button(t('Deactivate User'), $this->url('/dashboard/users/search?uID=' . intval($uID) . '&task=deactivate&ccm_token='.$valt->generate('user_deactivate')), 'left');?>
			<? } else { ?>
				<? print $ih->button(t('Activate User'), $this->url('/dashboard/users/search?uID=' . intval($uID) . '&task=activate&ccm_token='.$valt->generate('user_activate')), 'left');?>
			<? } ?>
			
			<? if ($u->isSuperUser()) { ?>
				<? 
				
				$loginAsUserConfirm = t('This will end your current session and sign you in as %s', $uo->getUserName());
				
				print $ih->button_js(t('Sign In as User'), 'loginAsUser()', 'left');?>

				<script type="text/javascript">
				loginAsUser = function() {
					if (confirm('<?=$loginAsUserConfirm?>')) { 
						location.href = "<?=$this->url('/dashboard/users/search', 'sign_in_as_user', $uo->getUserID(), $valt->generate('sudo'))?>";				
					}
				}
				</script>

			<? } else { ?>
				<? print $ih->button_js(t('Sign In as User'), 'alert(\'' . t('Only the super user may sign in as another account.') . '\')', 'left', 'ccm-button-inactive');?>
			<? } ?>

		</div>
		
		<h2><?=t('Required Information')?></h2>
		
		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td><?=$av->outputUserAvatar($uo)?></td>
			<td><?=$uo->getUserName()?><br/>
			<a href="mailto:<?=$uo->getUserEmail()?>"><?=$uo->getUserEmail()?></a><br/>
			<?=$uo->getUserDateAdded()?>
			<? if (USER_VALIDATE_EMAIL) { ?><br/>
				<?=t('Full Record')?>: <strong><?= ($uo->isFullRecord()) ? "Yes" : "No" ?></strong>
				&nbsp;&nbsp;
				<?=t('Email Validated')?>: <strong><?
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
			<? } ?></td>
		</tr>
		</table>
		</div>

		
		<?
		$attribs = UserAttributeKey::getList(true);
		if (count($attribs) > 0) { ?>
		<h2><?=t('Other Information')?></h2>

		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">


		<? 
		for ($i = 0; $i < count($attribs); $i = $i + 3) { 			
			$uk = $attribs[$i]; 
			$uk2 = $attribs[$i+1]; 
			$uk3 = $attribs[$i+2]; 		
			
			?>
			
		<tr>
			<td class="subheader" style="width: 33%"><?=$uk->getKeyName()?></td>
			<? if (is_object($uk2)) { ?><td  style="width: 33%" class="subheader"><?=$uk2->getKeyName()?></td><? } else { ?><td  style="width: 33%" class="subheader">&nbsp;</td><? } ?>
			<? if (is_object($uk3)) { ?><td  style="width: 33%"class="subheader"><?=$uk3->getKeyName()?></td><? } else { ?><td style="width: 33%" class="subheader">&nbsp;</td><? } ?>
		</tr>
		<tr>
			<td><?=$uk->getUserValue($uo->getUserID())?></td>
			<? if (is_object($uk2)) { ?><td><?=$uk2->getUserValue($uo->getUserID())?></td><? } else { ?><td style="width: 33%">&nbsp;</td><? } ?>
			<? if (is_object($uk3)) { ?><td><?=$uk3->getUserValue($uo->getUserID())?></td><? } else { ?><td>&nbsp;</td><? } ?>
		</tr>
		<? } ?>
		
		</table>
		</div>
		
		<? }  ?>
		
		<h2><?=t('Groups')?></h2>

		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td colspan="2" class="header"><?=t('Group')?></td>
			<td class="header"><?=t('Date Entered')?></td>
		</tr>
		<? $gArray = $gl->getGroupList(); ?>
		<tr>
			<td colspan="2">
				<? $enteredArray = array(); ?>
				<? foreach ($gArray as $g) { ?>
					<? if ($g->inGroup()) {
						echo($g->getGroupName() . '<br>');
						$enteredArray[] = $g->getGroupDateTimeEntered();
					} ?>
				<? } ?>
			</td>
			<td>
			<? foreach ($enteredArray as $dateTime) {
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
		


	<h1><span><?=t('Delete User')?></span></h1>
	
	<div class="ccm-dashboard-inner">
		<?
		$delConfirmJS = t('Are you sure you want to permanently remove this user?');
		if ($uo->getUserID() == USER_SUPER_ID) { ?>
			<?=t('You may not remove the super user account.')?>
		<? } else if($u->isSuperUser() == false){ ?>
			<?=t('You must be logged in as %s to remove user accounts.', USER_SUPER)?>
			
		<? }else{ ?>   
			
			<script type="text/javascript">
			deleteUser = function() {
				if (confirm('<?=$delConfirmJS?>')) { 
					location.href = "<?=$this->url('/dashboard/users/search', 'delete', $uo->getUserID(), $valt->generate('delete_account'))?>";				
				}
			}
			</script>

			<? print $ih->button_js(t('Delete User Account'), "deleteUser()", 'left');?>

		<? } ?>
		<div class="ccm-spacer"></div>
	</div>
	<? } ?>

<?

} else { ?>

	<h1><span><?=t('Search User Accounts')?></span></h1>
	
	<div class="ccm-dashboard-inner">

	<div id="ccm-user-search">
	
	<a href="javascript:void(0)" id="ccm-user-search-advanced-control" <? if ($_REQUEST['task'] == 'search') { ?> style="display: none" <? } ?>><?=t('Advanced Search')?> &gt;</a>
	
	<div id="ccm-user-search-simple" <? if ($_REQUEST['task'] == 'search') { ?> style="display: none" <? } ?>>
	<br/>
	
	<h3><?=t('Username or Email Address Contains:')?></h3>
	<form method="get" id="ccm-user-search-simple-form" action="<?=$this->url('/dashboard/users/search')?>">
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td>
	<input type="hidden" name="task" value="simple_search" />
	<input type="text" name="uVal" value="<?=htmlentities($_REQUEST['uVal'])?>" style="width: 200px" />
	</td>
	<td style="padding-left: 10px">
	<a href="javascript:void(0)" onclick="$('#ccm-user-search-simple-form').get(0).submit()" class="ccm-button"><span><?=t('Search Users')?></span></a>
	</td>
	</tr>
	</table>
	</div>
	</form>
	
	</div>
	
	<a href="javascript:void(0)" id="ccm-user-search-simple-control" <? if ($_REQUEST['task'] != 'search') { ?> style="display: none" <? } ?>>&lt; <?=t('Back to Simple Search')?></a>

	<div id="ccm-user-search-advanced" <? if ($_REQUEST['task'] == 'search') { ?> style="display: block" <? } ?>>
	
	<form method="get" action="<?=$this->url('/dashboard/users/search')?>" id="ccm-user-search-advanced-form">
	
	<input type="hidden" name="task" value="search" />
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader"><?=t('Username')?></td>
		<td><input type="text" name="uName" autocomplete="off" value="<?=htmlentities($_GET['uName'])?>" style="width: 100%"></td>
		<td class="subheader"><?=t('Email Address')?></td>
		<td><input type="text" name="uEmail" autocomplete="off" value="<?=htmlentities($_GET['uEmail'])?>" style="width: 100%"></td>
	</tr>
	<tr>
		<td class="subheader"><?=t('Registered between:')?></td>
		<td><? print $dtt->datetime('uDateAddedStart', $dtt->translate('uDateAddedStart', $_GET), true)?></td>
		<td class="subheader"><?=t('and:')?> </td>
		<td><? print $dtt->datetime('uDateAddedEnd', $dtt->translate('uDateAddedEnd', $_GET), true)?></td>
	</tr>
	<? if (USER_VALIDATE_EMAIL) { ?>
	<tr>
		<td class="subheader"><?=t('Email Validation')?></td>
		<td>
			<?=$form->checkbox('uIsValidated[]', 0, true)?> <?=t('Non-Validated')?>
			<?=$form->checkbox('uIsValidated[]', 1, true)?> <?=t('Validated')?>	
		</td>	
		<td class="subheader"><?=t('Record Types')?></td>
		<td>
			<?=$form->checkbox('uIsFullRecord[]', 1, true)?> <?=t('Full')?>	
			<?=$form->checkbox('uIsFullRecord[]', 0, true)?> <?=t('Email Only ')?>
		</td>	
	</tr>
	<? } ?>
	<? /*
	<tr>
		<td class="subheader">Logged in between:</td>
		<td><? print $dtt->datetime('uLoggedInDateStart', $_GET['uLoggedInDateStart'], true)?></td>
		<td class="subheader">and: </td>
		<td><? print $dtt->datetime('uLoggedInDateEnd', $_GET['uLoggedInDateEnd'], true)?></td>
	</tr>
	*/ ?>
	<?
	
	$attribs = UserAttributeKey::getList();
	$mod = false;
	for ($i = 0; $i < count($attribs); $i = $i + 2) {
		$ak = $attribs[$i]; ?>
		<tr>
			<td valign="top" class="subheader">
				<?=wordwrap($ak->getKeyName(),20,'<br/>')?>:</td>
			<td valign="top"><?=$ak->outputSearchHTML()?></td>
			<? if (is_object($attribs[$i+1])){
				$ak = $attribs[$i+1];
			?>
			<td valign="top" class="subheader">
				<?=wordwrap($ak->getKeyName(),20,'<br/>')?>:</td>
			<td valign="top"><?=$ak->outputSearchHTML()?></td>
			
			<? } else { ?>
			<td colspan="2">&nbsp;</td>
			<? } ?>
		</tr>
	<? } ?>
	<tr>
		<td colspan="4" class="header" style="text-align: right">
			<a href="javascript:void(0)" onclick="$('#ccm-user-search-advanced-form').get(0).submit()" class="ccm-button-right"><span><?=t('Search Users')?></span></a>
		</td>
	</tr>
	</table>
	</div>

	</form>	
	
	</div>
	</div>
	
	<h2><?=t('Results')?></h2>
	
		<? if ($s->getTotal() > 0) { ?>
	

	<? 
		$variables['output'] = 'excel';
		$url = Search::qsReplace($variables);
	?>
	<a href="<?=htmlentities($url) ?>" style="float: right; line-height: 18px; padding-left: 20px; background: transparent url(<?=ASSETS_URL_IMAGES?>/icons/excel.png) no-repeat"><?=t('Export to Excel')?></a>

	<? include(DIR_FILES_ELEMENTS_CORE . '/search_results_top.php'); ?>
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<?=$s->printHeader(t('User Name'),'uName',1)?>
		<?=$s->printHeader(t('Email Address'),'uEmail',1)?>
		<?=$s->printHeader(t('Date Added'),'uDateAdded',1)?>
		<?=$s->printHeader(t('# Logins'), 'uNumLogins',1)?>
	</tr>
	<? if ($s->getTotal() > 0) { 
		while ($row = $res->fetchRow()) { 
			$uName = '(' . t('None') . ')';
			if ($row['uName']) {
				$uName = $row['uName'];
			}
			?>
		<tr>
			<?=$s->printRow($uName, 'uName', $this->url('/dashboard/users/search?uID=' . intval($row['uID']) ))?>
			<?=$s->printRow($row['uEmail'], 'uEmail', 'mailto:' . $row['uEmail'])?>
			<?=$s->printRow($row['uDateAdded'], 'uDateAdded')?>
			<?=$s->printRow($row['uNumLogins'], 'uNumLogins')?>
		</tr>
		<? } 
	} ?>
	</table>
	</div>
	
	<? if ($pOptions['needPaging']) { ?>
		<br><br>
		<? include(DIR_FILES_ELEMENTS_CORE . '/search_results_paging.php'); ?>			
	<? } ?>
	
	<? } else { ?>
		
		<strong><?=t('No users found.')?></strong>
		
	<? } ?>
	
	</div>
	
	
	
<?
	
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