<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_AuthenticationType_Concrete extends AuthenticationTypeController {

	public function authenticate() {
		$post = $this->post();

		if (!isset($post['uName']) || !isset($post['uPassword'])) {
			throw new Exception('Please provide both username and password.');
		}
		$uName = $post['uName'];
		$uPassword = $post['uPassword'];

		$user = new User($uName,$uPassword);
		if (!is_object($user) || !($user instanceof User) || $user->isError()) {
			switch($user->getError()) {
				case USER_NON_VALIDATED:
					throw new Exception(t('This account has not yet been validated. Please check the email associated with this account and follow the link it contains.'));
					break;
				case USER_INVALID:
					if (USER_REGISTRATION_WITH_EMAIL_ADDRESS) {
						throw new Exception(t('Invalid email address or password.'));
					} else {
						throw new Exception(t('Invalid username or password.'));
					}
					break;
				case USER_INACTIVE:
					throw new Exception(t('This user is inactive. Please contact us regarding this account.'));
					break;
			}
		}
		if ($post['uMaintainLogin']) {
			$user->setAuthTypeCookie('concrete');
		}
	}

	public function deauthenticate(User $u) {
		list($uID, $authType, $hash) = explode(':', $_COOKIE['ccmAuthUserHash']);
		if ($authType == 'concrete') {
			$db = Loader::db();
			$db->execute('DELETE FROM authTypeConcreteCookieMap WHERE uID=? AND token=?',array($uID,$hash));
		}
	}

	public function verifyHash(User $u, $hash) {
		$uID = $u->getUserID();
		$db = Loader::db();
		$q = $db->getOne('SELECT validThrough FROM authTypeConcreteCookieMap WHERE uID=? AND token=?',array($uID,$hash));
		$bool = time() < $q;
		if (!$bool) {
			$db->execute('DELETE FROM authTypeConcreteCookieMap WHERE uID=? AND token=?',array($uID,$hash));
		} else {
			$newTime = strtotime('+2 weeks');
			$db->execute('UPDATE authTypeConcreteCookieMap SET validThrough=?',array($newTime));
		}
		return $bool;
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

	public function buildHash(User $u) {
		$db = Loader::db();

		$validThrough = strtotime('+2 weeks');
		$token = $this->genString();
		try {
			$db->execute('INSERT INTO authTypeConcreteCookieMap (token, uID, validThrough) VALUES (?,?,?)',array($token, $u->getUserID(), $validThrough));
		} catch (exception $e) {
			// HOLY CRAP.. SERIOUSLY?
			die($e->getMessage());
			return $this->buildHash($u);
		}
		return $token;
	}

	public function isAuthenticated(User $u) {
		return ($u->isLoggedIn());
	}

}