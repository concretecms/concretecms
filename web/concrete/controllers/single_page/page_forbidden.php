<?php

namespace Concrete\Controller\SinglePage;
use \Concrete\Core\Page\Controller\PageController;
use Loader;
use User;

class PageForbidden extends PageController {
	
	protected $viewPath = '/frontend/page_forbidden';

	public function view() {
		$u = new User();
		if (!$u->isRegistered()) { //if they are not logged in, and we show guests the login...
			$this->redirect('/login');
		}
	}


}