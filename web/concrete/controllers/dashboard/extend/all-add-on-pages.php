<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardExtendAllAddOnPagesController extends DashboardBaseController {

	public function view() {
		$pages = array();
		$dashboard = Page::getByPath('/dashboard');
		$children = $dashboard->getCollectionChildrenArray(true);
		foreach($children as $cID) {
			$nc = Page::getByID($cID, 'ACTIVE');
			if ($nc->getPackageID() > 0) { 
				$ncp = new Permissions($nc);
				if ($ncp->canRead()) {
					$pages[] = $nc;	
				}
			}
		}
		$this->set('pages', $pages);
	}
}