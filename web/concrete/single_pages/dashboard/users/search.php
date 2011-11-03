<?

$attribs = UserAttributeKey::getList(true);
$u = new User();
$uh = Loader::helper('concrete/user');
$txt = Loader::helper('text');
$vals = Loader::helper('validation/strings');
$valt = Loader::helper('validation/token');
$valc = Loader::helper('concrete/validation');
$dtt = Loader::helper('form/date_time');
$dh = Loader::helper('date');
$form = Loader::helper('form');
$ih = Loader::helper('concrete/interface');
$av = Loader::helper('concrete/avatar'); 

if ($_REQUEST['user_created'] == 1) {
	$message = t('User created successfully. ');
}

function printAttributeRow($ak, $uo) {
	
	$vo = $uo->getAttributeValueObject($ak);
	$value = '';
	if (is_object($vo)) {
		$value = $vo->getValue('displaySanitized', 'display');
	}
	
	if ($value == '') {
		$text = '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
	} else {
		$text = $value;
	}
	if ($ak->isAttributeKeyEditable()) { 
	$type = $ak->getAttributeType();
	
	$html = '
	<tr class="ccm-attribute-editable-field">
		<td style="white-space: nowrap; padding-right: 20px"><strong><a href="javascript:void(0)">' . $ak->getAttributeKeyDisplayHandle() . '</a></strong></td>
		<td width="100%" class="ccm-attribute-editable-field-central"><div class="ccm-attribute-editable-field-text">' . $text . '</div>
		<form method="post" action="' . View::url('/dashboard/users/search', 'edit_attribute') . '">
		<input type="hidden" name="uakID" value="' . $ak->getAttributeKeyID() . '" />
		<input type="hidden" name="uID" value="' . $uo->getUserID() . '" />
		<input type="hidden" name="task" value="update_extended_attribute" />
		<div class="ccm-attribute-editable-field-form ccm-attribute-editable-field-type-' . strtolower($type->getAttributeTypeHandle()) . '">
		' . $ak->render('form', $vo, true) . '
		</div>
		</form>
		</td>
		<td class="ccm-attribute-editable-field-save"><a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/edit_small.png" width="16" height="16" class="ccm-attribute-editable-field-save-button" /></a>
		<a href="javascript:void(0)"><img src="' . ASSETS_URL_IMAGES . '/icons/close.png" width="16" height="16" class="ccm-attribute-editable-field-clear-button" /></a>
		<img src="' . ASSETS_URL_IMAGES . '/throbber_white_16.gif" width="16" height="16" class="ccm-attribute-editable-field-loading" />
		</td>
	</tr>';
	
	} else {

	$html = '
	<tr>
		<th>' . $ak->getAttributeKeyDisplayHandle() . '</th>
		<td width="100%" colspan="2">' . $text . '</td>
	</tr>';	
	}
	print $html;
}


if (intval($_GET['uID'])) {
	
	$uo = UserInfo::getByID(intval($_GET['uID']));
	if (is_object($uo)) {
		$uID = intval($_REQUEST['uID']);
		
		if (isset($_GET['task'])) {
			if ($uo->getUserID() == USER_SUPER_ID && (!$u->isSuperUser())) {
				throw new Exception(t('Only the super user may edit this account.'));
			}
		}
		
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
			
			$username = trim($_POST['uName']);
			$username = preg_replace("/\s+/", " ", $username);
			$_POST['uName'] = $username;
			
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
				$process = $uo->update($_POST);
				
				//$db = Loader::db();
				if ($process) {
					if ( is_uploaded_file($_FILES['uAvatar']['tmp_name']) ) {
						$uHasAvatar = $av->updateUserAvatar($_FILES['uAvatar']['tmp_name'], $uo->getUserID());
					}
					
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
		
		
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit User'), t('Edit User account.'), false, false);?>
	
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
		<?
		$languages = Localization::getAvailableInterfaceLanguages();
		if (count($languages) > 0) { ?>
	
		<tr>
			<td class="subheader" colspan="3"><?=t('Default Language')?></td>
		</tr>	
		<tr>
			<Td colspan="3">
			<?
				array_unshift($languages, 'en_US');
				$locales = array();
				Loader::library('3rdparty/Zend/Locale');
				Loader::library('3rdparty/Zend/Locale/Data');
				$locales[''] = t('** Default');
				Zend_Locale_Data::setCache(Cache::getLibrary());
				foreach($languages as $lang) {
					$loc = new Zend_Locale($lang);
					$locales[$lang] = Zend_Locale::getTranslation($loc->getLanguage(), 'language', ACTIVE_LOCALE);
				}
				$ux = $uo->getUserObject();
				print $form->select('uDefaultLanguage', $locales, $ux->getUserDefaultLanguage());
			?>
			</td>
		</tr>	
		<? } ?>

		<? if(ENABLE_USER_TIMEZONES) { ?>
        <tr>
        	<td class="subheader" colspan="3"><?=t('Time Zone')?></td>
        </tr>
        <tr>
			<td colspan="3">
            	<?php 
				echo $form->select('uTimezone', 
						$dh->getTimezones(), 
						($uo->getUserTimezone()?$uo->getUserTimezone():date_default_timezone_get())
					); ?>
            </td>
		</tr>
        <?php } ?>
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
        
        <input type="hidden" name="edit" value="1" />

		<div class="ccm-buttons">
		
		<?=Loader::helper('concrete/interface')->button(t('Back'), $this->url('/dashboard/users/search?uID=' . intval($_GET['uID'])), 'left')?>
		<?=Loader::helper('concrete/interface')->submit(t('Update User'))?>

		</div>	
		</form>

		<div class="ccm-spacer">&nbsp;</div>
		
		<br/>
		
		<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td colspan="3" class="header"><?=t('Other Information - Click Field Name to Edit')?></td>
		</tr>
		<?
	
		$attribs = UserAttributeKey::getEditableList();
		foreach($attribs as $ak) { 
			printAttributeRow($ak, $uo);
		} ?>
		</table>
		

		</div>
		
		<div class="ccm-spacer">&nbsp;</div>
		
	</div>
	
	<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('View User'), t('View User accounts.'), false, false);?>
	<div class="ccm-dashboard-inner">
		<div class="actions" >			
		
			<? if ($uo->getUserID() != USER_SUPER_ID || $u->isSuperUser()) { ?>
	
				<? print $ih->button(t('Edit User'), $this->url('/dashboard/users/search?uID=' . intval($uID) ) . '&task=edit', 'left');?>
	
				<? if (USER_VALIDATE_EMAIL == true) { ?>
					<? if ($uo->isValidated() < 1) { ?>
					<? print $ih->button(t('Mark Email as Valid'), $this->url('/dashboard/users/search?uID=' . intval($uID) . '&task=validate_email'), 'left');?>
					<? } ?>
				<? } ?>
				
				<? if ($uo->getUserID() != USER_SUPER_ID) { ?>
					<? if ($uo->isActive()) { ?>
						<? print $ih->button(t('Deactivate User'), $this->url('/dashboard/users/search?uID=' . intval($uID) . '&task=deactivate&ccm_token='.$valt->generate('user_deactivate')), 'left');?>
					<? } else { ?>
						<? print $ih->button(t('Activate User'), $this->url('/dashboard/users/search?uID=' . intval($uID) . '&task=activate&ccm_token='.$valt->generate('user_activate')), 'left');?>
					<? } ?>
				<? } ?>
			
			<? } ?>
			
			<?
			$tp = new TaskPermission();
			if ($uo->getUserID() != $u->getUserID()) {
				if ($tp->canSudo()) { 
				
					$loginAsUserConfirm = t('This will end your current session and sign you in as %s', $uo->getUserName());
					
					print $ih->button_js(t('Sign In as User'), 'loginAsUser()', 'left');?>
	
					<script type="text/javascript">
					loginAsUser = function() {
						if (confirm('<?=$loginAsUserConfirm?>')) { 
							location.href = "<?=$this->url('/dashboard/users/search', 'sign_in_as_user', $uo->getUserID(), $valt->generate('sudo'))?>";				
						}
					}
					</script>
	
				<? } /*else { ?>
					<? print $ih->button_js(t('Sign In as User'), 'alert(\'' . t('You do not have permission to sign in as other users.') . '\')', 'left', 'ccm-button-inactive');?>
				<? }*/ ?>
			<? } ?>

		</div>
		
		<h2><?=t('Required Information')?></h2>
		
		<div style="margin:0px; padding:0px; width:100%; height:auto" >
		<table border="0" cellspacing="1" cellpadding="0">
		<tr>
			<td><?=$av->outputUserAvatar($uo)?></td>
			<td><?=$uo->getUserName()?><br/>
			<a href="mailto:<?=$uo->getUserEmail()?>"><?=$uo->getUserEmail()?></a><br/>
			<?=$uo->getUserDateAdded('user')?>
			<?=(ENABLE_USER_TIMEZONES && strlen($uo->getUserTimezone())?"<br />".t('Timezone').": ".$uo->getUserTimezone():"")?>
            
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
			<td class="subheader" style="width: 33%"><?=$uk->getAttributeKeyDisplayHandle()?></td>
			<? if (is_object($uk2)) { ?><td  style="width: 33%" class="subheader"><?=$uk2->getAttributeKeyDisplayHandle()?></td><? } else { ?><td  style="width: 33%" class="subheader">&nbsp;</td><? } ?>
			<? if (is_object($uk3)) { ?><td  style="width: 33%"class="subheader"><?=$uk3->getAttributeKeyDisplayHandle()?></td><? } else { ?><td style="width: 33%" class="subheader">&nbsp;</td><? } ?>
		</tr>
		<tr>
			<td><?=$uo->getAttribute($uk->getAttributeKeyHandle(), 'displaySanitized', 'display')?></td>
			<? if (is_object($uk2)) { ?><td><?=$uo->getAttribute($uk2->getAttributeKeyHandle(), 'displaySanitized', 'display')?></td><? } else { ?><td style="width: 33%">&nbsp;</td><? } ?>
			<? if (is_object($uk3)) { ?><td><?=$uo->getAttribute($uk3->getAttributeKeyHandle(), 'displaySanitized', 'display')?></td><? } else { ?><td>&nbsp;</td><? } ?>
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
		<div class="ccm-spacer"></div>
		<?
		$cu = new User();
		$tp = new TaskPermission();
		if ($tp->canDeleteUser()) {
		$delConfirmJS = t('Are you sure you want to permanently remove this user?');
			if ($uo->getUserID() == USER_SUPER_ID) { ?>
				<?=t('You may not remove the super user account.')?>
			<? } else if (!$tp->canDeleteUser()) { ?>
				<?=t('You do not have permission to perform this action.');		
			} else if ($uo->getUserID() == $cu->getUserID()) {
				echo t('You cannot delete your own user account.');
			}else{ ?>   
				
				<script type="text/javascript">
				deleteUser = function() {
					if (confirm('<?=$delConfirmJS?>')) { 
						location.href = "<?=$this->url('/dashboard/users/search', 'delete', $uo->getUserID(), $valt->generate('delete_account'))?>";				
					}
				}
				</script>
	
				<? print $ih->button_js(t('Delete User Account'), "deleteUser()", 'left');?>
	
			<? }
		} else {
			echo t('You do not have permission to perform this action.');
		}?>
		<div class="ccm-spacer"></div>
	</div>
	<? } ?>


<script type="text/javascript">


ccm_activateEditableProperties = function() {
	$("tr.ccm-attribute-editable-field").each(function() {
		var trow = $(this);
		$(this).find('a').click(function() {
			trow.find('.ccm-attribute-editable-field-text').hide();
			trow.find('.ccm-attribute-editable-field-clear-button').hide();
			trow.find('.ccm-attribute-editable-field-form').show();
			trow.find('.ccm-attribute-editable-field-save-button').show();
		});
		
		trow.find('form').submit(function() {
			ccm_submitEditableProperty(trow);
			return false;
		});
		
		trow.find('.ccm-attribute-editable-field-save-button').parent().click(function() {
			ccm_submitEditableProperty(trow);
		});

		trow.find('.ccm-attribute-editable-field-clear-button').parent().unbind();
		trow.find('.ccm-attribute-editable-field-clear-button').parent().click(function() {
			trow.find('form input[name=task]').val('clear_extended_attribute');
			ccm_submitEditableProperty(trow);
			return false;
		});

	});
}

ccm_submitEditableProperty = function(trow) {
	trow.find('.ccm-attribute-editable-field-save-button').hide();
	trow.find('.ccm-attribute-editable-field-clear-button').hide();
	trow.find('.ccm-attribute-editable-field-loading').show();
	try {
		tinyMCE.triggerSave(true, true);
	} catch(e) { }
	
	trow.find('form').ajaxSubmit(function(resp) {
		// resp is new HTML to display in the div
		trow.find('.ccm-attribute-editable-field-loading').hide();
		trow.find('.ccm-attribute-editable-field-save-button').show();
		trow.find('.ccm-attribute-editable-field-text').html(resp);
		trow.find('.ccm-attribute-editable-field-form').hide();
		trow.find('.ccm-attribute-editable-field-save-button').hide();
		trow.find('.ccm-attribute-editable-field-text').show();
		trow.find('.ccm-attribute-editable-field-clear-button').show();
		trow.find('td').show('highlight', {
			color: '#FFF9BB'
		});

	});
}

$(function() {
	ccm_activateEditableProperties();
	$("#groupSelector").dialog();
	ccm_triggerSelectGroup = function(gID, gName) {
		var html = '<input type="checkbox" name="gID[]" value="' + gID + '" style="vertical-align: middle" checked /> ' + gName + '<br/>';
		$("#ccm-additional-groups").append(html);
	}

});
</script>


<?

} else { ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Search Users'), t('Search the users of your site and perform bulk actions on them.'), false, false);?>

<?
$tp = new TaskPermission();
if ($tp->canAccessUserSearch()) { ?>

<div class="ccm-pane-options" id="ccm-<?=$searchInstance?>-pane-options">
<? Loader::element('users/search_form_advanced', array('columns' => $columns, 'searchInstance' => $searchInstance, 'searchRequest' => $searchRequest, 'searchType' => 'DASHBOARD')); ?>
</div>

<? Loader::element('users/search_results', array('columns' => $columns, 'searchInstance' => $searchInstance, 'searchType' => 'DASHBOARD', 'users' => $users, 'userList' => $userList, 'pagination' => $pagination)); ?>

<? } else { ?>
<div class="ccm-pane-body">
	<p><?=t('You do not have access to user search. This setting may be changed in the access section of the dashboard settings page.')?></p>
</div>	

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>

<? } ?>