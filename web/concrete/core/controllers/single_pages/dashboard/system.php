<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_System extends DashboardBaseController {

	public $helpers = array('form'); 
	
	public function view() {
		$this->enableNativeMobile();
		$categories = array();
		$c = Page::getCurrentPage();
		$children = $c->getCollectionChildrenArray(true);
		foreach($children as $cID) {
			$nc = Page::getByID($cID, 'ACTIVE');
			$ncp = new Permissions($nc);
			if ($ncp->canRead()) {
				$categories[] = $nc;	
			}
		}
		$this->set('categories', $categories);
	}

}