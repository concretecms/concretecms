<?php
namespace Concrete\Controller\PageType;

use Concrete\Core\Page\Controller\PageTypeController;
use Loader;
use Page;
use Permissions;
use View;

class CoreStack extends PageTypeController {


	public function on_start() {
		$c = Page::getByPath('/dashboard/blocks/stacks');
		$cp = new Permissions($c);
		if ($cp->canViewPage()) {
			$c = Page::getCurrentPage();
			$pcp = new Permissions($c);
			if ((!$pcp->canViewPageVersions()) || ($_GET['vtask'] != 'view_versions' && $_GET['vtask'] != 'compare')) {
				$cID = $c->getCollectionID();
				$this->redirect('/dashboard/blocks/stacks','view_details', $cID);		
			} else {
				$this->theme = 'dashboard';
			}
		} else {
			global $c; // ugh
			$v = View::getInstance();
			$c = new Page();
			$c->loadError(COLLECTION_NOT_FOUND);
			$v->setCollectionObject($c);
			$this->c = $c;
			$cont = Loader::controller("/page_not_found");
			$v->setController($cont);				
			$v->render('/page_not_found');
		}
	}		
	
}