<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('3rdparty/facebook/facebook');
class Concrete5_Controller_AuthenticationType_Facebook extends AuthenticationTypeController {

	public function authenticate() {
		if ($post['uMaintainLogin']) {
			$user->setAuthTypeCookie('concrete');
		}
	}

	public function status() {
		$u = new User();
		if (!isset($_SESSION['authFacebookStatus'])) {
			$this->redirect('/login_oauth');
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
		}
		Loader::controller('/login_oauth')->chooseRedirect();
	}

	public function getUserByFacebookUser($fbu) {
		$db = Loader::db();
		$uid = $db->getOne('SELECT uID FROM authTypeFacebookUserMap WHERE fbUserID=?',array($fbu));
		if (!$uid) {
			throw new Exception('This facebook account is not tied to a user.');
		}
		return User::getByUserID($uid);
	}
	public function mapUserByFacebookUser($fbu) {
		$u = new User;
		$db = Loader::db();
		$db->execute('DELETE FROM authTypeFacebookUserMap WHERE uID=?',array($u->getUserId()));
		$db->execute('INSERT INTO authTypeFacebookUserMap (fbUserID,uID) VALUES (?,?)',array($fbu,$u->getUserID()));
	}

	public function getConsumer() {
		if ($this->_consumer) {
			return $this->_consumer;
		}
		$config = array();
		$config['appId'] = '267802466681488';
		$config['secret'] = 'a8ae7654d2c4d812f7c20b3bb8f4e915';
		$this->_consumer = new Facebook($config);
		return $this->getConsumer();
	}

	public function view() {
		$consumer = $this->getConsumer();
		$params = array(
		  'scope' => 'read_stream, friends_likes',
		  'redirect_uri' => BASE_URL.view::url('/login_oauth','callback','facebook'),
		  'display' => 'popup'
		);
		$loginUrl = $consumer->getLoginUrl($params);

		$u = new User;
		$this->set('loggedin',$u->isLoggedIn());
		$this->set('loginUrl',$loginUrl);
		$this->set('statusURI',View::url("/login_oauth","callback","facebook","status"));
	}

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
					$_SESSION['authFacebookStatus'] = 2; // User has been successfully attached.
					exit;
				}
				$_SESSION['authFacebookStatus'] = 1; // User not tied to FB user.
				exit;
			}
			if ($u->isLoggedIn()) {
				$_SESSION['authFacebookStatus'] = 4; // Already mapped to this user.
				exit;
			}
			User::loginByUserID($user->getUserID());
			$_SESSION['authFacebookStatus'] = 3; // Good to go.
		}
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