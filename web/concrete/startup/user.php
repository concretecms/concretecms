<?
	defined('C5_EXECUTE') or die("Access Denied.");
	if (!User::isLoggedIn()) {
		User::checkUserForeverCookie();
	}
	
	if (User::isLoggedIn()) {		
		// check to see if this is a valid user account
		$u = new User();
		if (!$u->checkLogin()) {
			$u->logout();
			$v = View::getInstance();
			$v->setTheme(VIEW_CORE_THEME);
			if (!$u->isActive()) {
				Loader::controller('/login')->redirect("/login", "account_deactivated");		
			} else {
				$v->render("/user_error");		
			}
		}
	}
