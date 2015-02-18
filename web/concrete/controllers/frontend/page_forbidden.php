<?php

namespace Concrete\Controller\Frontend;
use Controller;
use User;
class PageForbidden extends Controller {
	
	protected $viewPath = '/frontend/page_forbidden';

	public function view() {
		$u = new User();
        if (!$u->isRegistered()) { //if they are not logged in, and we show guests the login...
			$this->redirect('/login');
		}
	}

	
}