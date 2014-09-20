<?php
namespace Concrete\Core\Application\Service;
use Loader;
use TaskPermission;
use Config;

defined('C5_EXECUTE') or die("Access Denied.");
class User {

	function getOnlineNow($uo, $showSpacer = true) {
		$ul = 0;
		if (is_object($uo)) {
			// user object
			$ul = $uo->getLastOnline();
		} else if (is_numeric($uo)) {
			$db = Loader::db();
			$ul = $db->getOne("select uLastOnline from Users where uID = {$uo}");
		}

		$online = (time() - $ul) <= ONLINE_NOW_TIMEOUT;

		if ($online) {

			return ONLINE_NOW_SRC_ON;
		} else {
			if ($showSpacer) {
				return ONLINE_NOW_SRC_OFF;
			}

		}
	}

	public function validNewPassword( $password, $errorObj=NULL){

		$valc = Loader::helper('concrete/validation');

		if ((strlen($password) < Config::get('concrete.user.password.minimum')) || (strlen($password) >  Config::get('concrete.user.password.maximum'))) {
			if($errorObj)
				$errorObj->add( t('A password must be between %s and %s characters', Config::get('concrete.user.password.minimum'),  Config::get('concrete.user.password.maximum')) );
			$invalid=1;
		}

		if($invalid) return false;

		return true;
	}

	public function canAccessUserSearchInterface() {
		$tp = new TaskPermission();
		return $tp->canAccessUserSearch();
	}
}
