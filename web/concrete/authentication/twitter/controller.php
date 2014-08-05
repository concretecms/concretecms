<?php

namespace Concrete\Authentication\Twitter;

use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Database\Database;
use Concrete\Core\Form\Service\Form as FormHelper;
use Session;
use User;
use UserInfo;
use View;

class Controller
	extends AuthenticationTypeController {

	public $apiMethods = array('callback', 'status', 'detachUser');
	
	public function authenticate() {
		if ($post['uMaintainLogin']) {
			$user->setAuthTypeCookie('concrete');
		}
	}

	public function getAuthenticationTypeIconHTML() {
		return '<i class="fa fa-twitter"></i>';
	}

	public function status() {
		$u = new User();
		if (!Session::has('authTwitterStatus')) {
			throw new \Exception(t('Something went wrong, please try again.'));
		}
		$status = Session::get('authTwitterStatus');
		Session::remove('authTwitterStatus');
		if ($status == 1) {
			$uname = (USER_REGISTRATION_WITH_EMAIL_ADDRESS ? 'Email' : 'Username');
			$msg = t(/* i18n %s is the site name */
					'<h2>Oh No!</h2>This Twitter account isn\'t tied to any account of %1$s!', h(SITE)) . '<br />';
			if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
				$msg .= t(
					'Please login with your Email and Password and then use the Twitter login to tie them together.');
			} else {
				$msg .= t(
					'Please login with your Username and Password and then use the Twitter login to tie them together.');
			}
			throw new \Exception($msg);
		} else if ($status == 2) {
			$this->set('message', t("Successfully attached this Twitter account to your user account."));
			return;
		} else if ($status == 4) {
			throw new \Exception(t(/* i18n %s is the site name */
				'This Twitter account is already attached to your %s account.', h(SITE)));
		} else {
			if ($status == 5) {
				throw new \Exception(t(
					'<h2>Oh No!</h2>The email used by your Twitter account is already in use!<br>Please login to your concrete5 account and then use the Twitter login to tie your accounts together.'));
			}
		}
		$this->completeAuthentication($u);
	}
	
	public function detachUser() {
		$user = new User();
		$db = $this->getDatabase();
		$db->execute('DELETE FROM authTypeTwitterUserMap WHERE uID=?', array($user->getUserID()));
		die(1);
	}

	public function getTwitterUserInfo() {
		$u = new User();
		if (is_object($u) && $u->isLoggedIn()) {
			$db = $this->getDatabase();
			return $db->query('SELECT * FROM authTypeTwitterUserData WHERE uID=?', array($u->getUserID()))->fetchRow();
		}
	}

	public function hook() {
		$u = new User();
		$this->set('controller', $this);
		$this->set('form', new FormHelper());
		$this->set('u', $u);
		$this->view();
		$this->set('statusURI', View::url("/account/profile/edit", "callback", "twitter", "status"));
	}

	public function view() {
		$consumer = $this->getConsumer();
		$requestToken = $consumer->getRequestToken(BASE_URL . View::url('/login', 'callback', 'twitter'));
		$_SESSION['oauth_token'] = $token = $requestToken['oauth_token'];
		$_SESSION['oauth_token_secret'] = $requestToken['oauth_token_secret'];
		$loginUrl = $consumer->getAuthorizeURL($token);
		$u = new User;
		$this->set('loggedin', $u->isLoggedIn());
		$this->set('loginUrl', $loginUrl);
		$this->set('statusURI', View::url("/login", "callback", "twitter", "status"));
	}

	public function getConsumer($oauth_token = null, $oauth_token_secret=null) {
		if (!$this->_consumer) {
			$this->_consumer = new \TwitterOAuth($this->config('apikey'), $this->config('apisecret'), $oauth_token, $oauth_token_secret);
		}
		return $this->_consumer;
	}

	public function dropConsumer() {
		$this->_consumer = null;
	}

	private function getDatabase() {
		return Database::getActiveConnection();
	}

	public function config($key, $value = false) {
		$db = $this->getDatabase();
		if ($value === false) {
			return $db->getOne('SELECT value FROM authTypeTwitterSettings WHERE setting=?', array($key));
		}
		$db->execute('DELETE FROM authTypeTwitterSettings WHERE setting=?', array($key));
		$db->execute('INSERT IGNORE INTO authTypeTwitterSettings (setting,value) VALUES (?,?)', array($key, $value));
		return $value;
	}

	public function getUserImagePath($u) {
		$id = $this->getTwitterUserByUser($u->getUserID());
		return "http://graph.facebook.com/$id/picture?type=normal";
	}

	public function getTwitterUserByUser($uid) {
		$db = $this->getDatabase();
		$twitterUID = $db->getOne('SELECT twUserID FROM authTypeTwitterUserMap WHERE uID=?', array($uid));
		if (!$twitterUID) {
			throw new \Exception(t('This user is not tied to a Twitter account.'));
		}
		return $twitterUID;
	}

	public function edit() {
		$this->set('form', new FormHelper());
		$this->set('apikey', $this->config('apikey'));
		$this->set('apisecret', $this->config('apisecret'));
	}

	public function saveAuthenticationType($args) {
		$this->config('apisecret', $args['apisecret']);
		$this->config('apikey', $args['apikey']);
	}

	public function getTwitterUserAccount() {
		$consumer = $this->getConsumer();
		$userInfo = $consumer->get('account/verify_credentials');
		return $userInfo;
	}
	
	public function getIdForTwitterUser() {
		$userInfo = $this->getTwitterUserAccount();
		return (isset($userInfo->id)) ? $userInfo->id : 0;
	}

	/**
	 * Callback, you get to this page either through
	 * site/login/callback/twitter, or
	 * site/login/callback/twitter/callback
	 */
	public function callback() {
//		$this->view();
		$consumer = $this->getConsumer($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$accessToken = $consumer->getAccessToken($_REQUEST['oauth_verifier']);
		$this->dropConsumer();
		$consumer = $this->getConsumer($accessToken['oauth_token'], $accessToken['oauth_token_secret']);
		$_SESSION['access_token'] = $accessToken;
		unset($_SESSION['oauth_token']);
		unset($_SESSION['oauth_token_secret']);
		$twitterUserID = $this->getIdForTwitterUser($acessToken['screen_name']);
		
		echo "<script>window.close()</script>";
		if ($twitterUserID > 0) {
			$u = new User();
			try {
				$u = $this->getUserByTwitterUserID($twitterUserID);
			} catch (\exception $e) {
				if ($u->isLoggedIn()) {
					$this->mapUserByTwitterUserID($twitterUserID);
					$this->updateTwitterUserInfo();
					$this->setSession(2); // User has been successfully attached.
				} else {
					$twitterUserInfo = $this->getTwitterUserAccount();
					$username = $twitterUserInfo->screen_name;
					$mutedUname = $username;
					$append = 1;
					while (UserInfo::getByUserName($mutedUname)) {
						// This is a heavy handed way to do this, but it must be done.
						$mutedUname = $username . '_' . $append++;
					}
					$data['uName'] = $mutedUname;
					$data['uPassword'] = $this->genString();
					$data['uPasswordConfirm'] = $data['uPassword'];
					$data['uEmail'] = $data['uName'].'@'.md5(microtime()).'.com';
					try {
						$process = UserInfo::register($data);
					} catch (\exception $e) {
						exit; // This will default to the generic "something broke" message.
					}
					if (!$process) {
						exit; // This will default to the generic "something broke" message.
					}
					User::loginByUserID($process->uID);
					$this->mapUserByTwitterUserID($this->getIdForTwitterUser());
					$this->updateTwitterUserInfo();
					$this->setSession(3);
					$u = new User();
				}
			}
			if ($u->isLoggedIn()) {
				$this->mapUserByTwitterUserID($twitterUserID);
				$this->setSession(2); // User has been successfully attached.
			}
			User::loginByUserID($u->getUserID());
			$this->updateTwitterUserInfo();
			$this->setSession(3); // Good to go.
		}
		exit; // just in case.
	}

	public function getUserByTwitterUserID($twitterUserID) {
		$db = $this->getDatabase();
		$uid = $db->getOne('SELECT uID FROM authTypeTwitterUserMap WHERE twUserID=?', array($twitterUserID));
		if (!$uid) {
			throw new \Exception(t('This Twitter account is not tied to a user.'));
		}
		return User::getByUserID($uid);
	}

	public function mapUserByTwitterUserID($twitterUserID) {
		$u = new User;
		$db = $this->getDatabase();
		$db->execute('DELETE FROM authTypeTwitterUserMap WHERE twUserID=? OR uID=?', array($twitterUserID, $u->getUserID()));
		$db->execute('INSERT INTO authTypeTwitterUserMap (twUserID,uID) VALUES (?,?)', array($twitterUserID, $u->getUserID()));
	}

	public function updateTwitterUserInfo() {
		$db = $this->getDatabase();
		try {
			$u = new User();
			$twitterUserAccount = $this->getTwitterUserAccount();
			$data = array(
			    'name'=>$twitterUserAccount->name,
			    'username'=>$twitterUserAccount->screen_name,
			    'timezone'=>$twitterUserAccount->time_zone,
			    'locale'=>$twitterUserAccount->lang,
			);
			$db->execute('DELETE FROM authTypeTwitterUserData WHERE uID=?', array($u->getUserID()));
			$db->execute('INSERT INTO authTypeTwitterUserData ('.implode(',',array_keys($data)).') VALUES (:'.implode(', :',array_keys($data)).')', $data);
		} catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

	private function setSession($var = 3) {
		Session::set('authTwitterStatus', $var);
		exit;
	}

	private function genString($a = 20) {
		$o = '';
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+{}|":<>?\'\\';
		$l = strlen($chars);
		while ($a--) {
			$o .= substr($chars, rand(0, $l), 1);
		}
		return md5($o);
	}

	public function deauthenticate(User $u) {
		
	}

	public function verifyHash(User $u, $hash) {
		// This currently does nothing.
		return true;
	}

	public function buildHash(User $u, $test = 1) {
		// This doesn't do anything.
		return 1;
	}

	public function isAuthenticated(User $u) {
		return ($u->isLoggedIn());
	}

}
