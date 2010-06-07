<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardSitemapExploreController extends Controller {

	public function view($nodeID = 1) {
		$dh = Loader::helper('concrete/dashboard/sitemap');
		if ($dh->canRead()) { 
			$this->set('nodeID', $nodeID);
			$this->addHeaderItem(Loader::helper('html')->css('ccm.sitemap.css'));
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.sitemap.js'));
			
			$nodes = $dh->getSubNodes($nodeID, 1, false, false);
			$instanceID = time();
			$this->set('listHTML', $dh->outputRequestHTML($instanceID, 'explore', false, $nodes));
			$this->set('instanceID', $instanceID);
		}
		$this->set('dh', $dh);
	}
	
	
}

?>