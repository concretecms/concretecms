<?

Loader::library('search');
Loader::model('search/user');
Loader::model('user_attributes');

$attribs = UserAttributeKey::getList(true);

$uh = Loader::helper('concrete/user');
$txt = Loader::helper('text');
$vals = Loader::helper('validation/strings');
$valc = Loader::helper('concrete/validation');
$dtt = Loader::helper('form/date_time');

if ($_REQUEST['updated_attribute']) {
	$message = 'User Attribute Updated.';
}
if ($_REQUEST['created_attribute']) {
	$message = 'User Attribute Created.';
}
if ($_REQUEST['attribute_deleted']) {
	$message = 'User Attribute Deleted.';
}

if ($_GET['uID']) {
	$uo = UserInfo::getByID($_GET['uID']);
	if (is_object($uo)) {
		if ($_GET['task'] == 'activate') {
			$uo->activate();
			$uo = UserInfo::getByID($_GET['uID']);
			$message = "User activated.";
		}
		
		
		if ($_GET['task'] == 'remove-avatar') {
			$av = Loader::helper('concrete/avatar');
			$av->removeAvatar($uo->getUserID());
			$this->controller->redirect('/dashboard/users?uID=' . $_GET['uID'] . '&task=edit');

		}
		
		if ($_GET['task'] == 'deactivate') {
			$uo->deactivate();
			$uo = UserInfo::getByID($_GET['uID']);
			$message = "User deactivated.";
		}
		
		
		if ($_POST['edit']) {
			
			if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
				$_POST['uName'] = $_POST['uEmail'];
			}
			
			$username = $txt->sanitize($_POST['uName']);
			$password = $txt->sanitize($_POST['uPassword']);
			$passwordConfirm = $txt->sanitize($_POST['uPasswordConfirm']);
			
			if ($password) {
				if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
					$error[] = 'A password must be between ' . USER_PASSWORD_MINIMUM . ' and ' . USER_PASSWORD_MAXIMUM . ' characters';
				}
			}
			
			if (!$vals->email($_POST['uEmail'])) {
				$error[] = 'Invalid email address provided.';
			} else if (!$valc->isUniqueEmail($_POST['uEmail']) && $uo->getUserEmail() != $_POST['uEmail']) {
				$error[] = "The email address '{$_POST['uEmail']}' is already in use. Please choose another.";
			}
			
			if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
				if (strlen($username) < USER_USERNAME_MINIMUM) {
					$error[] = 'A username must be between at least ' . USER_USERNAME_MINIMUM . ' characters long.';
				}
	
				if (strlen($username) > USER_USERNAME_MAXIMUM) {
					$error[] = 'A username cannot be more than ' . USER_USERNAME_MAXIMUM . ' characters long.';
				}

				if (strlen($username) >= USER_USERNAME_MINIMUM && !$vals->alphanum($username)) {
					$error[] = 'A username may only contain letters or numbers.';
				}
				if (!$valc->isUniqueUsername($username) && $uo->getUserName() != $username) {
					$error[] = "The username '{$username}' already exists. Please choose another";
				}		
			}
			
			if (strlen($password) >= USER_PASSWORD_MINIMUM && !$vals->alphanum($password)) {
				$error[] = 'A password may only contain letters or numbers.';
			}
			
			if ($password) {
				if ($password != $passwordConfirm) {
					$error[] = 'The two passwords provided do not match.';
				}
			}
					
		
			if (!$error) {
				// do the registration
				$process = $uo->update($_POST);
				$pr2 = $uo->updateUserAttributes($_POST);
				
				//$db = Loader::db();
				if ($process) {
	
					$av = Loader::helper('concrete/avatar');
					
					if ( is_uploaded_file($_FILES['uAvatar']['tmp_name']) ) {
						$uHasAvatar = $av->updateUserAvatar($_FILES['uAvatar']['tmp_name'], $uo->getUserID());
					}
					
					$uo->updateSelectedUserAttributes($data['editAKID'], $_POST);
					$uo->updateGroups($_POST['gID']);

					$message = "User updated successfully. ";
					if ($password) {
						$message .= "Password changed.";
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

if ($_POST['create']) {

	if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
		$_POST['uName'] = $_POST['uEmail'];
	}
	

	$username = $txt->sanitize($_POST['uName']);
	$password = $txt->sanitize($_POST['uPassword']);
	
	if (!$vals->email($_POST['uEmail'])) {
		$error[] = 'Invalid email address provided.';
	} else if (!$valc->isUniqueEmail($_POST['uEmail'])) {
		$error[] = "The email address '{$_POST['uEmail']}' is already in use. Please choose another.";
	}
	
	if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
		if (strlen($username) < USER_USERNAME_MINIMUM) {
			$error[] = 'A username must be between at least ' . USER_USERNAME_MINIMUM . ' characters long.';
		}

		if (strlen($username) > USER_USERNAME_MAXIMUM) {
			$error[] = 'A username cannot be more than ' . USER_USERNAME_MAXIMUM . ' characters long.';
		}

		if (strlen($username) >= USER_USERNAME_MINIMUM && !$vals->alphanum($username)) {
			$error[] = 'A username may only contain letters or numbers.';
		}
		if (!$valc->isUniqueUsername($username)) {
			$error[] = "The username '{$username}' already exists. Please choose another";
		}		
	}
	
	if ($username == USER_SUPER) {
		$error[] = 'Invalid Username';
	}

	
	if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
		$error[] = 'A password must be between ' . USER_PASSWORD_MINIMUM . ' and ' . USER_PASSWORD_MAXIMUM . ' characters';
	}
		
	if (strlen($password) >= USER_PASSWORD_MINIMUM && !$vals->alphanum($password)) {
		$error[] = 'A password may only contain letters or numbers.';
	}

	if (!$error) {
		// do the registration
		$uo = UserInfo::add($_POST);
		
		if (is_object($uo)) {

			$av = Loader::helper('concrete/avatar');
			if (is_uploaded_file($_FILES['uAvatar']['tmp_name'])) {
				$uHasAvatar = $av->updateUserAvatar($_FILES['uAvatar']['tmp_name'], $uo->getUserID());
			}
			
			$uo->updateSelectedUserAttributes($data['editAKID'], $_POST);
			$uo->updateGroups($_POST['gID']);
	
			$message = "User created successfully. ";
		} else {
			$error[] = 'An error occurred while trying to create the account.';
		}
		
	}		
}
		
if ((!is_object($uo))) {
	if ($_REQUEST['task'] == 'simple_search') { 
		$sa['uVal'] = $_GET['uVal'];
	} else {
		$sa = $_GET;
		$sa['uDateAddedStart'] = $dtt->translate('uDateAddedStart', $sa);
		$sa['uDateAddedEnd'] = $dtt->translate('uDateAddedEnd', $sa);
		$sa['uLoggedInDateStart'] = $dtt->translate('uLoggedInDateStart', $sa);
		$sa['uLoggedInDateEnd'] = $dtt->translate('uLoggedInDateEnd', $sa);
	}
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
			echo("<td><b>Username</b></td>");
			echo("<td><b>Email Address</b></td>");
			echo("<td><b>Registered</b></td>");
			echo("<td><b># Logins</b></td>");
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
		<span class="required">*</span> - required field
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
		
		
	<h1><span>Edit Account</span></h1>
	
	<div class="ccm-dashboard-inner">

		<form method="post" enctype="multipart/form-data" id="ccm-user-form" action="<?=$this->url('/dashboard/users?uID=' . $_GET['uID'])?>">
		<input type="hidden" name="_disableLogin" value="1">
	
		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td colspan="3" class="header">Core Information</td>
		</tr>
		<tr>
			<td class="subheader">Username <span class="required">*</span></td>
			<td class="subheader">Email Address <span class="required">*</span></td>
			<td class="subheader">User Avatar</td>
		</tr>	
		<tr>
			<td><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?><?=$uo->getUserName()?><? } else { ?><input type="text" name="uName" autocomplete="off" value="<?=$uName?>" style="width: 100%"><? } ?></td>
			<td><input type="text" name="uEmail" autocomplete="off" value="<?=$uEmail?>" style="width: 100%"></td>
			<td><input type="file" name="uAvatar" style="width: 100%" /> <input type="hidden" name="uHasAvatar" value="<?=$uo->hasAvatar()?>" />
			
			<? if ($uo->hasAvatar()) { ?>
			<input type="button" onclick="location.href='<?=$this->url('/dashboard/users?uID=' . $_REQUEST['uID'] . '&task=remove-avatar')?>'" value="Remove Avatar" />
			<? } ?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="header">Change Password</td>
		</tr>
		<tr>
			<td class="subheader">Password</td>
			<td class="subheader" colspan="2">Password (Confirm)</td>
		</tr>	
		<tr>
			<td><input type="password" name="uPassword" autocomplete="off" value="" style="width: 100%"></td>
			<td><input type="password" name="uPasswordConfirm" autocomplete="off" value="" style="width: 100%"></td>
			<td>(Leave these fields blank to keep the same password)</td>
		</tr>
		<tr>
			<td colspan="3" class="header">Other Information (Click the checkbox to modify existing values)</td>
		</tr>
		<?
	
		$attribs = UserAttributeKey::getList();
		foreach($attribs as $ak) { 
			$attrVal=$ak->getUserValue($_REQUEST['uID']);
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
						<div id="attEditWrap<?=$ak->getKeyID()?>" style="display:none"><?=$ak->outputHTML($uo->getUserID())?> <a onclick="editAttrVal(<?=$ak->getKeyID()?>,1)">Cancel</a></div>
						<div id="attUnknownWrap<?=$ak->getKeyID()?>">Unknown <a onclick="editAttrVal(<?=$ak->getKeyID()?>)">Edit</a></div>
					<? } ?>
				</td>
			</tr>	
		<? } ?>
		
		<tr>
			<td colspan="3" class="header">Groups</td>
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
			</td>
		</tr>
		</table>
		</div>
		
		<div class="ccm-buttons">
		<input type="hidden" name="edit" value="1" />
		<a href="<?=$this->url('/dashboard/users?uID=' . $_GET['uID'])?>" class="ccm-button-left cancel"><span>Cancel</span></a>
		<a href="javascript:void(0)" onclick="$('#ccm-user-form').get(0).submit()" class="ccm-button-right accept"><span>Update User</span></a>
		</div>	
		
		<div class="ccm-spacer">&nbsp;</div>
		</form>
	</div>
	
	<? } else { ?>
	<h1><span>View User</span></h1>
	
	<div class="ccm-dashboard-inner">
		<div class="actions" >
			<? if ($uo->isActive()) { ?>
				<a href="<?=$this->url('/dashboard/users?uID=' . $_REQUEST['uID'] . '&task=deactivate')?>">Deactivate User</a>
			<? } else { ?>
				<a href="<?=$this->url('/dashboard/users?uID=' . $_REQUEST['uID'] . '&task=activate')?>">Activate User</a>
			<? } ?>
			&nbsp;|&nbsp;		
			<a href="<?=$this->url('/dashboard/users?uID=' . $_REQUEST['uID'])?>&task=edit">Edit User</a>		
		</div>
		
		<h2>Required Information</h2>
		
		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td><?=$uh->outputUserAvatar($uo)?></td>
			<td><?=$uo->getUserName()?><br/>
			<a href="mailto:<?=$uo->getUserEmail()?>"><?=$uo->getUserEmail()?></a><br/>
			<?=$uo->getUserDateAdded()?></td>
		</tr>
		</table>
		</div>

		
		<?
		$attribs = UserAttributeKey::getList(true);
		if (count($attribs) > 0) { ?>
		<h2>Other Information</h2>

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
		
				<h2>Groups</h2>

		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td colspan="2" class="header">Group</td>
			<td class="header">Date Entered</td>
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
		
	
	<? } ?>

<?

} else { ?>

	<h1><span>Search User Accounts</span></h1>
	
	<div class="ccm-dashboard-inner">

	<div id="ccm-user-search">
	
	<a href="javascript:void(0)" id="ccm-user-search-advanced-control" <? if ($_REQUEST['task'] == 'search') { ?> style="display: none" <? } ?>>Advanced Search &gt;</a>
	
	<div id="ccm-user-search-simple" <? if ($_REQUEST['task'] == 'search') { ?> style="display: none" <? } ?>>
	<br/>
	
	<h3>Username or Email Address Contains:</h3>
	<form method="get" id="ccm-user-search-simple-form" action="<?=$this->url('/dashboard/users')?>">
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td>
	<input type="hidden" name="task" value="simple_search" />
	<input type="text" name="uVal" value="<?=$_REQUEST['uVal']?>" style="width: 200px" />
	</td>
	<td style="padding-left: 10px">
	<a href="javascript:void(0)" onclick="$('#ccm-user-search-simple-form').get(0).submit()" class="ccm-button"><span>Search Users</span></a>
	</td>
	</tr>
	</table>
	</div>
	</form>
	
	</div>
	
	<a href="javascript:void(0)" id="ccm-user-search-simple-control" <? if ($_REQUEST['task'] != 'search') { ?> style="display: none" <? } ?>>&lt; Back to Simple Search</a>

	<div id="ccm-user-search-advanced" <? if ($_REQUEST['task'] == 'search') { ?> style="display: block" <? } ?>>
	
	<form method="get" action="<?=$this->url('/dashboard/users')?>" id="ccm-user-search-advanced-form">
	<input type="hidden" name="task" value="search" />
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader">Username</td>
		<td><input type="text" name="uName" autocomplete="off" value="<?=$_GET['uName']?>" style="width: 100%"></td>
		<td class="subheader">Email Address</td>
		<td><input type="text" name="uEmail" autocomplete="off" value="<?=$_GET['uEmail']?>" style="width: 100%"></td>
	</tr>
	<tr>
		<td valign="top" class="subheader">In Group</td>
		<td valign="top"><select name="gID[]" style="width: 100%; height: 100px" multiple="multiple" >
		<?
			$gID = array();
			if (is_array($_REQUEST['gID'])) {
				$gID = $_REQUEST['gID'];
			}
			$xgID = array();
			if (is_array($_REQUEST['xgID'])) {
				$xgID = $_REQUEST['xgID'];
			}
			$gl = new GroupList(null, true);
			$gArray = $gl->getGroupList();
			foreach ($gArray as $g) { ?>
				<option value="<?=$g->getGroupID()?>"<? if (in_array($g->getGroupID(), $gID)) { ?> selected<? } ?>><?=$g->getGroupName()?></option>
			<? } ?>
		</select>			
		</td>
		<td valign="top" class="subheader">Not In Group</td>
		<td valign="top"><select name="xgID[]" style="width: 100%; height: 100px" multiple="multiple" >
		<?
			$gl = new GroupList(null, true);
			$gArray = $gl->getGroupList();
			foreach ($gArray as $g) { ?>
				<option value="<?=$g->getGroupID()?>"<? if (in_array($g->getGroupID(), $xgID)) { ?> selected<? } ?>><?=$g->getGroupName()?></option>
			<? } ?>
		</select>			
		</td>
	</tr>
	<tr>
		<td class="subheader">Registered between:</td>
		<td><? print $dtt->datetime('uDateAddedStart', $dtt->translate('uDateAddedStart', $_GET), true)?></td>
		<td class="subheader">and: </td>
		<td><? print $dtt->datetime('uDateAddedEnd', $dtt->translate('uDateAddedEnd', $_GET), true)?></td>
	</tr>
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
			<a href="javascript:void(0)" onclick="$('#ccm-user-search-advanced-form').get(0).submit()" class="ccm-button-right"><span>Search Users</span></a>
		</td>
	</tr>
	</table>
	</div>

	</form>	
	
	</div>
	</div>
	
	<? if ($_REQUEST['task'] == 'search' || $_REQUEST['task'] == 'simple_search') { ?>
	
	<h2>Results</h2>
	
		<? if ($s->getTotal() > 0) { ?>
	

	<? 
		$variables['output'] = 'excel';
		$url = Search::qsReplace($variables);
	?>
	<a href="<?=$url?>" style="float: right; line-height: 18px; padding-left: 20px; background: transparent url(<?=ASSETS_URL_IMAGES?>/icons/excel.png) no-repeat">Export to Excel</a>

	<? include(DIR_FILES_ELEMENTS_CORE . '/search_results_top.php'); ?>
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table border="0" cellspacing="1" cellpadding="0" class="grid-list">
	<tr>
		<?=$s->printHeader('User Name','uName',1)?>
		<?=$s->printHeader('Email Address','uEmail',1)?>
		<?=$s->printHeader('Date Added','uDateAdded',1)?>
		<?=$s->printHeader('# Logins', 'uNumLogins',1)?>
	</tr>
	<? if ($s->getTotal() > 0) { 
		while ($row = $res->fetchRow()) { ?>
		<tr>
			<?=$s->printRow($row['uName'], 'uName', $this->url('/dashboard/users?uID=' . $row['uID']))?>
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
		
		<strong>No users found.</strong>
		
	<? } ?>
	
	
	<? } ?>
	</div>
	
	
	<h1><span>Create Account</span></h1>
	
	<div class="ccm-dashboard-inner">
	
	<div class="actions">
	<span class="required">*</span> - required field
	</div>
	
	<form method="post" enctype="multipart/form-data" id="ccm-user-form" action="<?=$this->url('/dashboard/users?task=create')?>">
	<input type="hidden" name="_disableLogin" value="1">

	<h2>Required Information</h2>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="subheader" width="50%"><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) { ?>Username <span class="required">*</span><? } else { ?>Email Address <span class="required">*</span><? } ?></td>
		<td class="subheader" width="50%">Password <span class="required">*</span></td>
	</tr>
	<tr>
		<td><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) { ?><input type="text" name="uName" autocomplete="off" value="<?=$_POST['uName']?>" style="width: 100%"><? } else { ?><input type="text" name="uEmail" autocomplete="off" value="<?=$_POST['uEmail']?>" style="width: 100%"><? } ?></td>
		<td><input type="password" autocomplete="off" name="uPassword" value="" style="width: 100%"></td>
	</tr>
	<tr>
		<td class="subheader"><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>&nbsp;<? } else { ?>Email Address <span class="required">*</span><? } ?></td>
		<td class="subheader">User Avatar</td>
	</tr>	
	<tr>
		<td><? if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) { ?>&nbsp;<? } else { ?><input type="text" name="uEmail" autocomplete="off" value="<?=$_POST['uEmail']?>" style="width: 100%"><? } ?></td>
		<td><input type="file" name="uAvatar" style="width: 100%"/></td>
	</tr>
	</table>
	</div>
	
	<h2>Groups</h2>
	
	<div style="margin:0px; padding:0px; width:100%; height:auto" >	
	<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
	<? 
	$gID = array();
	if (is_array($_POST['gID'])) {
		$gID = $_POST['gID'];
	}
	$gArray = $gl->getGroupList(); ?>
	<tr>
		<td>
		<? foreach ($gArray as $g) { ?>
			<input type="checkbox" name="gID[]" value="<?=$g->getGroupID()?>" style="vertical-align: middle"<? if (in_array($g->getGroupID(), $gID)) { ?> checked <? } ?>> <?=$g->getGroupName()?><br>
		<? } ?>
		</td>
	</tr>
	</table>
	</div>

	<div class="ccm-buttons">
		<input type="hidden" name="create" value="1" />
		<a href="javascript:void(0)" onclick="$('#ccm-user-form').get(0).submit()" class="ccm-button-right accept"><span>Create User</span></a>
	</div>	

	<div class="ccm-spacer">&nbsp;</div>
	
	</div>
	</form>
	
	<a name="attributes"></a>

	<h1><span>User Attributes</span></h1>
	<div class="ccm-dashboard-inner">
	
	
	<? if (count($attribs) > 0) { ?>

	<p>To set the order for these items on the registration form, click and drag the graphic next to the attribute's name.</p>
	
	<div id="user-attributes-list">
	
	<?
	foreach($attribs as $ak) { ?>
	<div class="uat" id="item_<?=$ak->getKeyID()?>" style="font-size: 12px">
	<img src="<?=ASSETS_URL_IMAGES?>/dashboard/uat-<?=$ak->getKeyType()?>.gif" width="21" height="21" class="handle" id="handle<?=$ak->getKeyID()?>" /><a href="<?=$this->url('/dashboard/users/attributes?ukID=' . $ak->getKeyID() . '&task=edit')?>"><?=$ak->getKeyName()?></a> (<?=$ak->getNumEntries()?>)
	</div>
	
	<? } ?>

	</div>
	
	<? } else { ?>
		
	<br/><strong>No user attributes defined.</strong><br/><br/>
		
	<? } ?>

	<a href="<?=$this->url('/dashboard/users/attributes')?>" class="ccm-button-right"><span>Add User Attribute</span></a>
	<div class="ccm-spacer">&nbsp;</div>
	
	</div>


<script type="text/javascript">
$(function() {

	$("#ccm-user-search-advanced-control").click(function() {
		$("#ccm-user-search-simple").hide();
		$("#ccm-user-search-simple-control").show();
		$(this).hide();
		$("#ccm-user-search-advanced").show('slide', {
			direction: 'up'
		});
	});

	$("#ccm-user-search-simple-control").click(function() {
		$("#ccm-user-search-advanced").hide();
		$("#ccm-user-search-advanced-control").show();
		$(this).hide();
		$("#ccm-user-search-simple").show();
	});
	
	$("div#user-attributes-list").sortable({
		handle: 'img.handle',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			$.post('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/user_attributes_update.php', ualist, function(r) {

			});
		}
	});
	

	
});
</script>
<?
}

?>