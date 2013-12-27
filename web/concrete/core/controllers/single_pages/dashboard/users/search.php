<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_Users_Search extends DashboardController {

	protected $user = false;

	public function on_start(){
		$this->error = Loader::helper('validation/error');
	}

	public function update_avatar($uID = false) {
		$this->setupUser($uID);
		if (!Loader::helper('validation/token')->validate()) {
			throw new Exception(Loader::helper('validation/token')->getErrorMessage());
		}
		if ($this->canEditAvatar) {
			$av = Loader::helper('concrete/avatar'); 
			if (is_uploaded_file($_FILES['avatar']['tmp_name']) ) {
				$av->updateUserAvatar($_FILES['avatar']['tmp_name'], $this->user->getUserID());
			} else {
				if ($_POST['task'] == 'clear') {
					$this->user->update(array('uHasAvatar' => false));
				}
			}
		} else {
			throw new Exception(t('Access Denied.'));
		}

		$ui = UserInfo::getByID($uID); // avatar doesn't reload automatically
		$sr = new UserEditResponse();
		$sr->setUser($this->user);
		$sr->setMessage(t('Avatar saved successfully.'));
		$html = $av->outputUserAvatar($ui);
		$sr->setAdditionalDataAttribute('imageHTML', $html);
		$sr->outputJSON();
	}

	protected function setupUser($uID) {
		$ui = UserInfo::getByID(Loader::helper('security')->sanitizeInt($uID));
		if (is_object($ui)) {
			$up = new Permissions($ui);
			if (!$up->canViewUser()) {
				throw new Exception(t('Access Denied.'));
			}
			$pke = PermissionKey::getByHandle('edit_user_properties');
			$this->user = $ui;
			$this->assignment = $pke->getMyAssignment();
			$this->canEdit = $up->canEditUser();
			if ($this->canEdit) {
				$this->canEditAvatar = $this->assignment->allowEditAvatar();
				$this->canEditUserName = $this->assignment->allowEditUserName();
				$this->canEditEmail = $this->assignment->allowEditEmail();
				$this->canEditPassword = $this->assignment->allowEditPassword();
			}
			$this->set('user', $ui);
			$this->set('canEditAvatar', $this->canEditAvatar);
			$this->set('canEditUserName', $this->canEditUserName);
			$this->set('canEditEmail', $this->canEditEmail);
			$this->set('canEditPassword', $this->canEditPassword);
		}
	}

	public function update_email($uID = false) {
		$this->setupUser($uID);
		if ($this->canEditEmail) {
			$email = $this->post('value');
			if (!Loader::helper('validation/token')->validate()) {
				$this->error->add(Loader::helper('validation/token')->getErrorMessage());
			}
			if (!Loader::helper('validation/strings')->email($email)) {
				$this->error->add(t('Invalid email address provided.'));
			} else if (!Loader::helper('concrete/validation')->isUniqueEmail($email) && $this->user->getUserEmail() != $email) {
				$this->error->add(t("The email address '%s' is already in use. Please choose another.",$email));
			}

			$sr = new UserEditResponse();
			$sr->setUser($this->user);
			if (!$this->error->has()) {
				$data = array('uEmail' => $email);
				$this->user->update($data);
				$sr->setMessage(t('Email saved successfully.'));
			} else {
				$sr->setError($this->error);
			}
			$sr->outputJSON();
		}
	}

	public function update_username($uID = false) {
		$this->setupUser($uID);
		if ($this->canEditUserName) {
			$username = $this->post('value');
			if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == false) {
				if (!Loader::helper('validation/token')->validate()) {
					$this->error->add(Loader::helper('validation/token')->getErrorMessage());
				}
				if (strlen($username) < USER_USERNAME_MINIMUM) {
					$this->error->add(t('A username must be at least %s characters long.',USER_USERNAME_MINIMUM));
				}
	
				if (strlen($username) > USER_USERNAME_MAXIMUM) {
					$this->error->add(t('A username cannot be more than %s characters long.',USER_USERNAME_MAXIMUM));
				}
	
				
				if (strlen($username) >= USER_USERNAME_MINIMUM && !Loader::helper('concrete/validation')->username($username)) {
					if(USER_USERNAME_ALLOW_SPACES) {
						$this->error->add(t('A username may only contain letters, numbers and spaces.'));
					} else {
						$this->error->add(t('A username may only contain letters or numbers.'));
					}
				}
				if (!Loader::Helper('concrete/validation')->isUniqueUsername($username) && $this->user->getUserName() != $username) {
					$this->error->add(t("The username '%s' already exists. Please choose another",$username));
				}

				$sr = new UserEditResponse();
				$sr->setUser($this->user);
				if (!$this->error->has()) {
					$data = array('uName' => $username);
					$this->user->update($data);
					$sr->setMessage(t('Username saved successfully.'));
				} else {
					$sr->setError($this->error);
				}
				$sr->outputJSON();
			}
		}
	}

	public function change_password($uID = false) {
		$this->setupUser($uID);
		if ($this->canEditPassword) {
			$password = $this->post('uPassword');
			$passwordConfirm = $this->post('uPasswordConfirm');
			if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
				$this->error->add( t('A password must be between %s and %s characters',USER_PASSWORD_MINIMUM,USER_PASSWORD_MAXIMUM));
			}
			if (!Loader::helper('validation/token')->validate('change_password')) {
				$this->error->add(Loader::helper('validation/token')->getErrorMessage());
			}
			if (strlen($password) >= USER_PASSWORD_MINIMUM && !Loader::helper('concrete/validation')->password($password)) {
				$this->error->add(t('A password may not contain ", \', >, <, or any spaces.'));
			}
			
			if ($password != $passwordConfirm) {
				$this->error->add(t('The two passwords provided do not match.'));
			}
			
			$sr = new UserEditResponse();
			$sr->setUser($this->user);
			if (!$this->error->has()) {
				$data['uPassword'] = $password;
				$data['uPasswordConfirm'] = $passwordConfirm;
				$this->user->update($data);
				$sr->setMessage(t('Password updated successfully.'));
			} else {
				$sr->setError($this->error);
			}
			$sr->outputJSON();
		
		}

	}
	public function view($uID = false) {
		if ($uID) {
			$this->setupUser($uID);
			if ($this->canEdit) {
				$r = ResponseAssetGroup::get();
				$r->requireAsset('core/app/editable-fields');
			}
			$uo = $this->user->getUserObject();
			$this->set('groups', $uo->getUserGroupObjects());
		}
		$ui = $this->user;
		if (!is_object($ui)) {
			$cnt = new SearchUsersController();
			$cnt->search();
			$this->set('searchController', $cnt);
			$object = $cnt->getSearchResultObject()->getJSONObject();
			$result = Loader::helper('json')->encode($object);
			$this->addFooterItem("<script type=\"text/javascript\">
			$(function() { 
				$('div[data-search=users]').concreteAjaxSearch({
					result: " . $result . ", 
					onLoad: function(concreteSearch) { 
						concreteSearch.\$element.on('click', 'a[data-user-id]', function() {
							window.location.href='" . URL::to('/dashboard/users/search', 'view') . "/' + $(this).attr('data-user-id');
							return false;
						});
					}
				});
			});
			</script>");
		}
	}

	/*

	public function view() {

		if (isset($_GET['uID'])) {
			$uo = UserInfo::getByID(intval($_GET['uID']));
			if (is_object($uo)) {
				if (!$up->canViewUser()) {
					$this->redirect('/dashboard/users/search');
				}
			}
		}
			// this is hacky as hell, we need to make this page MVC
		if (!is_object($uo)) {
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
				
				$gIDs = array();
				if (is_array($_POST['gID'])) {
					foreach($_POST['gID'] as $gID) {
						$gx = Group::getByID($gID);
						$gxp = new Permissions($gx);
						if ($gxp->canAssignGroup()) {
							$gIDs[] = $gID;
						}
					}
				}
				
				$gIDs = array_unique($gIDs);

				$uo->updateGroups($gIDs);

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
	*/

}