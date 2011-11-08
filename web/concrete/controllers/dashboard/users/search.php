<?
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('attribute/categories/user');
Loader::model('user_list');
class DashboardUsersSearchController extends Controller {

	public function on_start(){
		$this->error = Loader::helper('validation/error');
	}

	public function view() {
		$html = Loader::helper('html');
		$form = Loader::helper('form');
		$this->set('form', $form);
		$this->addHeaderItem('<script type="text/javascript">$(function() { ccm_setupAdvancedSearch(\'user\'); });</script>');
		$userList = $this->getRequestedSearchResults();
		$users = $userList->getPage();
				
		$this->set('userList', $userList);		
		$this->set('users', $users);		
		$this->set('pagination', $userList->getPagination());	
		
		if($_POST['uName'])	{
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

	}
	
	
	
	public function validate_user(){
			$vals = Loader::helper('validation/strings');
			$valt = Loader::helper('validation/token');
			$valc = Loader::helper('concrete/validation');

			$uo = UserInfo::getByID(intval($_GET['uID']));			
			
			$username = trim($_POST['uName']);
			$username = preg_replace("/\s+/", " ", $username);
			$_POST['uName'] = $username;
			
			$password = $_POST['uPassword'];
			$passwordConfirm = $_POST['uPasswordConfirm'];
			
			if ($password) {
				if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
					$this->error->add( t('A password must be between %s and %s characters',USER_PASSWORD_MINIMUM,USER_PASSWORD_MAXIMUM));
				}
			}
			
			if (!$vals->email($_POST['uEmail'])) {
				$this->error->add(t('Invalid email address provided.'));
			} else if (!$valc->isUniqueEmail($_POST['uEmail']) && $uo->getUserEmail() != $_POST['uEmail']) {
				$this->error->add(t("The email address '%s' is already in use. Please choose another.",$_POST['uEmail']));
			}
			
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
				if (!$valc->isUniqueUsername($username) && $uo->getUserName() != $username) {
					$this->error->add(t("The username '%s' already exists. Please choose another",$username));
				}		
			}
			
			if (strlen($password) >= USER_PASSWORD_MINIMUM && !$valc->password($password)) {
				$this->error->add(t('A password may not contain ", \', >, <, or any spaces.'));
			}
			
			if ($password) {
				if ($password != $passwordConfirm) {
					$this->error->add(t('The two passwords provided do not match.'));
				}
			}
			
			if (!$valt->validate('update_account_' . intval($_GET['uID']) )) {
				$this->error->add($valt->getErrorMessage());
			}
		
			if (!$this->error->has()) {
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
		
		if ($_REQUEST['numResults']) {
			$userList->setItemsPerPage($_REQUEST['numResults']);
		}
		
		if (isset($_REQUEST['gID']) && is_array($_REQUEST['gID'])) {
			foreach($_REQUEST['gID'] as $gID) {
				$userList->filterByGroupID($gID);
			}
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
		
		$akID = $_REQUEST['uakID'];
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

			$delUI=UserInfo::getByID($delUserId); 
			
			if(!($delUI instanceof UserInfo)) {
				throw new Exception(t('Invalid user ID.'));
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

?>