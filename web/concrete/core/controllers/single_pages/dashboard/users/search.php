<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Users_Search extends Controller {

	public function on_start(){
		$this->error = Loader::helper('validation/error');
	}

	public function view() {

		if (isset($_GET['uID'])) {
			$uo = UserInfo::getByID(intval($_GET['uID']));
			if (is_object($uo)) {
				$up = new Permissions($uo);
				if (!$up->canViewUser()) {
					$this->redirect('/dashboard/users/search');
				}
			}
		}
			// this is hacky as hell, we need to make this page MVC
		if (!is_object($uo)) {
			$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_setupAdvancedSearch(\'user\'); });</script>');
			$userList = $this->getRequestedSearchResults();
			$users = $userList->getPage();
					
			$this->set('userList', $userList);		
			$this->set('users', $users);		
			$this->set('pagination', $userList->getPagination());	
		}

		$form = Loader::helper('form');
		$this->set('form', $form);

		if($_POST['edit'])	{
			$this->validate_user();
		}
		
		if ($_REQUEST['deactivated']) {
			$this->set('message', t('User deactivated.'));
		}
		if ($_REQUEST['activated']) {
			$this->set('message', t('User activated.'));
		}
		if ($_REQUEST['validated']) {
			$this->set('message', t('Email marked as valid.'));
		}
		if ($_REQUEST['user_created']) {
			$this->set('message', t('User created.'));
		}

		if ($_REQUEST['user_updated']) {
			$this->set('message', t('User updated successfully.'));
		}

		if ($_REQUEST['user_updated_with_password']) {
			$this->set('message', t('User updated successfully. Password changed.'));
		}

	}
	
	
	
	public function validate_user() {
		$pke = PermissionKey::getByHandle('edit_user_properties');
		if (!$pke->validate()) {
			return false;
		}
		
		$assignment = $pke->getMyAssignment();
		
		
		$vals = Loader::helper('validation/strings');
		$valt = Loader::helper('validation/token');
		$valc = Loader::helper('concrete/validation');

		$uo = UserInfo::getByID(intval($_GET['uID']));			
		
		$username = trim($_POST['uName']);
		$username = preg_replace("/\s+/", " ", $username);
		
		if ($assignment->allowEditPassword()) { 

			$password = $_POST['uPassword'];
			$passwordConfirm = $_POST['uPasswordConfirm'];

			if ($password) {
				if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
					$this->error->add( t('A password must be between %s and %s characters',USER_PASSWORD_MINIMUM,USER_PASSWORD_MAXIMUM));
				}
			}
		}		
		
		if ($assignment->allowEditEmail()) { 
			if (!$vals->email($_POST['uEmail'])) {
				$this->error->add(t('Invalid email address provided.'));
			} else if (!$valc->isUniqueEmail($_POST['uEmail']) && $uo->getUserEmail() != $_POST['uEmail']) {
				$this->error->add(t("The email address '%s' is already in use. Please choose another.",$_POST['uEmail']));
			}
		}

		if ($assignment->allowEditUserName()) { 
			$_POST['uName'] = $username;		
			if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
				if (strlen($username) < USER_USERNAME_MINIMUM) {
					$this->error->add(t('A username must be at least %s characters long.',USER_USERNAME_MINIMUM));
				}
	
				if (strlen($username) > USER_USERNAME_MAXIMUM) {
					$this->error->add(t('A username cannot be more than %s characters long.',USER_USERNAME_MAXIMUM));
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
						$this->error->add(t('A username may only contain letters, numbers and spaces.'));
					} else {
						$this->error->add(t('A username may only contain letters or numbers.'));
					}
				}
				if (strcasecmp($uo->getUserName(), $username) && !$valc->isUniqueUsername($username)) {
					$this->error->add(t("The username '%s' already exists. Please choose another",$username));
				}		
			}
		}

		if ($assignment->allowEditPassword()) { 
			if (strlen($password) >= USER_PASSWORD_MINIMUM && !$valc->password($password)) {
				$this->error->add(t('A password may not contain ", \', >, <, or any spaces.'));
			}
			
			if ($password) {
				if ($password != $passwordConfirm) {
					$this->error->add(t('The two passwords provided do not match.'));
				}
			}
		}
		
		if (!$valt->validate('update_account_' . intval($_GET['uID']) )) {
			$this->error->add($valt->getErrorMessage());
		}
	
		if (!$this->error->has()) {
			// do the registration
			$data = array();
			if ($assignment->allowEditUserName()) { 
				$data['uName'] = $_POST['uName'];
			}
			if ($assignment->allowEditEmail()) { 
				$data['uEmail'] = $_POST['uEmail'];
			}
			if ($assignment->allowEditPassword()) { 
				$data['uPassword'] = $_POST['uPassword'];
				$data['uPasswordConfirm'] = $_POST['uPasswordConfirm'];
			}
			if ($assignment->allowEditTimezone()) { 
				$data['uTimezone'] = $_POST['uTimezone'];
			}
			if ($assignment->allowEditDefaultLanguage()) { 
				$data['uDefaultLanguage'] = $_POST['uDefaultLanguage'];
			}
			$process = $uo->update($data);
			
			//$db = Loader::db();
			if ($process) {
				if ($assignment->allowEditAvatar()) {
					$av = Loader::helper('concrete/avatar'); 
					if ( is_uploaded_file($_FILES['uAvatar']['tmp_name']) ) {
						$uHasAvatar = $av->updateUserAvatar($_FILES['uAvatar']['tmp_name'], $uo->getUserID());
					}
				}

				$uu = User::getByUserID($uo->getUserID());
				$groups = $uu->getUserGroups();
				
				$currentGroupIDs = array();
				$submittedGroupIDs = array();
				$updateGroupIDs = array();

				foreach($groups as $gID => $gName) {
					if ($gID > REGISTERED_GROUP_ID) {
						$currentGroupIDs[] = $gID;
					}
				}

				if (is_array($_POST['gID'])) {
					foreach($_POST['gID'] as $gID) {
						$submittedGroupIDs[] = $gID;
					}
				}

				$updateGroupIDs = array();
				// now we have a merged array of all groups that the user is currently in
				// as well as the ones that may have been checked on on the form.
				foreach(array_unique(array_merge($currentGroupIDs, $submittedGroupIDs)) as $gID) {
					$gx = Group::getByID($gID);
					$gxp = new Permissions($gx);
					if ($gxp->canAssignGroup()) {
						if (in_array($gID, $submittedGroupIDs)) {
							$updateGroupIDs[] = $gID;
						}
					} else if ($uu->inGroup($gx)) {
						$updateGroupIDs[] = $gID;
					}
				}
				$uo->updateGroups($updateGroupIDs);

				$success = 'user_updated=1';
				if ($password) {
					$success = 'user_updated_with_password=1';
				}

				$editComplete = true;
				// reload user object
				$this->redirect('/dashboard/users/search?uID=' . $uu->getUserID() . '&' . $success);
				$uo = UserInfo::getByID(intval($_GET['uID']));
				$this->set('message', $message);
			} else {
				$db = Loader::db();
				$this->error->add($db->ErrorMsg());
				$this->set('error',$this->error);
			}
		}else{
			$this->set('error',$this->error);
		}		
	}
	
	public function getRequestedSearchResults() {
		$userList = new UserList();
		$userList->sortBy('uDateAdded', 'desc');
		$userList->showInactiveUsers = true;
		$userList->showInvalidatedUsers = true;
		
		$columns = UserSearchColumnSet::getCurrent();
		$this->set('columns', $columns);

		if ($_GET['keywords'] != '') {
			$userList->filterByKeywords($_GET['keywords']);
		}	
		
		if ($_REQUEST['numResults'] && Loader::helper('validation/numbers')->integer($_REQUEST['numResults'])) {
			$userList->setItemsPerPage($_REQUEST['numResults']);
		}
		
		$u = new User();
		if (!$u->isSuperUser()) {
			$gIDs = array(-1);
			$gs = new GroupSearch();
			$groups = $gs->get();
			foreach($groups as $gRow) {
				$g = Group::getByID($gRow['gID']);
				if (is_object($g)) {
					$gp = new Permissions($g);
					if ($gp->canSearchUsersInGroup()) {
						$gIDs[] = $g->getGroupID();
					}
				}
			}
			$userList->addToQuery("left join UserGroups ugRequired on ugRequired.uID = u.uID ");	
			$userList->filter(false, '(ugRequired.gID in (' . implode(',', $gIDs) . ') or ugRequired.gID is null)');
		}
		
		$filterGIDs = array();
		if (isset($_REQUEST['gID']) && is_array($_REQUEST['gID'])) {
			foreach($_REQUEST['gID'] as $gID) {
				$g = Group::getByID($gID);
				if (is_object($g)) {
					$gp = new Permissions($g);
					if ($gp->canSearchUsersInGroup()) {
						$filterGIDs[] = $g->getGroupID();
					}
				}
			}
		}
		
		foreach($filterGIDs as $gID) {
			$userList->filterByGroupID($gID);
		}

		if (is_array($_REQUEST['selectedSearchField'])) {
			foreach($_REQUEST['selectedSearchField'] as $i => $item) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					switch($item) {
						case 'is_active':
							if ($_GET['active'] === '0') {
								$userList->filterByIsActive(0);
							} else if ($_GET['active'] === '1') {
								$userList->filterByIsActive(1);
							}
							break;
						case "date_added":
							$dateFrom = $_REQUEST['date_from'];
							$dateTo = $_REQUEST['date_to'];
							if ($dateFrom != '') {
								$dateFrom = date('Y-m-d', strtotime($dateFrom));
								$userList->filterByDateAdded($dateFrom, '>=');
								$dateFrom .= ' 00:00:00';
							}
							if ($dateTo != '') {
								$dateTo = date('Y-m-d', strtotime($dateTo));
								$dateTo .= ' 23:59:59';
								
								$userList->filterByDateAdded($dateTo, '<=');
							}
							break;
						case "group_set":
							$gsID = $_REQUEST['gsID'];
							$gs = GroupSet::getByID($gsID);
							$groupsetids = array(-1);
							if (is_object($gs)) {
								$groups = $gs->getGroups();
							}
							$userList->addToQuery('left join UserGroups ugs on u.uID = ugs.uID');
							foreach($groups as $g) {
								if ($pk->validate($g) && (!in_array($g->getGroupID(), $groupsetids))) {
									$groupsetids[] = $g->getGroupID();
								}								
							}							
							$instr = 'ugs.gID in (' . implode(',', $groupsetids) . ')';
							$userList->filter(false, $instr);
							break;

						default:
							$akID = $item;
							$fak = UserAttributeKey::get($akID);
							$type = $fak->getAttributeType();
							$cnt = $type->getController();
							$cnt->setAttributeKey($fak);
							$cnt->searchForm($userList);
							break;
					}
				}
			}
		}
		return $userList;
	}
	
		public function sign_in_as_user($uID, $token = null) {
		try {
			$u = new User();
			
			$tp = new TaskPermission();
			if (!$tp->canSudo()) { 
				throw new Exception(t('You do not have permission to perform this action.'));
			}
			
			if($uID == USER_SUPER_ID) {
				throw new Exception(t('You can\'t sign in as the %s user.', USER_SUPER));
			}
			
			$ui = UserInfo::getByID($uID); 
			if(!($ui instanceof UserInfo)) {
				throw new Exception(t('Invalid user ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('sudo', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			User::loginByUserID($uID);
			$this->redirect('/');

		} catch(Exception $e) {
			$this->set('error', $e);
			$this->view();
		}
	}
	
	public function edit_attribute() {
		$uo = UserInfo::getByID($_POST['uID']);
		$u = new User();
		if ($uo->getUserID() == USER_SUPER_ID && (!$u->isSuperUser())) {
			throw new Exception(t('Only the super user may edit this account.'));
		}
		
		$assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();
		$akID = $_REQUEST['uakID'];
		if (!in_array($akID, $assignment->getAttributesAllowedArray())) {
			throw new Exception(t('You do not have permission to modify this attribute.'));
		}
		
		$ak = UserAttributeKey::get($akID);

		if ($_POST['task'] == 'update_extended_attribute') { 
			$ak->saveAttributeForm($uo);
			$val = $uo->getAttributeValueObject($ak);
			print $val->getValue('displaySanitized','display');
			exit;
		}
		
		if ($_POST['task'] == 'clear_extended_attribute') {
			$uo->clearAttribute($ak);			
			$val = $uo->getAttributeValueObject($ak);
			print '<div class="ccm-attribute-field-none">' . t('None') . '</div>';
			exit;
		}
	}
	
	public function delete($delUserId, $token = null){
		$u=new User();
		try {

			$delUI=UserInfo::getByID($delUserId); 
			
			if(!($delUI instanceof UserInfo)) {
				throw new Exception(t('Invalid user ID.'));
			}

			$dp = new Permissions($delUI);
			if (!$dp->canViewUser()) {
				throw new Exception(t('Access Denied.'));
			}
		
			$tp = new TaskPermission();
			if (!$tp->canDeleteUser()) { 
				throw new Exception(t('You do not have permission to perform this action.'));
			}

			if ($delUserId == USER_SUPER_ID) {
				throw new Exception(t('You may not remove the super user account.'));
			}			

			if($delUserId==$u->getUserID()) {
				throw new Exception(t('You cannot delete your own user account.'));
			}


			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_account', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$delUI->delete(); 
			$resultMsg=t('User deleted successfully.');
			
			$_REQUEST=array();
			$_GET=array();
			$_POST=array();		
			$this->set('message', $resultMsg);
		} catch (Exception $e) {
			$this->set('error', $e);
		}
		$this->view();

	}

}