<?
defined('C5_EXECUTE') or die("Access Denied.");

Loader::controller('/dashboard/base');
class DashboardStacksDetailController extends DashboardBaseController {

	
	public function on_start() {
		$c = Page::getByPath('/dashboard/stacks');
		$cp = new Permissions($c);
		if ($cp->canRead()) {
			$c = Page::getCurrentPage();
			$pcp = new Permissions($c);
			if ((!$pcp->canReadVersions()) || ($_GET['vtask'] != 'view_versions' && $_GET['vtask'] != 'compare')) {
				$cID = $c->getCollectionID();
				$this->redirect('/dashboard/stacks','view_details', $cID);		
			}
		} else {
			$v = View::getInstance();
			$v->render('/page_not_found');
		}
	}		
	

}