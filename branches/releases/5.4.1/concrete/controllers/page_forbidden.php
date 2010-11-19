<?php 

defined('C5_EXECUTE') or die("Access Denied.");

Loader::controller('/login');

class PageForbiddenController extends LoginController {
	
	public function view() {
		$v = View::getInstance();
		$c = $v->getCollectionObject();
		if (is_object($c)) {
			$cID = $c->getCollectionID();
			if($cID) { 
				$this->forward($cID); // set the intended url
			}
		}
		parent::view();
		$this->render('/login');
	}
	
}