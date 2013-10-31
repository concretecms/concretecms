<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Page_Dashboard_Sitemap_Explore extends DashboardController {

	public function view($nodeID = 1, $auxMessage = false) {
		$v = View::getInstance();
		$v->requireAsset('core/sitemap');

		$dh = Loader::helper('concrete/dashboard/sitemap');
		if ($dh->canRead()) { 
			$this->set('nodeID', $nodeID);			
		}
		
		if (isset($_REQUEST['task']) && isset($_REQUEST['cNodeID'])) {
			$nc = Page::getByID($_REQUEST['cNodeID']);
			if ($_REQUEST['task'] == 'send_to_top') {
				$nc->movePageDisplayOrderToTop();
			} else if ($_REQUEST['task'] == 'send_to_bottom') {
				$nc->movePageDisplayOrderToBottom();
			}
			$this->redirect('/dashboard/sitemap/explore', $nc->getCollectionParentID(), 'order_updated');
		}
		
		if ($auxMessage != false) {
			switch($auxMessage) {
				case 'order_updated':
					$this->set('message', t('Sort order saved'));	
					break;
			}
		}
		$this->set('dh', $dh);
	}
	
	
}