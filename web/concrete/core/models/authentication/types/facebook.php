<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('3rdparty/Zend/Oauth/consumer');
class Concrete5_Controller_AuthenticationType_Facebook extends AuthenticationTypeController {

	public function authenticate() {
		if ($post['uMaintainLogin']) {
			$user->setAuthTypeCookie('concrete');
		}
	}

	public function view() {
		$config = array(
			'callbackUrl' => BASE_URL.view::url("/login","callback","facebook","callback"),
			'siteUrl' => 'http://twitter.com/oauth',
			'consumerKey' => 'gg3DsFTW9OU9eWPnbuPzQ',
			'consumerSecret' => 'tFB0fyWLSMf74lkEu9FTyoHXcazOWpbrAjTCCK48A'
		);
		$consumer = new Zend_Oauth_Consumer($config);

		$this->set('consumer',$consumer);
	}

	public function callback() {
		print_r($_REQUEST);
		exit;
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

	public function buildHash(User $u,$test=1) {
		if ($test>10) {
			// This should only ever happen if by some stroke of divine intervention,
			// we end up pulling 10 hashes that already exist. the chances of this are very very low.
			throw new exception('There was a database error, try again.');
		}
		$db = Loader::db();

		$validThrough = strtotime('+2 weeks');
		$token = $this->genString();
		try {
			$db->execute('INSERT INTO authTypeConcreteCookieMap (token, uID, validThrough) VALUES (?,?,?)',array($token, $u->getUserID(), $validThrough));
		} catch (exception $e) {
			// HOLY CRAP.. SERIOUSLY?
			$this->buildHash($u,$test++);
		}
		return $token;
	}


	public function isAuthenticated(User $u) {
		return ($u->isLoggedIn());
	}

}