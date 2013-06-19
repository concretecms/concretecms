<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_Users_Add extends Controller {

	public function on_start() {
		$this->set('form',Loader::helper('form'));
		$this->set('valt',Loader::helper('validation/token'));
		$this->set('valc',Loader::helper('concrete/validation'));
		$this->set('ih',Loader::helper('concrete/interface'));
		$this->set('av',Loader::helper('concrete/avatar'));
		$this->set('dtt',Loader::helper('form/date_time'));
			
		$this->error = Loader::helper('validation/error');
	}

	public function view(){
			$assignment = PermissionKey::getByHandle('edit_user_properties')->getMyAssignment();
			$vals = Loader::helper('validation/strings');
			$valt = Loader::helper('validation/token');
			$valc = Loader::helper('concrete/validation');
			
			if ($_POST['create']) {
				
				$username = trim($_POST['uName']);
				$username = preg_replace("/\s+/", " ", $username);
				$_POST['uName'] = $username;	
				
				$password = $_POST['uPassword'];
				
				if (!$vals->email($_POST['uEmail'])) {
					$this->error->add(t('Invalid email address provided.'));
				} else if (!$valc->isUniqueEmail($_POST['uEmail'])) {
					$this->error->add(t("The email address '%s' is already in use. Please choose another.",$_POST['uEmail']));
				}
				
				if (strlen($username) < USER_USERNAME_MINIMUM) {
					$this->error->add(t('A username must be between at least %s characters long.',USER_USERNAME_MINIMUM));
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
			
				if (!$valc->isUniqueUsername($username)) {
					$this->error->add(t("The username '%s' already exists. Please choose another",$username));
				}		
			
				if ($username == USER_SUPER) {
					$this->error->add(t('Invalid Username'));
				}
			
				
				if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
					$this->error->add(t('A password must be between %s and %s characters',USER_PASSWORD_MINIMUM,USER_PASSWORD_MAXIMUM));
				}
					
				if (strlen($password) >= USER_PASSWORD_MINIMUM && !$valc->password($password)) {
					$this->error->add(t('A password may not contain ", \', >, <, or any spaces.'));
				}
			
				if (!$valt->validate('create_account')) {
					$this->error->add($valt->getErrorMessage());
				}
			
				Loader::model("attribute/categories/user");
				$aks = UserAttributeKey::getRegistrationList();
			
				foreach($aks as $uak) {
					if ($uak->isAttributeKeyRequiredOnRegister()) {
						$e1 = $uak->validateAttributeForm();
						if ($e1 == false) {
							$this->error->add(t('The field "%s" is required', tc('AttributeKeyName', $uak->getAttributeKeyName())));
						} else if ($e1 instanceof ValidationErrorHelper) {
							$this->error->add( $e1->getList() );
						}
					}
				}
				
				if (!$this->error->has()) {
					// do the registration
					$data = array('uName' => $username, 'uPassword' => $password, 'uEmail' => $_POST['uEmail'], 'uDefaultLanguage' => $_POST['uDefaultLanguage']);
					$uo = UserInfo::add($data);
					
					if (is_object($uo)) {
						
						$av = Loader::helper('concrete/avatar'); 
						if ($assignment->allowEditAvatar()) {
							if (is_uploaded_file($_FILES['uAvatar']['tmp_name'])) {
								$uHasAvatar = $av->updateUserAvatar($_FILES['uAvatar']['tmp_name'], $uo->getUserID());
							}
						}
						
						foreach($aks as $uak) {
							if (in_array($uak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { 
								$uak->saveAttributeForm($uo);
							}
						}

						$gak = PermissionKey::getByHandle('assign_user_groups');
						$gIDs = array();
						if (is_array($_POST['gID'])) {
							foreach($_POST['gID'] as $gID) {
								if ($gak->validate($gID)) {
									$gIDs[] = $gID;
								}
							}
						}
		
						$uo->updateGroups($gIDs);
						$uID = $uo->getUserID();
						$this->redirect('/dashboard/users/search?uID=' . $uID . '&user_created=1');
					} else {
						$this->error->add(t('An error occurred while trying to create the account.'));
						$this->set('error',$this->error);
					}
					
				}else{
					$this->set('error',$this->error);
				}
			}
	}
}