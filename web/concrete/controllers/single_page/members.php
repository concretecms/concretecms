<?php
namespace Concrete\Controller\SinglePage;
use Concrete\Core\Page\Controller\PublicProfilePageController;
class Members extends PublicProfilePageController {
	
	public function view() {
        $this->redirect('/members/directory');
	}

}