<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	if (User::isLoggedIn()) {
		
		// check to see if this is a valid user account
		$u = new User();
		if (!$u->checkLogin()) {
			$u = $u->logout();
			$v = View::getInstance();
			$v->setTheme(VIEW_CORE_THEME);
			$v->render("/user_error");		
		}
	} else {
		User::checkUserForeverCookie();
	}