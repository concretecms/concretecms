<?php
namespace Concrete\Controller\SinglePage;
use \Concrete\Core\Page\Controller\PageController;
use Config;
use Loader;
use User;
use UserInfo;
use UserAttributeKey;

class Register extends PageController {

	public $helpers = array('form', 'html');

	protected $displayUserName = true;

	public function on_start() {
		if(!in_array(Config::get('concrete.user.registration.type'), array('validate_email', 'enabled', 'manual_approve'))) {
            $this->render('/page_not_found');
 		}
		$u = new User();
		$this->set('u', $u);
		$this->set('displayUserName', $this->displayUserName);
		$this->requireAsset('css', 'core/frontend/captcha');
	}

	public function forward($cID = 0) {
		$this->set('rcID', Loader::helper('security')->sanitizeInt($cID));
	}

	public function do_register() {
		$userHelper = Loader::helper('concrete/user');
		$e = Loader::helper('validation/error');
		$ip = Loader::helper('validation/ip');
		$txt = Loader::helper('text');
		$vals = Loader::helper('validation/strings');
		$valc = Loader::helper('concrete/validation');

		$username = $_POST['uName'];
		$password = $_POST['uPassword'];
		$passwordConfirm = $_POST['uPasswordConfirm'];

		// clean the username
		$username = trim($username);
		$username = preg_replace("/ +/", " ", $username);
		
		if ($ip->isBanned()) {
			$e->add($ip->getErrorMessage());
		}

		if (Config::get('concrete.user.registration.captcha')) {
			$captcha = Loader::helper('validation/captcha');
			if (!$captcha->check()) {
				$e->add(t("Incorrect image validation code. Please check the image and re-enter the letters or numbers as necessary."));
			}
		}

		if (!$vals->email($_POST['uEmail'])) {
			$e->add(t('Invalid email address provided.'));
		} else if (!$valc->isUniqueEmail($_POST['uEmail'])) {
			$e->add(t("The email address %s is already in use. Please choose another.", $_POST['uEmail']));
		}

		if ($this->displayUserName) {

			if (strlen($username) < Config::get('concrete.user.username.minimum')) {
				$e->add(t('A username must be at least %s characters long.', Config::get('concrete.user.username.minimum')));
			}

			if (strlen($username) > Config::get('concrete.user.username.maximum')) {
				$e->add(t('A username cannot be more than %s characters long.', Config::get('concrete.user.username.maximum')));
			}


			if (strlen($username) >= Config::get('concrete.user.username.minimum') && !$valc->username($username)) {
				if(Config::get('concrete.user.username.allow_spaces')) {
					$e->add(t('A username may only contain letters, numbers and spaces.'));
				} else {
					$e->add(t('A username may only contain letters or numbers.'));
				}

			}
			if (!$valc->isUniqueUsername($username)) {
				$e->add(t("The username %s already exists. Please choose another", $username));
			}

		}

		if ($username == USER_SUPER) {
			$e->add(t('Invalid Username'));
		}

		/*
		if ((strlen($password) < Config::get('concrete.user.password.minimum')) || (strlen($password) >  Config::get('concrete.user.password.maximum'))) {
			$e->add(t('A password must be between %s and %s characters', Config::get('concrete.user.password.minimum'),  Config::get('concrete.user.password.maximum')));
		}

		if (strlen($password) >= Config::get('concrete.user.password.minimum') && !$valc->password($password)) {
			$e->add(t('A password may not contain ", \', >, <, or any spaces.'));
		}
		*/

		$userHelper->validNewPassword($password,$e);

		if ($password) {
			if ($password != $passwordConfirm) {
				$e->add(t('The two passwords provided do not match.'));
			}
		}

		$aks = UserAttributeKey::getRegistrationList();

		foreach($aks as $uak) {
			if ($uak->isAttributeKeyRequiredOnRegister()) {
				$e1 = $uak->validateAttributeForm();
				if ($e1 == false) {
					$e->add(t('The field "%s" is required', $uak->getAttributeKeyDisplayName()));
				} else if ($e1 instanceof \Concrete\Core\Error\Error) {
					$e->add($e1);
				}
			}
		}

		if (!$e->has()) {

			// do the registration
			$data = $_POST;
			$data['uName'] = $username;
			$data['uPassword'] = $password;
			$data['uPasswordConfirm'] = $passwordConfirm;

			$process = UserInfo::register($data);
			if (is_object($process)) {

                $process->saveUserAttributesForm($aks);

				if (Config::get('concrete.user.registration.notification')) { //do we notify someone if a new user is added?
					$mh = Loader::helper('mail');
					if(Config::get('concrete.user.registration.notification_email')) {
						$mh->to(Config::get('concrete.user.registration.notification_email'));
					} else {
						$adminUser = UserInfo::getByID(USER_SUPER_ID);
						if (is_object($adminUser)) {
							$mh->to($adminUser->getUserEmail());
						}
					}

					$mh->addParameter('uID',    $process->getUserID());
					$mh->addParameter('user',   $process);
					$mh->addParameter('uName',  $process->getUserName());
					$mh->addParameter('uEmail', $process->getUserEmail());
					$attribs = UserAttributeKey::getRegistrationList();
					$attribValues = array();
					foreach($attribs as $ak) {
						$attribValues[] = $ak->getAttributeKeyDisplayName('text') . ': ' . $process->getAttribute($ak->getAttributeKeyHandle(), 'display');
					}
					$mh->addParameter('attribs', $attribValues);
					$mh->addParameter('siteName', Config::get('concrete.site'));

					if (Config::get('concrete.user.registration.notification_email')) {
						$mh->from(Config::get('concrete.user.registration.notification_email'),  t('Website Registration Notification'));
					} else {
						$adminUser = UserInfo::getByID(USER_SUPER_ID);
						if (is_object($adminUser)) {
							$mh->from($adminUser->getUserEmail(),  t('Website Registration Notification'));
						}
					}
					if(Config::get('concrete.user.registration.type') == 'manual_approve') {
						$mh->load('user_register_approval_required');
					} else {
						$mh->load('user_register');
					}
					$mh->sendMail();
				}

				// now we log the user in
				if (Config::get('concrete.user.registration.email_registration')) {
					$u = new User($_POST['uEmail'], $_POST['uPassword']);
				} else {
					$u = new User($_POST['uName'], $_POST['uPassword']);
				}
				// if this is successful, uID is loaded into session for this user

				$rcID = $this->post('rcID');
				$nh = Loader::helper('validation/numbers');
				if (!$nh->integer($rcID)) {
					$rcID = 0;
				}

				// now we check whether we need to validate this user's email address
				if (Config::get('concrete.user.registration.validate_email')) {
                    $uHash = $process->setupValidation();

                    $mh = Loader::helper('mail');
                    if (defined('EMAIL_ADDRESS_VALIDATE')) {
                        $mh->from(EMAIL_ADDRESS_VALIDATE,  t('Validate Email Address'));
                    }
                    $mh->addParameter('uEmail', $_POST['uEmail']);
                    $mh->addParameter('uHash', $uHash);
                    $mh->addParameter('site', Config::get('concrete.site'));
                    $mh->to($_POST['uEmail']);
                    $mh->load('validate_user_email');
                    $mh->sendMail();

                    //$this->redirect('/register', 'register_success_validate', $rcID);
                    $redirectMethod='register_success_validate';
                    $u->logout();

				} else if(Config::get('concrete.user.registration.approval')) {
					$ui = UserInfo::getByID($u->getUserID());
					$ui->deactivate();
					//$this->redirect('/register', 'register_pending', $rcID);
					$redirectMethod='register_pending';
					$this->set('message', $this->getRegisterPendingMsg());
					$u->logout();
				}

				if (!$u->isError()) {
					//$this->redirect('/register', 'register_success', $rcID);
					if(!$redirectMethod){
						$redirectMethod='register_success';
					}
				}


				if($_REQUEST['format']!='JSON')
					$this->redirect('/register', $redirectMethod, $rcID);
			}
		} else {
			$ip->logSignupRequest();
			if ($ip->signupRequestThreshholdReached()) {
				$ip->createIPBan();
			}		
			$this->set('error', $e);
		}
	}

	public function register_success_validate($rcID = 0) {
		$this->set('rcID', $rcID);
		$this->set('registerSuccess', 'validate');
		$this->set('successMsg', $this->getRegisterSuccessValidateMsgs() );
	}

	public function register_success($rcID = 0) {
		$this->set('rcID', $rcID);
		$this->set('registerSuccess', 'registered');
		$this->set('successMsg', $this->getRegisterSuccessMsg() );
	}

	public function register_pending($rcID = 0) {
		$this->set('rcID', $rcID);
		$this->set('registerSuccess', 'pending');
		$this->set('successMsg', $this->getRegisterPendingMsg() );
	}

	public function getRegisterSuccessMsg(){
		return t('Your account has been created, and you are now logged in.');
	}

	public function getRegisterSuccessValidateMsgs(){
		$msgs=array();
		$msgs[]= t('You are registered but you need to validate your email address. Some or all functionality on this site will be limited until you do so.');
		$msgs[]= t('An email has been sent to your email address. Click on the URL contained in the email to validate your email address.');
		return $msgs;
	}

	public function getRegisterPendingMsg(){
		return t('You are registered but a site administrator must review your account, you will not be able to login until your account has been approved.');
	}
}

?>
