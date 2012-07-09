<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Sitemap_Explore extends Controller {

	public function view($nodeID = 1, $auxMessage = false) {
		$dh = Loader::helper('concrete/dashboard/sitemap');
		if ($dh->canRead()) { 
			$this->set('nodeID', $nodeID);			
			$nodes = $dh->getSubNodes($nodeID, 1, false, false);
			$instanceID = time();
			$this->set('listHTML', $dh->outputRequestHTML($instanceID, 'explore', false, $nodes));
			$this->set('instanceID', $instanceID);
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

?>