<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::controller('/login');

class PageForbiddenController extends LoginController {
	
	
	public function view() {
		parent::view();
		$this->render('/login');
	}
	
}