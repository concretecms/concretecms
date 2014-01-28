<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('3rdparty/facebook/facebook');
class Concrete5_Controller_AuthenticationType_Facebook extends AuthenticationTypeController {

	public $apiMethods = array('callback','status','detachUser');

	public function authenticate() {
		if ($post['uMaintainLogin']) {
			$user->setAuthTypeCookie('concrete');
		}
	}

	public function status() {
		$u = new User();
		if (!isset($_SESSION['authFacebookStatus'])) {
			return;
			throw new exception('Something went wrong, please try again.');
		}
		$status = $_SESSION['authFacebookStatus'];
		unset($_SESSION['authFacebookStatus']);
		if ($status == 1) {
			$site = SITE;
			if (in_array(substr($site,0,1),array('a','e','i','o','u'))) {
				$sitename = "an $site";
			} else {
				$sitename = "a $site";
			}
			$uname = (USER_REGISTRATION_WITH_EMAIL_ADDRESS?'Email':'Username');
			throw new Exception('<h2>Oh No!</h2>This facebook account isn\'t tied to '.$sitename.' account!<br>Please login with your '.$uname.' and Password and then use the facebook login to tie them together.');
		} else if ($status == 2) {
			return "Successfully attached this facebook account to your user account.";
		} else if ($status == 4) {
			throw new Exception('This facebook account is already attached to your '.SITE.' account.');
		} else if ($status == 5) {
			throw new Exception('<h2>Oh No!</h2>The email used by your facebook account is already in use!<br>Please login to your concrete5 account and then use the facebook login to tie your accounts together.');
		}

		$this->completeAuthentication();
	}

	public function config($key,$value=false) {
		$db = Loader::db();
		if (!$value) {
			return $db->getOne('SELECT value FROM authTypeFacebookSettings WHERE setting=?',array($key));
		}
		$db->execute('DELETE FROM authTypeFacebookSettings WHERE setting=?',array($key));
		$db->execute('INSERT IGNORE INTO authTypeFacebookSettings (setting,value) VALUES (?,?)',array($key,$value));
		return $value;
	}

	public function getUserByFacebookUser($fbu) {
		$db = Loader::db();
		$uid = $db->getOne('SELECT uID FROM authTypeFacebookUserMap WHERE fbUserID=?',array($fbu));
		if (!$uid) {
			throw new Exception('This facebook account is not tied to a user.');
		}
		return User::getByUserID($uid);
	}

	public function getFacebookUserByUser($uid) {
		$db = Loader::db();
		$fbuid = $db->getOne('SELECT fbUserID FROM authTypeFacebookUserMap WHERE uID=?',array($uid));
		if (!$fbuid) {
			throw new Exception('This user is not tied to a facebook account.');
		}
		return $fbuid;
	}

	public function mapUserByFacebookUser($fbu) {
		$u = new User;
		$db = Loader::db();
		$db->execute('DELETE FROM authTypeFacebookUserMap WHERE fbUserID=? OR uID=?',array($fbu,$u->getUserID()));
		$db->execute('INSERT INTO authTypeFacebookUserMap (fbUserID,uID) VALUES (?,?)',array($fbu,$u->getUserID()));
	}

	public function detachUser() {
		$user = new User();
		$db = Loader::db();
		$db->execute('DELETE FROM authTypeFacebookUserMap WHERE uID=?',array($user->getUserID()));
		die(1);
	}

	public function updateFacebookUserInfo() {
		$db = Loader::db();
		try {
			$u = new User();
			$fbuserarr = $this->getConsumer()->api('/me');
			$arr = array('name','first_name','last_name','link','username','birthday','gender','email','timezone','locale','verified','updated_time');
			$dbarr = array();
			$dbarr[] = $u->getUserID();
			foreach($arr as $key) {
				$dbarr[] = $fbuserarr[$key];
			}
			$db->execute('DELETE FROM authTypeFacebookUserData WHERE uID=?',array($u->getUserID()));
			$db->execute('INSERT INTO authTypeFacebookUserData VALUES ('.str_repeat('?,', count($arr)).'?)',
				$dbarr);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	public function getFacebookUserInfo() {
		$u = new User();
		if (is_object($u) && $u->isLoggedIn()) {
			$db = Loader::db();
			return $db->query('SELECT * FROM authTypeFacebookUserData WHERE uID=?',array($u->getUserID()))->fetchRow();
		}
	}

	public function hook() {
		$u = new User();
		$this->set('controller',$this);
		$this->set('form',Loader::helper('form'));
		$this->set('u',$u);
		$this->view();
		$this->set('statusURI',View::url("/account/profile/edit","callback","facebook","status"));
	}

	public function getConsumer() {
		if ($this->_consumer) {
			return $this->_consumer;
		}
		$config = array();
		$config['appId'] = $this->config('apikey');
		$config['secret'] = $this->config('apisecret');
		$this->_consumer = new Facebook($config);
		return $this->getConsumer();
	}

	public function view() {
		$consumer = $this->getConsumer();
		$params = array(
		  'scope' => 'email,user_birthday,user_location,user_photos,user_status,user_website',
		  'redirect_uri' => BASE_URL.view::url('/login','callback','facebook'),
		  'display' => 'popup'
		);
		$loginUrl = $consumer->getLoginUrl($params);
		$u = new User;
		$this->set('loggedin',$u->isLoggedIn());
		$this->set('loginUrl',$loginUrl);
		$this->set('statusURI',View::url("/login","callback","facebook","status"));
	}

	public function getUserImagePath($u) {
		$id = $this->getFacebookUserByUser($u->getUserID());
		return "http://graph.facebook.com/$id/picture?type=normal";
	}

	public function edit() {
		$this->set('form',Loader::helper('form'));
		$this->set('apikey',$this->config('apikey'));
		$this->set('apisecret',$this->config('apisecret'));
	}

	public function saveAuthenticationType($args) {
		$this->config('apisecret',$args['apisecret']);
		$this->config('apikey',$args['apikey']);
	}

	/**
	 * Callback, you get to this page either through
	 * site/login/callback/facebook, or
	 * site/login/callback/facebook/callback
	 */
	public function callback() {
		$this->view();
		$consumer = $this->getConsumer();
		$fbuser = $consumer->getUser();
		echo "<script>window.close()</script>";
		if ($fbuser > 0) {
			$u = new User();
			try {
				$user = $this->getUserByFacebookUser($fbuser);
			} catch (exception $e) {
				if ($u->isLoggedIn()) {
					$this->mapUserByFacebookUser($fbuser);
					$this->updateFacebookUserInfo();
					$this->setSession(2); // User has been successfully attached.
				}
				$fbuserarr = $consumer->api('/me');
				$username = $fbuserarr['username'];
				$mutedUname = $username;
				$append = 1;
				while (UserInfo::getByUserName($mutedUname)) {
					// This is a heavy handed way to do this, but it must be done.
					$mutedUname = $username.'_'.$append++;
				}
				if (UserInfo::getByEmail($fbuserarr['email'])) {
					$this->setSession(5); // Email already in use.
				}
				$data['uName'] = $mutedUname;
				$data['uPassword'] = $this->genString();
				$data['uPasswordConfirm'] = $data['uPassword'];
				$data['uEmail'] = $fbuserarr['email'];
				try {
					$process = UserInfo::register($data);
				} catch (exception $e) {
					exit; // This will default to the generic "something broke" message.
				}
				if (!$process) {
					exit; // This will default to the generic "something broke" message.
				}
				User::loginByUserID($process->uID);
				$this->mapUserByFacebookUser($consumer->getUser());
				$this->updateFacebookUserInfo();
				$this->setSession(3);
			}
			if ($u->isLoggedIn()) {
				$this->mapUserByFacebookUser($fbuser);
				$this->setSession(2); // User has been successfully attached.
			}
			User::loginByUserID($user->getUserID());
			$this->updateFacebookUserInfo();
			$this->setSession(3); // Good to go.
		}
		exit; // just in case.
	}

	private function setSession($var=3) {
		$_SESSION['authFacebookStatus'] = $var;
		exit;
	}

	public function deauthenticate(User $u) {}

	public function verifyHash(User $u, $hash) {
		// This currently does nothing.
		return true;
	}

	private function genString($a=20) {
		$o = '';
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+{}|":<>?\'\\';
		$l = strlen($chars);
		while ($a--) {
			$o .= substr($chars, rand(0,$l), 1);
		}
		return md5($o);
	}

	public function buildHash(User $u,$test=1) {
		// This doesn't do anything.
		return 1;
	}

	public function isAuthenticated(User $u) {
		return ($u->isLoggedIn());
	}

}
