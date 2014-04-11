<?
namespace Concrete\Controller\SinglePage\Profile;
use \Concrete\Core\Page\Controller\AccountPageController;
use UserInfo;
use Exception;
use \Concrete\Core\Authentication\AuthenticationType;
use \Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Loader;
use UserAttributeKey;

class Edit extends AccountPageController {
	
	public function view() {
		$u = new User();
		$profile = UserInfo::getByID($u->getUserID());
		if (is_object($profile)) {
			$this->set('profile', $profile);
		} else {
			throw new Exception(t('You must be logged in to access this page.'));
		}
	}
	
	public function on_start() {
		parent::on_start();
		$this->set('valt', Loader::helper('validation/token'));
	}

	public function callback($type,$method='callback') {
		$at = AuthenticationType::getByHandle($type);
		$this->view();
		if (!method_exists($at->controller, $method)) {
			throw new exception('Invalid method.');
		}
		if ($method != 'callback') {
			if (!is_array($at->controller->apiMethods) || !in_array($method,$at->controller->apiMethods)) {
				throw new Exception("Invalid method.");
			}
		}
		try {
			$message = call_user_method($method, $at->controller);
			if (trim($message)) {
				$this->set('message',$message);
			}
		} catch (exception $e) {
			if ($e instanceof AuthenticationTypeFailureException) {
				// Throw again if this is a big`n
				throw $e;
			}
			$this->error->add($e->getMessage());
		}
	}
		
	public function save() { 
		$this->view();
		$ui = $this->get('profile');

		$uh = Loader::helper('concrete/user');
		$th = Loader::helper('text');
		$vsh = Loader::helper('validation/strings');
		$cvh = Loader::helper('concrete/validation');
		$valt = Loader::helper('validation/token');
		$e = Loader::helper('validation/error');
	
		$data = $this->post();
		
		/* 
		 * Validation
		*/
		//token
		if(!$valt->validate('profile_edit')) {
			$e->add($valt->getErrorMessage());
		}

		// validate the user's email
		$email = $this->post('uEmail');
		if (!$vsh->email($email)) {
			$e->add(t('Invalid email address provided.'));
		} else if (!$cvh->isUniqueEmail($email) && $ui->getUserEmail() != $email) {
			$e->add(t("The email address '%s' is already in use. Please choose another.",$email));
		}


		// password
		if(strlen($data['uPasswordNew'])) {
			$passwordNew = $data['uPasswordNew'];
			$passwordNewConfirm = $data['uPasswordNewConfirm'];
			
			if ((strlen($passwordNew) < USER_PASSWORD_MINIMUM) || (strlen($passwordNew) > USER_PASSWORD_MAXIMUM)) {
				$e->add(t('A password must be between %s and %s characters', USER_PASSWORD_MINIMUM, USER_PASSWORD_MAXIMUM));
			}		
			
			if (strlen($passwordNew) >= USER_PASSWORD_MINIMUM && !$cvh->password($passwordNew)) {
				$e->add(t('A password may not contain ", \', >, <, or any spaces.'));
			}
			
			if ($passwordNew) {
				if ($passwordNew != $passwordNewConfirm) {
					$e->add(t('The two passwords provided do not match.'));
				}
			}
			$data['uPasswordConfirm'] = $passwordNew;
			$data['uPassword'] = $passwordNew;
		}		
		
		$aks = UserAttributeKey::getEditableInProfileList();

		foreach($aks as $uak) {
			if ($uak->isAttributeKeyRequiredOnProfile()) {
				$e1 = $uak->validateAttributeForm();
				if ($e1 == false) {
					$e->add(t('The field "%s" is required', $uak->getAttributeKeyDisplayName()));
				} else if ($e1 instanceof \Concrete\Helper\Validation\Error) {
					$e->add($e1);
				}
			}
		}

		if (!$e->has()) {		
			$data['uEmail'] = $email;		
			if(ENABLE_USER_TIMEZONES) {
				$data['uTimezone'] = $this->post('uTimezone');
			}
			
			$ui->update($data);
			
			foreach($aks as $uak) {
				$uak->saveAttributeForm($ui);				
			}
			$this->redirect("/account/profile/public", "save_complete");
		} else {
			$this->set('error', $e);
		}
	}
}