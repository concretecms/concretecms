<?php
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Edit_Page extends Controller {

	public function check_in($cID, $token) {
		if (Loader::helper('validation/token')->validate('', $token)) {
			$c = Page::getByID($cID);
			$cp = new Permissions($c);
			if ($cp->canViewToolbar()) {
				$u = new User();
				$u->unloadCollectionEdit();
			}
			return Redirect::page($c);
		}
	
		return new Response(t('Access Denied'));
	}


}
	
